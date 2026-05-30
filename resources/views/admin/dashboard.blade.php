@extends('admin.layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    .dashboard-enterprise {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dashboard-enterprise .glass-panel {
        background: rgba(17, 24, 39, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.96); }
    }

    .dashboard-enterprise .animate-pulse-subtle {
        animation: pulse-subtle 3s infinite ease-in-out;
    }

    .dashboard-enterprise.custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
    }

    .dashboard-enterprise.custom-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .dashboard-enterprise.custom-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }
</style>
@endpush

@section('content')
<div class="dashboard-enterprise custom-scroll flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 text-slate-100 antialiased space-y-6">

    {{-- Flashing Live Alerts --}}
    <div class="glass-panel rounded-2xl p-4 shadow-xl border border-rose-500/20 bg-gradient-to-r from-rose-950/10 via-slate-900/60 to-slate-900/60 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-3 shrink-0">
            <div class="bg-rose-500/10 p-2 rounded-lg border border-rose-500/30">
                <i class="fa-solid fa-satellite-dish text-rose-400 text-lg animate-pulse-subtle"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold uppercase tracking-widest text-rose-400">Flashing Live Alerts</h2>
                <p class="text-[11px] text-slate-400">Real-time election & validation channels</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 w-full">
            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800 flex justify-between items-center transition hover:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Nomination Portal</span>
                        <span class="text-xs font-semibold text-slate-200">2026 National Executive Council Call</span>
                    </div>
                </div>
                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm">
                    Live
                </span>
            </div>

            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800 flex justify-between items-center transition hover:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-2 w-2">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Polling Module</span>
                        <span class="text-xs font-semibold text-slate-300">Welfare Fund Contribution Amendment</span>
                    </div>
                </div>
                <span class="bg-slate-800 text-slate-400 border border-slate-700 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm">
                    Ended
                </span>
            </div>
        </div>
    </div>

    {{-- Metric cards & upcoming meeting --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass-panel rounded-2xl p-6 shadow-lg flex flex-col justify-between relative overflow-hidden group hover:border-slate-700 transition duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition duration-300"></div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Member Count</p>
                    <h3 class="text-3xl font-bold tracking-tight mt-2 text-white">142,840</h3>
                </div>
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20 shadow-inner">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-emerald-400 font-medium bg-emerald-500/5 px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> +1.8%
                </span>
                <span class="text-slate-500">vs historical baseline</span>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-6 shadow-lg flex flex-col justify-between relative overflow-hidden group hover:border-slate-700 transition duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition duration-300"></div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Member Approval Count</p>
                    <h3 class="text-3xl font-bold tracking-tight mt-2 text-amber-400">384</h3>
                </div>
                <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl border border-amber-500/20 shadow-inner">
                    <i class="fa-solid fa-user-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-amber-400 font-medium">Pending Review</span>
                <a href="{{ route('admin.members.pending-approvals.index') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition flex items-center gap-1">
                    Go to Queue <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-6 shadow-lg md:col-span-2 lg:col-span-1 flex flex-col justify-between relative overflow-hidden group hover:border-slate-700 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Upcoming Meeting</p>
                    <h4 class="text-sm font-semibold text-white mt-2 group-hover:text-indigo-400 transition">Regional Secretariat Executives Summit</h4>
                </div>
                <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl border border-indigo-500/20">
                    <i class="fa-regular fa-calendar-check text-xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-indigo-400"></i>
                    <span>May 28, 2026 • 09:30 GMT</span>
                </div>
                <span class="bg-slate-800 border border-slate-700 px-2 py-0.5 rounded text-slate-300 font-medium">HQ Hall A</span>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-panel rounded-2xl p-5 shadow-xl flex flex-col h-[380px] hover:border-slate-700/80 transition duration-300">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Monthly Donation Payments</h3>
                    <p class="text-[11px] text-slate-400">Voluntary contributions and specific welfare support programs</p>
                </div>
                <div class="relative">
                    <select onchange="updateDonationData(this.value)" class="appearance-none bg-slate-900 border border-slate-700 rounded-xl pl-3 pr-8 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 font-medium shadow-inner cursor-pointer">
                        <option value="2026">FY 2026</option>
                        <option value="2025">FY 2025</option>
                        <option value="2024">FY 2024</option>
                    </select>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 absolute right-3 top-3 pointer-events-none"></i>
                </div>
            </div>
            <div class="flex-1 relative min-h-0">
                <canvas id="donationChartCanvas"></canvas>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 shadow-xl flex flex-col h-[380px] hover:border-slate-700/80 transition duration-300">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Monthly Subscription Payments</h3>
                    <p class="text-[11px] text-slate-400">Standard union structured dues automated payments</p>
                </div>
                <div class="relative">
                    <select onchange="updateSubscriptionData(this.value)" class="appearance-none bg-slate-900 border border-slate-700 rounded-xl pl-3 pr-8 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-indigo-500 font-medium shadow-inner cursor-pointer">
                        <option value="2026">FY 2026</option>
                        <option value="2025">FY 2025</option>
                        <option value="2024">FY 2024</option>
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
        <div class="glass-panel rounded-2xl p-5 shadow-xl xl:col-span-2 flex flex-col hover:border-slate-700/80 transition duration-300">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Members Renewal List</h3>
                    <p class="text-[11px] text-slate-400 font-normal">Subscription lifecycles approaching breach thresholds</p>
                </div>
                <span class="text-[11px] font-semibold bg-slate-800 border border-slate-700 px-2.5 py-1 rounded-lg text-slate-400">
                    3 Flagged Records
                </span>
            </div>

            <div class="overflow-x-auto custom-scroll flex-1">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold bg-slate-900/30">
                            <th class="py-3 px-3 rounded-l-lg">Name / Details</th>
                            <th class="py-3 px-3">Member ID</th>
                            <th class="py-3 px-3">Expires On</th>
                            <th class="py-3 px-3 text-right rounded-r-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-[11px] border border-indigo-500/20">FO</div>
                                    <div>
                                        <p class="font-semibold text-slate-100 group-hover:text-indigo-400 transition">Francis Osei-Tutu</p>
                                        <p class="text-[10px] text-slate-500">Primary Division • Kumasi</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-slate-300">GNT-7740-2021</td>
                            <td class="py-3.5 px-3">
                                <span class="text-amber-400 font-medium bg-amber-400/5 border border-amber-400/20 px-2 py-0.5 rounded-md">In 4 Days</span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <button type="button" onclick="triggerNotification(this, 'Francis')" class="cursor-pointer inline-flex items-center gap-1 bg-indigo-500 hover:bg-indigo-600 text-white font-medium px-3 py-1.5 rounded-lg text-[11px] shadow-sm shadow-indigo-500/20 active:scale-95 transition-all">
                                    <i class="fa-regular fa-paper-plane text-[10px]"></i> Send Notification
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-[11px] border border-emerald-500/20">HA</div>
                                    <div>
                                        <p class="font-semibold text-slate-100 group-hover:text-emerald-400 transition">Harriet Mensah-Annan</p>
                                        <p class="text-[10px] text-slate-500">JHS Technical • Tamale</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-slate-300">GNT-0921-2018</td>
                            <td class="py-3.5 px-3">
                                <span class="text-amber-400 font-medium bg-amber-400/5 border border-amber-400/20 px-2 py-0.5 rounded-md">In 9 Days</span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <button type="button" onclick="triggerNotification(this, 'Harriet')" class="cursor-pointer inline-flex items-center gap-1 bg-indigo-500 hover:bg-indigo-600 text-white font-medium px-3 py-1.5 rounded-lg text-[11px] shadow-sm shadow-indigo-500/20 active:scale-95 transition-all">
                                    <i class="fa-regular fa-paper-plane text-[10px]"></i> Send Notification
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-[11px] border border-rose-500/20">EB</div>
                                    <div>
                                        <p class="font-semibold text-slate-100 group-hover:text-rose-400 transition">Ebenezer Boateng</p>
                                        <p class="text-[10px] text-slate-500">Senior Secondary • Cape Coast</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-slate-300">GNT-5512-2023</td>
                            <td class="py-3.5 px-3">
                                <span class="text-rose-400 font-medium bg-rose-400/5 border border-rose-400/20 px-2 py-0.5 rounded-md">Expired</span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <button type="button" onclick="triggerNotification(this, 'Ebenezer')" class="cursor-pointer inline-flex items-center gap-1 bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-medium px-3 py-1.5 rounded-lg text-[11px] shadow-sm active:scale-95 transition-all">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Send Urgent Alert
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 shadow-xl flex flex-col justify-between hover:border-slate-700/80 transition duration-300">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Recent Payments</h3>
                    <p class="text-[11px] text-slate-400">Real-time ledger entries pipeline</p>
                </div>
                <i class="fa-solid fa-receipt text-slate-500 text-sm"></i>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto custom-scroll max-h-[220px] pr-1">
                <div class="flex justify-between items-center bg-slate-900/40 p-3 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-inner">New</span>
                        <div>
                            <p class="text-xs font-semibold text-slate-200">Salifu Abdul Rahman</p>
                            <span class="text-[10px] text-slate-500">Core Setup & Activation</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-emerald-400">+₵150.00</span>
                        <p class="text-[9px] text-slate-500">2 mins ago</p>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-slate-900/40 p-3 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-inner">Renewal</span>
                        <div>
                            <p class="text-xs font-semibold text-slate-200">Comfort Mensah</p>
                            <span class="text-[10px] text-slate-500">Annual Sub Assessment</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-emerald-400">+₵360.00</span>
                        <p class="text-[9px] text-slate-500">14 mins ago</p>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-slate-900/40 p-3 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-inner">Renewal</span>
                        <div>
                            <p class="text-xs font-semibold text-slate-200">Patrick Gyamfi</p>
                            <span class="text-[10px] text-slate-500">Annual Sub Assessment</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-emerald-400">+₵360.00</span>
                        <p class="text-[9px] text-slate-500">1 hr ago</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-800/80 text-center">
                <a href="#" class="text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                    View All Consolidated Transactions
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-[11px] text-slate-500 pb-2">
        &copy; {{ date('Y') }} Ghana National Association of Teachers (GNAT). Systems & Operations Audit Control Registry.
    </p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const mockDonationDataset = {
        '2026': [14200, 19500, 24000, 31000, 28000, 0, 0, 0, 0, 0, 0, 0],
        '2025': [22000, 24000, 27500, 32000, 31000, 29000, 34000, 41000, 39000, 36000, 33200, 45000],
        '2024': [18000, 19500, 21000, 26000, 24500, 23000, 28000, 32000, 30500, 29000, 27000, 38000]
    };

    const mockSubscriptionDataset = {
        '2026': [89000, 92000, 94500, 110000, 108000, 0, 0, 0, 0, 0, 0, 0],
        '2025': [82000, 85000, 89000, 96000, 99000, 102000, 104000, 106000, 108000, 112000, 115000, 121000],
        '2024': [74000, 76000, 78500, 82000, 84000, 86500, 89000, 91000, 93000, 95000, 98000, 104000]
    };

    const chartGlobalStyles = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } },
            y: { grid: { color: 'rgba(51, 65, 85, 0.25)' }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } }
        }
    };

    const donationCtx = document.getElementById('donationChartCanvas').getContext('2d');
    const donationGrad = donationCtx.createLinearGradient(0, 0, 0, 300);
    donationGrad.addColorStop(0, 'rgba(16, 185, 129, 0.85)');
    donationGrad.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

    const donationChartInstance = new Chart(donationCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                data: mockDonationDataset['2026'],
                backgroundColor: donationGrad,
                borderRadius: 6,
                barThickness: 14
            }]
        },
        options: chartGlobalStyles
    });

    const subscriptionCtx = document.getElementById('subscriptionChartCanvas').getContext('2d');
    const subscriptionGrad = subscriptionCtx.createLinearGradient(0, 0, 0, 300);
    subscriptionGrad.addColorStop(0, 'rgba(99, 102, 241, 0.85)');
    subscriptionGrad.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

    const subscriptionChartInstance = new Chart(subscriptionCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                data: mockSubscriptionDataset['2026'],
                backgroundColor: subscriptionGrad,
                borderRadius: 6,
                barThickness: 14
            }]
        },
        options: chartGlobalStyles
    });

    function updateDonationData(selectedYear) {
        donationChartInstance.data.datasets[0].data = mockDonationDataset[selectedYear];
        donationChartInstance.update();
    }

    function updateSubscriptionData(selectedYear) {
        subscriptionChartInstance.data.datasets[0].data = mockSubscriptionDataset[selectedYear];
        subscriptionChartInstance.update();
    }

    function triggerNotification(buttonElement) {
        const structuralBackup = buttonElement.innerHTML;
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Dispatched...';
        buttonElement.classList.add('opacity-60');

        setTimeout(() => {
            buttonElement.innerHTML = '<i class="fa-solid fa-check-double"></i> Alerted!';
            buttonElement.classList.remove('bg-indigo-500', 'bg-rose-500/20', 'text-rose-300');
            buttonElement.classList.add('bg-emerald-500/20', 'text-emerald-400');

            setTimeout(() => {
                buttonElement.innerHTML = structuralBackup;
                buttonElement.disabled = false;
                buttonElement.className = buttonElement.className.replace('bg-emerald-500/20 text-emerald-400', '');
                buttonElement.classList.remove('opacity-60');
            }, 2000);
        }, 1200);
    }
</script>
@endpush
