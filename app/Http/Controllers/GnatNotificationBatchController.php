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
        $status = trim((string) $request->query('status', ''));

        $batches = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
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
            'status' => $status,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $status = trim((string) $request->query('status', ''));

        $batch = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->findOrFail($id);

        $logsQuery = $batch->deliveryLogs()
            ->with('user:id,name,email,mobile');

        if ($status === 'failed') {
            $logsQuery->where(function ($q) {
                $q->where('email_status', 'failed')
                    ->orWhere('sms_status', 'failed')
                    ->orWhere('whatsapp_status', 'failed');
            });
        } elseif ($status !== '') {
            $logsQuery->where(function ($q) use ($status) {
                $q->where('email_status', $status)
                    ->orWhere('sms_status', $status)
                    ->orWhere('whatsapp_status', $status);
            });
        }

        $logs = $logsQuery->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin.notification-batches.show', [
            'batch' => $batch,
            'logs' => $logs,
            'statusFilter' => $status,
        ]);
    }
}
