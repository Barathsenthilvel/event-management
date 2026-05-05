<?php

namespace App\Http\Controllers;

use App\Models\AdminJob;
use App\Models\AdminJobAlert;
use App\Models\AdminJobApplication;
use App\Models\Designation;
use App\Models\Hospital;
use App\Models\MemberJobRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminJobController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $hospital = trim((string) $request->query('hospital', ''));

        $jobs = AdminJob::query()
            ->with('creator:id,name')
            ->withCount('applications')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('hospital', 'like', '%'.$q.'%')
                        ->orWhere('title', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%');
                });
            })
            ->when($hospital !== '', fn ($query) => $query->where('hospital', $hospital))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $hospitalSuggestions = $this->hospitalSuggestions();
        $needJobRequestsCount = MemberJobRequest::query()->count();

        return view('admin.jobs.index', compact('jobs', 'q', 'hospital', 'hospitalSuggestions', 'needJobRequestsCount'));
    }

    public function create()
    {
        return view('admin.jobs.create', [
            'hospitalSuggestions' => $this->hospitalSuggestions(),
            'hospitalDirectory' => $this->hospitalDirectory(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $this->validateChoiceGroups($request);
        $payload = $this->buildPayload($request, $validated, true);
        AdminJob::create($payload);

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    public function edit(AdminJob $job)
    {
        return view('admin.jobs.edit', [
            'job' => $job,
            'hospitalSuggestions' => $this->hospitalSuggestions(),
            'hospitalDirectory' => $this->hospitalDirectory(),
        ]);
    }

    public function storeHospital(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'logo' => 'nullable|image|max:2048',
        ]);

        $name = trim((string) $validated['name']);
        $existing = Hospital::query()->where('name', $name)->first();

        $data = [
            'address' => trim((string) $validated['address']),
            'is_active' => true,
            'created_by_admin_id' => Auth::guard('admin')->id(),
        ];

        if ($request->hasFile('logo')) {
            if ($existing && ! empty($existing->logo_path)) {
                Storage::disk('public')->delete($existing->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('hospitals/logos', 'public');
        }

        $hospital = Hospital::query()->updateOrCreate(
            ['name' => $name],
            $data
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hospital added successfully.',
                'hospital' => [
                    'id' => $hospital->id,
                    'name' => $hospital->name,
                    'address' => $hospital->address,
                    'logo_path' => $hospital->logo_path,
                    'logo_url' => $hospital->logo_path ? asset('storage/'.$hospital->logo_path) : null,
                ],
            ]);
        }

        return back()->with('success', 'Hospital added successfully.');
    }

    public function update(Request $request, AdminJob $job)
    {
        $validated = $request->validate($this->rules($job->id));
        $this->validateChoiceGroups($request);
        $payload = $this->buildPayload($request, $validated, false);
        $job->update($payload);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(AdminJob $job)
    {
        if (! empty($job->hospital_logo_path)) {
            Storage::disk('public')->delete($job->hospital_logo_path);
        }
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function togglePromote(AdminJob $job)
    {
        $job->update(['promote_front' => ! $job->promote_front]);

        return back()->with('success', 'Promote front updated.');
    }

    public function toggleStatus(AdminJob $job)
    {
        $job->update(['is_active' => ! $job->is_active]);

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
        $designationId = (int) $request->query('designation_id', 0);
        $leadersOnly = $request->boolean('leaders_only');

        $members = User::query()
            ->where('is_approved', true)
            ->when($designationId > 0, fn ($query) => $query->where('designation_id', $designationId))
            ->when($leadersOnly, function ($query) {
                $query->whereHas('designation', fn ($d) => $d->where('name', 'like', '%leader%'));
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('mobile', 'like', '%'.$q.'%');
                });
            })
            ->with('designation:id,name')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $alertedIds = AdminJobAlert::query()->where('job_id', $job->id)->pluck('user_id')->all();
        $designations = Designation::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);

        return view('admin.jobs.alert', compact('job', 'members', 'alertedIds', 'q', 'designationId', 'designations', 'leadersOnly'));
    }

    public function alertStore(AdminJob $job, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,specific,leaders_only',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'notify_whatsapp' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
            'notify_all' => 'nullable|boolean',
        ]);

        $notifyAll = $request->boolean('notify_all');
        $notifyWhatsApp = $request->boolean('notify_whatsapp');
        $notifySms = $request->boolean('notify_sms');
        $notifyEmail = $request->boolean('notify_email');
        if ($notifyAll) {
            $notifyWhatsApp = true;
            $notifySms = true;
            $notifyEmail = true;
        }

        if (! $notifyWhatsApp && ! $notifySms && ! $notifyEmail) {
            return back()->withErrors(['notify_channel' => 'Select at least one notification channel.'])->withInput();
        }

        $memberIds = match ($validated['target']) {
            'all' => User::query()->where('is_approved', true)->pluck('id')->all(),
            'leaders_only' => User::query()
                ->where('is_approved', true)
                ->whereHas('designation', fn ($d) => $d->where('name', 'like', '%leader%'))
                ->pluck('id')
                ->all(),
            default => array_values(array_unique($validated['member_ids'] ?? [])),
        };

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
                    $userQ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('mobile', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jobs.applications', compact('job', 'applications', 'q'));
    }

    public function downloadReport(AdminJob $job): StreamedResponse
    {
        $applications = AdminJobApplication::query()
            ->with('user:id,name,email,mobile')
            ->where('job_id', $job->id)
            ->orderBy('id')
            ->get();

        $filename = 'job-report-'.$job->id.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($applications, $job) {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Job', $job->title]);
            fputcsv($stream, ['Job Code', $job->code]);
            fputcsv($stream, ['No. Of Openings', $job->no_of_openings]);
            fputcsv($stream, []);
            fputcsv($stream, ['Member Name', 'Email', 'Mobile', 'Status', 'Submitted At', 'Status Mail Triggered At']);

            foreach ($applications as $application) {
                fputcsv($stream, [
                    $application->user->name ?? '',
                    $application->user->email ?? '',
                    $application->user->mobile ?? '',
                    $application->application_status,
                    optional($application->submitted_at)->format('Y-m-d H:i:s'),
                    optional($application->status_emailed_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
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

    public function needJobRequests(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $rows = MemberJobRequest::query()
            ->with('user:id,name,email,mobile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('mobile', 'like', '%'.$q.'%')
                        ->orWhere('position_looking_for', 'like', '%'.$q.'%');
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.need-job-requests', [
            'rows' => $rows,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function downloadNeedJobRequestsReport(Request $request): StreamedResponse
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $rows = MemberJobRequest::query()
            ->with('user:id,name,email,mobile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('mobile', 'like', '%'.$q.'%')
                        ->orWhere('position_looking_for', 'like', '%'.$q.'%');
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->get();

        $filename = 'need-job-requests-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $stream = fopen('php://output', 'w');
            if ($stream === false) {
                return;
            }
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, [
                'ID',
                'Name',
                'Account member',
                'Email',
                'Mobile',
                'Qualification',
                'Position looking for',
                'Experience',
                'Details',
                'Resume file',
                'Status',
                'Submitted at',
            ]);

            foreach ($rows as $row) {
                $resumeCell = $row->resume_path
                    ? (string) $row->resume_path
                    : '';
                fputcsv($stream, [
                    $row->id,
                    $row->name ?? '',
                    $row->user->name ?? '',
                    $row->email ?? '',
                    $row->mobile ?? '',
                    $row->qualification ?? '',
                    $row->position_looking_for ?? '',
                    $row->experience ?? '',
                    $row->details ?? '',
                    $resumeCell,
                    $row->status ?? '',
                    optional($row->created_at)->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function updateNeedJobRequestStatus(MemberJobRequest $requestRow, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewed,contacted,closed',
        ]);

        $requestRow->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Need Job request status updated.');
    }

    private function rules(?int $jobId = null): array
    {
        return [
            'hospital' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:admin_jobs,code,'.($jobId ?? 'NULL').',id',
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

    private function hospitalSuggestions(): array
    {
        $fromDirectory = Hospital::query()
            ->where('is_active', true)
            ->pluck('name');

        $fromJobs = AdminJob::query()
            ->whereNotNull('hospital')
            ->where('hospital', '!=', '')
            ->distinct()
            ->pluck('hospital');

        return $fromDirectory
            ->merge($fromJobs)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function hospitalDirectory()
    {
        return Hospital::query()
            ->where('is_active', true)
            ->latest('id')
            ->limit(12)
            ->get(['id', 'name', 'address']);
    }

    private function validateChoiceGroups(Request $request): void
    {
        if (! $request->boolean('vacancy_permanent') && ! $request->boolean('vacancy_temporary') && ! $request->boolean('vacancy_any')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vacancy_type' => 'Select at least one vacancy type.',
            ]);
        }

        if (! $request->boolean('preference_wfh') && ! $request->boolean('preference_onsite') && ! $request->boolean('preference_any')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'preference' => 'Select at least one preference type.',
            ]);
        }
    }
}
