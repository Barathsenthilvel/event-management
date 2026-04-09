@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6"
     x-data="membersPage()">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Members</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">All registered members with status summary.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.designations.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:border-[#965995]/30 hover:text-[#351c42]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Designations
            </a>
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/email/mobile..."
                    class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 w-64">
                <button
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                    Search
                </button>
            </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold text-slate-900">Overview</p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">
                        Total: {{ $totalCount }} • Active: {{ $activeCount }} • Inactive: {{ $inactiveCount }}
                    </p>
                </div>
            </div>

            <div class="inline-flex rounded-full bg-slate-100 p-1 text-[11px] font-black uppercase tracking-widest">
                <button type="button"
                        class="px-4 py-1.5 rounded-full"
                        :class="tab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-700'"
                        @click.prevent="switchTab('all')">
                    All ({{ $totalCount }})
                </button>
                <button type="button"
                        class="px-4 py-1.5 rounded-full"
                        :class="tab === 'active' ? 'bg-emerald-600 text-white' : 'text-slate-700'"
                        @click.prevent="switchTab('active')">
                    Active ({{ $activeCount }})
                </button>
                <button type="button"
                        class="px-4 py-1.5 rounded-full"
                        :class="tab === 'inactive' ? 'bg-amber-500 text-white' : 'text-slate-700'"
                        @click.prevent="switchTab('inactive')">
                    Inactive ({{ $inactiveCount }})
                </button>
            </div>
        </div>

        <div id="members-table-root" x-ref="tableRoot">
            @if($members->count() === 0)
                <div class="p-10 text-center">
                    <p class="text-sm font-extrabold text-slate-900">No members found</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">Try changing the tab or search filters.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                            <tr>
                                <th class="px-6 py-4">Member</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4 text-center">Profile</th>
                                <th class="px-6 py-4 text-center">Approval</th>
                                <th class="px-6 py-4 min-w-[200px]">Designation</th>
                                <th class="px-6 py-4 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($members as $m)
                                <tr class="bg-white">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-black text-indigo-700">
                                                {{ strtoupper(substr($m->name ?? 'ME', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-extrabold text-slate-900 truncate">{{ $m->name }}</p>
                                                <p class="text-[11px] font-bold text-slate-500 truncate">{{ $m->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-[11px] font-bold text-slate-700">{{ $m->mobile ?? '—' }}</p>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">ID: {{ $m->id }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-xl px-3 py-1 text-[10px] font-black uppercase tracking-widest
                                            {{ $m->profile_completed ? 'bg-emerald-50 text-emerald-800 border border-emerald-100' : 'bg-amber-50 text-amber-800 border border-amber-100' }}">
                                            {{ $m->profile_completed ? 'Completed' : 'Incomplete' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-xl px-3 py-1 text-[10px] font-black uppercase tracking-widest
                                            {{ $m->is_approved ? 'bg-emerald-50 text-emerald-800 border border-emerald-100' : 'bg-amber-50 text-amber-800 border border-amber-100' }}">
                                            {{ $m->is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <form method="POST" action="{{ route('admin.members.designation.update', $m) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="designation_id"
                                                class="flex-1 min-w-0 max-w-[220px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px] font-bold text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500/20">
                                                <option value="">— None —</option>
                                                @foreach($designations as $d)
                                                    <option value="{{ $d->id }}" @selected($m->designation_id == $d->id)>{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="shrink-0 px-3 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-[10px] font-extrabold shadow-sm transition-all">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right align-middle">
                                        <button type="button"
                                            class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm transition hover:border-[#965995]/40 hover:text-[#351c42]"
                                            aria-expanded="false"
                                            data-member-detail-toggle="{{ $m->id }}">
                                            View
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="member-detail-{{ $m->id }}" class="hidden bg-slate-50/90">
                                    <td colspan="6" class="px-6 py-5">
                                        <div class="grid gap-6 lg:grid-cols-2">
                                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Profile details</p>
                                                <dl class="grid gap-2 text-[11px] font-bold text-slate-800 sm:grid-cols-2">
                                                    <div><dt class="text-slate-400">DOB</dt><dd>{{ $m->dob?->format('M j, Y') ?? '—' }}</dd></div>
                                                    <div><dt class="text-slate-400">Gender</dt><dd>{{ $m->gender ?? '—' }}</dd></div>
                                                    <div><dt class="text-slate-400">Qualification</dt><dd>{{ $m->qualification ?? '—' }}</dd></div>
                                                    <div><dt class="text-slate-400">Blood group</dt><dd>{{ $m->blood_group ?? '—' }}</dd></div>
                                                    <div class="sm:col-span-2"><dt class="text-slate-400">RNRM</dt><dd>{{ $m->rnrm_number_with_date ?? '—' }}</dd></div>
                                                    <div class="sm:col-span-2"><dt class="text-slate-400">College</dt><dd>{{ $m->college_name ?? '—' }}</dd></div>
                                                    <div class="sm:col-span-2"><dt class="text-slate-400">Address</dt><dd class="font-semibold text-slate-700">{{ trim(implode(', ', array_filter([$m->door_no, $m->locality_area, $m->state, $m->pin_code]))) ?: '—' }}</dd></div>
                                                    <div class="sm:col-span-2"><dt class="text-slate-400">Council state</dt><dd>{{ $m->council_state ?? '—' }}</dd></div>
                                                    <div class="sm:col-span-2"><dt class="text-slate-400">Currently working</dt><dd class="font-normal text-slate-600">{{ $m->currently_working ?: '—' }}</dd></div>
                                                </dl>
                                            </div>
                                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Membership &amp; payments</p>
                                                @php($activeSub = $m->activeSubscription)
                                                @if($activeSub)
                                                    <div class="rounded-xl bg-emerald-50/80 px-3 py-2 text-[11px] font-bold text-emerald-950">
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-800/80">Active subscription</p>
                                                        <p class="mt-1">{{ $activeSub->plan?->subscription_type ?? 'Plan' }} · {{ strtoupper((string) $activeSub->status) }}</p>
                                                        <p class="mt-1 text-[10px] font-semibold text-emerald-900/80">
                                                            {{ $activeSub->start_date?->format('M j, Y') ?? '—' }} — {{ $activeSub->end_date?->format('M j, Y') ?? '—' }}
                                                            <span class="text-slate-600">·</span> {{ $activeSub->currency ?? 'INR' }} {{ number_format((float) $activeSub->amount, 2) }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <p class="text-[11px] font-bold text-slate-500">No active subscription on file.</p>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Recent payments</p>
                                                    @if($m->paymentTransactions->isEmpty())
                                                        <p class="text-[11px] font-bold text-slate-500">No payment records.</p>
                                                    @else
                                                        <ul class="space-y-2">
                                                            @foreach($m->paymentTransactions as $pt)
                                                                <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-[10px] font-bold text-slate-800">
                                                                    <span class="uppercase tracking-widest text-slate-500">{{ $pt->status }}</span>
                                                                    <span>{{ $pt->subscriptionPlan?->subscription_type ?? 'Membership' }}</span>
                                                                    <span>₹{{ number_format((float) $pt->amount, 2) }}</span>
                                                                    <span class="text-slate-500">{{ $pt->paid_at?->format('M j, Y H:i') ?? '—' }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-slate-50">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    document.getElementById('members-table-root')?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-member-detail-toggle]');
        if (!btn) return;
        const id = btn.getAttribute('data-member-detail-toggle');
        const row = document.getElementById('member-detail-' + id);
        if (!row) return;
        row.classList.toggle('hidden');
        const open = !row.classList.contains('hidden');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('membersPage', () => ({
            tab: @json($tab),
            q: @json($q),
            loading: false,
            async switchTab(newTab) {
                if (this.tab === newTab || this.loading) return;
                this.tab = newTab;
                const params = new URLSearchParams();
                params.set('tab', newTab);
                if (this.q) params.set('q', this.q);
                this.loading = true;
                try {
                    const res = await fetch(`{{ route('admin.members.index') }}?` + params.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                    });
                    if (!res.ok) return;
                    const html = await res.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newRoot = doc.querySelector('#members-table-root');
                    if (newRoot && this.$refs.tableRoot) {
                        this.$refs.tableRoot.innerHTML = newRoot.innerHTML;
                    }
                    window.history.pushState({}, '', `{{ route('admin.members.index') }}?` + params.toString());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },
        }));
    });
</script>
@endsection

