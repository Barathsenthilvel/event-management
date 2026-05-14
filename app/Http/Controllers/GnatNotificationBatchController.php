<?php

namespace App\Http\Controllers;

use App\Jobs\SendGnatBulkNotificationChunkJob;
use App\Models\GnatNotificationBatch;
use Illuminate\Http\Request;

class GnatNotificationBatchController extends Controller
{
    /** @var array<string, string> */
    private const TYPE_LABELS = [
        SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITES => 'Event invites',
        SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITE_REMINDERS => 'Event invite reminders',
        SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITES => 'Meeting invites',
        SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITE_REMINDERS => 'Meeting invite reminders',
        SendGnatBulkNotificationChunkJob::TYPE_NOMINATION_ALERTS => 'Nomination alerts',
        SendGnatBulkNotificationChunkJob::TYPE_JOB_POSTING_ALERTS => 'Job posting alerts',
        SendGnatBulkNotificationChunkJob::TYPE_POLLING_LIVE_ALERTS => 'Polling live alerts',
        SendGnatBulkNotificationChunkJob::TYPE_POLLING_RESULTS_ALERTS => 'Polling results alerts',
    ];

    public static function typeLabel(string $type): string
    {
        return self::TYPE_LABELS[$type] ?? $type;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $batches = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('entity_label', 'like', '%'.$q.'%')
                        ->orWhere('notification_type', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.notification-batches.index', [
            'batches' => $batches,
            'q' => $q,
        ]);
    }

    public function show(int $id)
    {
        $batch = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->findOrFail($id);

        $logs = $batch->deliveryLogs()
            ->with('user:id,name,email,mobile')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin.notification-batches.show', [
            'batch' => $batch,
            'logs' => $logs,
        ]);
    }
}
