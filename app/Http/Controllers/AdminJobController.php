<?php

namespace App\Http\Controllers;

use App\Models\AdminJob;
use App\Models\AdminJobAlert;
use App\Models\AdminJobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminJobController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $jobs = AdminJob::query()
            ->with('creator:id,name')
            ->withCount('applications')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('hospital', 'like', '%' . $q . '%')
                        ->orWhere('title', 'like', '%' . $q . '%')
                        ->orWhere('code', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs', 'q'));
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        AdminJob::create($this->buildPayload($request, $validated, true));
        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    public function edit(AdminJob $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, AdminJob $job)
    {
        $validated = $request->validate($this->rules($job->id));
        $job->update($this->buildPayload($request, $validated, false));
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(AdminJob $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function togglePromote(AdminJob $job)
    {
        $job->update(['promote_front' => !$job->promote_front]);
        return back()->with('success', 'Promote front updated.');
    }

    public function toggleStatus(AdminJob $job)
    {
        $job->update(['is_active' => !$job->is_active]);
        return back()->with('success', 'Display status updated.');
    }

    public function toggleListing(AdminJob $job)
    {
        $job->update(['listing_status' => $job->listing_status === 'listed' ? 'unlisted' : 'listed']);
        return back()->with('success', 'Listing status updated.');
    }

    public function alertForm(AdminJob $job, Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $members = User::query()
            ->where('is_approved', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $alertedIds = AdminJobAlert::query()->where('job_id', $job->id)->pluck('user_id')->all();

        return view('admin.jobs.alert', compact('job', 'members', 'alertedIds', 'q'));
    }

    public function alertStore(AdminJob $job, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,specific',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'notify_whatsapp' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
        ]);

        $notifyWhatsApp = $request->boolean('notify_whatsapp');
        $notifySms = $request->boolean('notify_sms');
        $notifyEmail = $request->boolean('notify_email');

        if (!$notifyWhatsApp && !$notifySms && !$notifyEmail) {
            return back()->withErrors(['notify_channel' => 'Select at least one notification channel.'])->withInput();
        }

        $memberIds = $validated['target'] === 'all'
            ? User::query()->where('is_approved', true)->pluck('id')->all()
            : array_values(array_unique($validated['member_ids'] ?? []));

        if (empty($memberIds)) {
            return back()->withErrors(['member_ids' => 'Please select at least one member.'])->withInput();
        }

        DB::transaction(function () use ($memberIds, $job, $notifyWhatsApp, $notifySms, $notifyEmail) {
            foreach ($memberIds as $userId) {
                AdminJobAlert::updateOrCreate(
                    ['job_id' => $job->id, 'user_id' => $userId],
                    [
                        'notify_whatsapp' => $notifyWhatsApp,
                        'notify_sms' => $notifySms,
                        'notify_email' => $notifyEmail,
                        'alert_sent_at' => now(),
                    ]
                );

                AdminJobApplication::firstOrCreate(
                    ['job_id' => $job->id, 'user_id' => $userId],
                    ['submitted_at' => now(), 'application_status' => 'pending']
                );
            }
        });

        return redirect()->route('admin.jobs.applications', $job->id)->with('success', 'Job alert sent successfully.');
    }

    public function applications(AdminJob $job, Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $applications = AdminJobApplication::query()
            ->with('user:id,name,email,mobile,educational_certificate_path')
            ->where('job_id', $job->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('user', function ($userQ) use ($q) {
                    $userQ->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.applications', compact('job', 'applications', 'q'));
    }

    public function updateApplicationStatus(AdminJob $job, AdminJobApplication $application, Request $request)
    {
        if ($application->job_id !== $job->id) {
            abort(404);
        }

        $validated = $request->validate([
            'application_status' => 'required|in:pending,selected,not_selected,joined,not_joined',
        ]);

        $application->update([
            'application_status' => $validated['application_status'],
            'status_emailed_at' => now(), // mark mail trigger point
        ]);

        return back()->with('success', 'Application status updated and mail trigger marked.');
    }

    private function rules(?int $jobId = null): array
    {
        return [
            'hospital' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:admin_jobs,code,' . ($jobId ?? 'NULL') . ',id',
            'no_of_openings' => 'required|integer|min:0',
            'vacancy_permanent' => 'nullable|boolean',
            'vacancy_temporary' => 'nullable|boolean',
            'vacancy_any' => 'nullable|boolean',
            'preference_wfh' => 'nullable|boolean',
            'preference_onsite' => 'nullable|boolean',
            'preference_any' => 'nullable|boolean',
            'description' => 'nullable|string',
            'key_skills' => 'nullable|string',
            'promote_front' => 'nullable|boolean',
            'listing_status' => 'required|in:listed,unlisted',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'hospital' => $validated['hospital'] ?? null,
            'title' => $validated['title'],
            'code' => $validated['code'],
            'no_of_openings' => (int) $validated['no_of_openings'],
            'vacancy_permanent' => $request->boolean('vacancy_permanent'),
            'vacancy_temporary' => $request->boolean('vacancy_temporary'),
            'vacancy_any' => $request->boolean('vacancy_any'),
            'preference_wfh' => $request->boolean('preference_wfh'),
            'preference_onsite' => $request->boolean('preference_onsite'),
            'preference_any' => $request->boolean('preference_any'),
            'description' => $validated['description'] ?? null,
            'key_skills' => $validated['key_skills'] ?? null,
            'promote_front' => $request->boolean('promote_front'),
            'listing_status' => $validated['listing_status'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        return $payload;
    }
}

