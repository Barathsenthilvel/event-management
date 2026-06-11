@extends('admin.layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    .dashboard-enterprise {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .dashboard-enterprise .dash-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-enterprise .dash-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
    }

    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.96); }
    }

    .dashboard-enterprise .animate-pulse-subtle {
        animation: pulse-subtle 3s infinite ease-in-out;
    }
</style>
@endpush

@section('content')
@php
    $defaultChartYear = (string) ($chartYears[0] ?? date('Y'));
    $upcomingSchedule = $upcomingMeeting?->schedules?->first();
    $nominationIsLive = $featuredNomination && $featuredNomination->status === 'active' && $featuredNomination->is_active;
    $pollingIsLive = $featuredPolling && $featuredPolling->polling_status === 'live';
@endphp
<div class="dashboard-enterprise custom-scroll flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 text-slate-800 antialiased space-y-6">

    {{-- Flashing Live Alerts --}}
    <div class="dash-card rounded-2xl p-4 border-rose-100 bg-gradient-to-r from-rose-50/80 via-white to-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-3 shrink-0">
            <div class="bg-rose-50 p-2 rounded-lg border border-rose-100">
                <i class="fa-solid fa-satellite-dish text-rose-500 text-lg animate-pulse-subtle"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold uppercase tracking-widest text-rose-600">Flashing Live Alerts</h2>
                <p class="text-[11px] text-slate-500">Real-time election & validation channels</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 w-full">
            @if($featuredNomination)
            <a href="{{ route('admin.nominations.index') }}" class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex justify-between items-center transition hover:border-emerald-200 hover:bg-emerald-50/30">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative flex h-2 w-2 shrink-0">
                        @if($nominationIsLive)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        @else
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-400"></span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Nomination Portal</span>
                        <span class="text-xs font-semibold text-slate-800 truncate block">{{ $featuredNomination->title }}</span>
                    </div>
                </div>
                <span class="shrink-0 ml-2 {{ $nominationIsLive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }} border px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                    {{ $nominationIsLive ? 'Live' : 'Ended' }}
                </span>
            </a>
            @else
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-2 w-2">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-300"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Nomination Portal</span>
                        <span class="text-xs font-semibold text-slate-500">No nominations yet</span>
                    </div>
                </div>
            </div>
            @endif

            @if($featuredPolling)
            <a href="{{ route('admin.pollings.index') }}" class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex justify-between items-center transition {{ $pollingIsLive ? 'hover:border-emerald-200 hover:bg-emerald-50/30' : 'hover:border-slate-200' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative flex h-2 w-2 shrink-0">
                        @if($pollingIsLive)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        @else
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-400"></span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Polling Module</span>
                        <span class="text-xs font-semibold text-slate-700 truncate block">{{ $featuredPolling->title }}</span>
                    </div>
                </div>
                <span class="shrink-0 ml-2 {{ $pollingIsLive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }} border px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                    {{ $pollingIsLive ? 'Live' : 'Ended' }}
                </span>
            </a>
            @else
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-2 w-2">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-300"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Polling Module</span>
                        <span class="text-xs font-semibold text-slate-500">No pollings yet</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Metric cards & upcoming meeting --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="dash-card rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-100/60 rounded-full blur-xl"></div>
            <div class="flex justify-between items-start relative">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Member Count</p>
                    <h3 class="text-3xl font-bold tracking-tight mt-2 text-slate-900">{{ number_format($activeMemberCount) }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs relative">
                @if($memberGrowthPercent !== null)
                <span class="text-emerald-700 font-medium bg-emerald-50 px-2 py-0.5 rounded flex items-center gap-1 border border-emerald-100">
                    <i class="fa-solid fa-arrow-trend-{{ $memberGrowthPercent >= 0 ? 'up' : 'down' }} text-[10px]"></i>
                    {{ $memberGrowthPercent >= 0 ? '+' : '' }}{{ $memberGrowthPercent }}%
                </span>
                @else
                <span class="text-slate-500 font-medium">No prior baseline</span>
                @endif
                <span class="text-slate-500">{{ number_format($totalMemberCount) }} total registered</span>
            </div>
        </div>

        <div class="dash-card rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-100/60 rounded-full blur-xl"></div>
            <div class="flex justify-between items-start relative">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Member Approval Count</p>
                    <h3 class="text-3xl font-bold tracking-tight mt-2 text-amber-600">{{ number_format($pendingApprovalCount) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100">
                    <i class="fa-solid fa-user-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs relative">
                <span class="text-amber-700 font-medium">Pending Review</span>
                <a href="{{ route('admin.members.pending-approvals.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition flex items-center gap-1">
                    Go to Queue <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <div class="dash-card rounded-2xl p-6 md:col-span-2 lg:col-span-1 flex flex-col justify-between group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Upcoming Meeting</p>
                    @if($upcomingMeeting)
                    <a href="{{ route('admin.meetings.index') }}" class="block">
                        <h4 class="text-sm font-semibold text-slate-900 mt-2 group-hover:text-indigo-600 transition">{{ $upcomingMeeting->title }}</h4>
                    </a>
                    @else
                    <h4 class="text-sm font-semibold text-slate-500 mt-2">No upcoming meetings</h4>
                    @endif
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                    <i class="fa-regular fa-calendar-check text-xl"></i>
                </div>
            </div>
            @if($upcomingMeeting && $upcomingSchedule)
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-indigo-500"></i>
                    <span>
                        {{ $upcomingSchedule->meeting_date?->format('M j, Y') }}
                        @if($upcomingSchedule->from_time)
                        • {{ \Carbon\Carbon::parse($upcomingSchedule->from_time)->format('H:i') }}
                        @endif
                    </span>
                </div>
                <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-slate-700 font-medium">{{ $upcomingMeeting->meeting_mode ?? 'TBD' }}</span>
            </div>
            @else
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400">
                Schedule a meeting from the meetings module.
            </div>
            @endif
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="dash-card rounded-2xl p-5 flex flex-col h-[380px]">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Monthly Donation Payments</h3>
                    <p class="text-[11px] text-slate-500">Voluntary contributions and specific welfare support programs</p>
                </div>
                <div class="relative">
                    <select onchange="updateDonationData(this.value)" class="appearance-none bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-8 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium cursor-pointer">
                        @foreach($chartYears as $year)
                        <option value="{{ $year }}" {{ (string) $year === $defaultChartYear ? 'selected' : '' }}>FY {{ $year }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 absolute right-3 top-3 pointer-events-none"></i>
                </div>
            </div>
            <div class="flex-1 relative min-h-0">
                <canvas id="donationChartCanvas"></canvas>
            </div>
        </div>

        <div class="dash-card rounded-2xl p-5 flex flex-col h-[380px]">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Monthly Subscription Payments</h3>
                    <p class="text-[11px] text-slate-500">Standard union structured dues automated payments</p>
                </div>
                <div class="relative">
                    <select onchange="updateSubscriptionData(this.value)" class="appearance-none bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-8 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 font-medium cursor-pointer">
                        @foreach($chartYears as $year)
                        <option value="{{ $year }}" {{ (string) $year === $defaultChartYear ? 'selected' : '' }}>FY {{ $year }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 absolute right-3 top-3 pointer-events-none"></i>
                </div>
            </div>
            <div class="flex-1 relative min-h-0">
                <canvas id="subscriptionChartCanvas"></canvas>
            </div>
        </div>
    </div>

    {{-- Renewal list & recent payments --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="dash-card rounded-2xl p-5 xl:col-span-2 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Members Renewal List</h3>
                    <p class="text-[11px] text-slate-500 font-normal">Subscription lifecycles approaching breach thresholds</p>
                </div>
                <span class="text-[11px] font-semibold bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg text-slate-600">
                    {{ number_format($renewalFlaggedCount) }} Flagged Record{{ $renewalFlaggedCount === 1 ? '' : 's' }}
                </span>
            </div>

            <div class="overflow-x-auto custom-scroll flex-1">
                @if($renewalMembers->isEmpty())
                <div class="py-10 text-center text-xs text-slate-500">
                    No members approaching renewal threshold.
                </div>
                @else
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold bg-slate-50/80">
                            <th class="py-3 px-3 rounded-l-lg">Name / Details</th>
                            <th class="py-3 px-3">Member ID</th>
                            <th class="py-3 px-3">Expires On</th>
                            <th class="py-3 px-3 text-right rounded-r-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($renewalMembers as $row)
                        @php
                            $member = $row['user'];
                            $initials = strtoupper(substr($member->name ?? 'ME', 0, 2));
                            $detailParts = array_filter([
                                $member->designation?->name,
                                $member->state,
                            ]);
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full {{ $row['is_expired'] ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100' }} flex items-center justify-center font-bold text-[11px] border">{{ $initials }}</div>
                                    <div>
                                        <p class="font-semibold text-slate-800 group-hover:text-indigo-600 transition">{{ $member->name }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $detailParts ? implode(' • ', $detailParts) : '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-slate-600">{{ $member->id }}</td>
                            <td class="py-3.5 px-3">
                                <span class="{{ $row['is_expired'] ? 'text-rose-700 bg-rose-50 border-rose-200' : 'text-amber-700 bg-amber-50 border-amber-200' }} font-medium border px-2 py-0.5 rounded-md">{{ $row['expiry_label'] }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <button type="button"
                                    data-notify-url="{{ route('admin.dashboard.renewals.notify', $row['subscription']) }}"
                                    onclick="triggerNotification(this)"
                                    class="cursor-pointer inline-flex items-center gap-1 {{ $row['is_expired'] ? 'bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }} font-medium px-3 py-1.5 rounded-lg text-[11px] shadow-sm active:scale-95 transition-all">
                                    <i class="fa-regular fa-paper-plane text-[10px]"></i>
                                    {{ $row['is_expired'] ? 'Send Urgent Alert' : 'Send Notification' }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="dash-card rounded-2xl p-5 flex flex-col justify-between">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Recent Payments</h3>
                    <p class="text-[11px] text-slate-500">Real-time ledger entries pipeline</p>
                </div>
                <i class="fa-solid fa-receipt text-slate-400 text-sm"></i>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto custom-scroll max-h-[220px] pr-1">
                @forelse($recentPayments as $payment)
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                    <div class="flex items-center space-x-3 min-w-0">
                        <span class="shrink-0 {{ $payment['type'] === 'new' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($payment['type'] === 'donation' ? 'bg-violet-50 text-violet-700 border-violet-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200') }} border text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">{{ $payment['type_label'] }}</span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $payment['name'] }}</p>
                            <span class="text-[10px] text-slate-500 truncate block">{{ $payment['description'] }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <span class="text-xs font-bold text-emerald-600">+{{ $payment['currency'] }} {{ number_format($payment['amount'], 2) }}</span>
                        <p class="text-[9px] text-slate-500">{{ $payment['paid_at']?->diffForHumans() ?? '—' }}</p>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500">
                    No successful payments recorded yet.
                </div>
                @endforelse
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('admin.subscriptions.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                    View All Subscription Transactions
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-[11px] text-slate-400 pb-2">
        &copy; {{ date('Y') }} Ghana National Association of Teachers (GNAT). Systems & Operations Audit Control Registry.
    </p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const defaultChartYear = @json($defaultChartYear);

    const donationDataset = @json($donationChartByYear);
    const subscriptionDataset = @json($subscriptionChartByYear);

    const chartGlobalStyles = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } },
            y: { grid: { color: 'rgba(226, 232, 240, 0.9)' }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } }
        }
    };

    const donationCtx = document.getElementById('donationChartCanvas').getContext('2d');
    const donationGrad = donationCtx.createLinearGradient(0, 0, 0, 300);
    donationGrad.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
    donationGrad.addColorStop(1, 'rgba(16, 185, 129, 0.15)');

    const donationChartInstance = new Chart(donationCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                data: donationDataset[defaultChartYear] || Array(12).fill(0),
                backgroundColor: donationGrad,
                borderRadius: 6,
                barThickness: 14
            }]
        },
        options: chartGlobalStyles
    });

    const subscriptionCtx = document.getElementById('subscriptionChartCanvas').getContext('2d');
    const subscriptionGrad = subscriptionCtx.createLinearGradient(0, 0, 0, 300);
    subscriptionGrad.addColorStop(0, 'rgba(99, 102, 241, 0.9)');
    subscriptionGrad.addColorStop(1, 'rgba(99, 102, 241, 0.15)');

    const subscriptionChartInstance = new Chart(subscriptionCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                data: subscriptionDataset[defaultChartYear] || Array(12).fill(0),
                backgroundColor: subscriptionGrad,
                borderRadius: 6,
                barThickness: 14
            }]
        },
        options: chartGlobalStyles
    });

    function updateDonationData(selectedYear) {
        donationChartInstance.data.datasets[0].data = donationDataset[selectedYear] || Array(12).fill(0);
        donationChartInstance.update();
    }

    function updateSubscriptionData(selectedYear) {
        subscriptionChartInstance.data.datasets[0].data = subscriptionDataset[selectedYear] || Array(12).fill(0);
        subscriptionChartInstance.update();
    }

    function triggerNotification(buttonElement) {
        const notifyUrl = buttonElement.dataset.notifyUrl;
        if (!notifyUrl) {
            return;
        }

        const structuralBackup = buttonElement.innerHTML;
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Dispatched...';
        buttonElement.classList.add('opacity-60');

        fetch(notifyUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                throw new Error(data.message || 'Failed to send notification.');
            }
            buttonElement.innerHTML = '<i class="fa-solid fa-check-double"></i> Alerted!';
            buttonElement.classList.remove('bg-indigo-600', 'bg-rose-50', 'text-rose-700', 'hover:bg-rose-600', 'hover:text-white');
            buttonElement.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
        })
        .catch(() => {
            buttonElement.innerHTML = '<i class="fa-solid fa-xmark"></i> Failed';
            buttonElement.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
        })
        .finally(() => {
            setTimeout(() => {
                buttonElement.innerHTML = structuralBackup;
                buttonElement.disabled = false;
                buttonElement.classList.remove('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200', 'opacity-60', 'bg-rose-50', 'text-rose-700', 'border-rose-200');
            }, 2000);
        });
    }
</script>
@endpush
