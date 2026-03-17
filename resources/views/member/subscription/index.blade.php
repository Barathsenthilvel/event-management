@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[28px] border border-white bg-gradient-to-br from-white via-white to-indigo-50/40 shadow-sm p-6 md:p-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Hello, {{ $user->first_name ?? 'Member' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Choose your subscription plan and continue.</p>
            </div>
            <a href="{{ route('member.dashboard') }}"
                class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-12">
            <div class="bg-white p-6 md:p-8 rounded-[28px] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Subscription Plans</h2>
                        <p class="text-xs text-slate-500">Fees are shown in INR. Registration fee applies only for new subscription (if enabled).</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[1100px] w-full text-left text-xs">
                        <thead class="text-[10px] font-extrabold text-white uppercase tracking-widest bg-indigo-600">
                            <tr>
                                <th class="px-5 py-4 rounded-l-2xl">Type</th>
                                <th class="px-5 py-4">Subscription Fee</th>
                                <th class="px-5 py-4">Registration Fee</th>
                                <th class="px-5 py-4">Monthly</th>
                                <th class="px-5 py-4">Bi - Monthly</th>
                                <th class="px-5 py-4">Quarterly</th>
                                <th class="px-5 py-4">Half Yearly</th>
                                <th class="px-5 py-4">Yearly</th>
                                <th class="px-5 py-4 rounded-r-2xl">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rows as $row)
                                <tr class="bg-slate-50/30">
                                    <td class="px-5 py-4 font-extrabold text-slate-800">
                                        {{ $row['subscription_type'] === 'New' ? 'New Members Subscription' : 'Renewal Subscription' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['subscription_fee'] !== null)
                                            {{ number_format($row['subscription_fee'], 0) }} / Month
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['subscription_type'] === 'New')
                                            {{ number_format((float)($row['registration_fee'] ?? 0), 0) }} / Year
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['monthly'] !== null)
                                            {{ number_format($row['monthly'], 0) }} INR
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['bi_monthly'] !== null)
                                            {{ number_format($row['bi_monthly'], 0) }} INR
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['quarterly'] !== null)
                                            {{ number_format($row['quarterly'], 0) }} INR
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['half_yearly'] !== null)
                                            {{ number_format($row['half_yearly'], 0) }} INR
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($row['yearly'] !== null)
                                            {{ number_format($row['yearly'], 0) }} INR
                                        @else
                                            <span class="text-slate-400 font-bold">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 font-extrabold text-slate-800">
                                        @if($row['total'] !== null)
                                            {{ number_format($row['total'], 0) }}
                                            <span class="text-slate-500 font-bold text-[10px] block">(Membership fee + Registration Fee)</span>
                                        @else
                                            <span class="text-slate-400 font-bold">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-center">
                    <div class="bg-white border border-slate-200 rounded-[28px] shadow-sm p-10 text-center w-full max-w-lg">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">Hello, Member</h3>
                        <p class="text-slate-600 mb-8">
                            Your Membership fee depends on your plan selection.
                        </p>
                        <button type="button"
                            class="inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-extrabold shadow-lg shadow-indigo-200 transition-all">
                            Pay Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

