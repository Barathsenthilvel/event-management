@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    @php
        $candidateVotersMap = [];
        foreach ($positionStats as $block) {
            foreach (($block['voters'] ?? []) as $voter) {
                $candidateUserId = (int) ($voter['candidate_user_id'] ?? 0);
                if ($candidateUserId <= 0) {
                    continue;
                }
                if (!isset($candidateVotersMap[$candidateUserId])) {
                    $candidateVotersMap[$candidateUserId] = [];
                }
                $candidateVotersMap[$candidateUserId][] = [
                    'name' => $voter['name'] ?? '-',
                    'email' => $voter['email'] ?? '-',
                    'mobile' => $voter['mobile'] ?? '-',
                    'voted_at' => $voter['voted_at'] ?? '-',
                ];
            }
        }
    @endphp
    <div>
        <a href="{{ route('admin.pollings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to pollings
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.pollings.results', $polling) }}" class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5 shadow-sm space-y-4">
        @csrf
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Member-facing results</h2>
                <p class="text-xs text-slate-600 mt-1">When voting has ended, turn this on so members see totals and the official winner you select below.</p>
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-800">
                <input type="hidden" name="results_visible_to_members" value="0">
                <input type="checkbox" name="results_visible_to_members" value="1" class="rounded border-slate-300" {{ old('results_visible_to_members', $polling->results_visible_to_members) ? 'checked' : '' }}>
                Show results to members
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($positionStats as $block)
                @php $pos = $block['position']; @endphp
                <div class="rounded-xl border border-white bg-white p-3 shadow-sm">
                    <p class="text-[10px] font-black uppercase text-slate-500">Official winner</p>
                    <p class="text-xs font-bold text-slate-800 mb-2">{{ $pos->position }}</p>
                    <select name="winners[{{ $pos->id }}]" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-xs font-semibold text-slate-800">
                        <option value="">— Not set —</option>
                        @foreach($pos->candidates as $cand)
                            <option value="{{ $cand->id }}" {{ (int) old('winners.'.$pos->id, $pos->winner_user_id ?? 0) === (int) $cand->id ? 'selected' : '' }}>{{ $cand->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white text-sm font-extrabold">Save results &amp; visibility</button>
        </div>
    </form>

    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-start justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase">{{ $polling->title }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
        </div>
        <a href="{{ route('admin.pollings.report', $polling->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 p-4">
            <p class="text-sm font-extrabold text-slate-800 mb-2">{{ $polling->title }}</p>
            <p class="text-xs text-slate-500">
                @if($polling->polling_date_to && $polling->polling_date_to->toDateString() !== $polling->polling_date->toDateString())
                    {{ optional($polling->polling_date)->format('d M Y') }} – {{ $polling->polling_date_to->format('d M Y') }}
                @else
                    {{ optional($polling->polling_date)->format('d M Y') }}
                @endif
            </p>
            <p class="text-xs text-slate-500">
                {{ $polling->polling_from ? \Illuminate\Support\Carbon::parse($polling->polling_from)->format('h:i A') : '-' }}
                -
                {{ $polling->polling_to ? \Illuminate\Support\Carbon::parse($polling->polling_to)->format('h:i A') : '-' }}
            </p>
            <div class="mt-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $polling->polling_status === 'live' ? 'In Live' : 'Ends' }}
                </span>
            </div>
            <div class="mt-4 text-xs font-bold text-slate-700">Show Stats
                <span class="ml-2 px-2 py-0.5 rounded-full {{ $polling->show_stats ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $polling->show_stats ? 'ON' : 'OFF' }}
                </span>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-6 space-y-10">
            @forelse($positionStats as $block)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-lg font-extrabold text-[#351c42]">{{ $block['position']->position }}</h3>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $block['total_votes'] }} {{ $block['total_votes'] === 1 ? 'vote' : 'votes' }}</span>
                    </div>
                    @if(!empty($block['winner_name']))
                        <p class="mb-3 text-xs font-semibold text-emerald-800">
                            Official winner:
                            <span class="font-extrabold">{{ $block['winner_name'] }}</span>
                        </p>
                    @else
                        <p class="mb-3 text-xs font-semibold text-slate-500">Official winner: Not set</p>
                    @endif
                    @forelse($block['candidates'] as $candidate)
                        <div class="relative mb-3 overflow-hidden rounded-2xl border border-[#c4b5d5]/60 bg-[#f3eef9]/50">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 rounded-2xl bg-gradient-to-r from-[#d4c4e8] to-[#c9b6e0]"
                                style="width: {{ $candidate['bar_percent'] }}%"
                            ></div>
                            <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="truncate text-sm font-bold text-[#351c42]">{{ $candidate['name'] }}</span>
                                    @if(!empty($candidate['is_winner']))
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-emerald-800">Winner</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#351c42]/20 bg-white/80 text-[#351c42] transition hover:border-[#965995]/50 hover:bg-[#965995]/10"
                                        data-open-candidate-voters
                                        data-candidate-id="{{ $candidate['id'] }}"
                                        data-candidate-name="{{ $candidate['name'] }}"
                                        aria-label="View voters for {{ $candidate['name'] }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <span class="text-sm font-black tabular-nums text-[#351c42]">{{ $candidate['votes'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No candidates listed for this position.</p>
                    @endforelse

                </div>
            @empty
                <p class="text-sm text-slate-500">No positions found.</p>
            @endforelse
        </div>
    </div>
</div>

<div id="candidate-voters-modal" class="fixed inset-0 z-[180] hidden items-center justify-center bg-slate-900/55 p-4 backdrop-blur-[2px]" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="candidate-voters-modal-title">
    <div class="absolute inset-0" data-close-candidate-voters aria-hidden="true"></div>
    <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h3 id="candidate-voters-modal-title" class="text-base font-extrabold text-slate-900">Candidate voters</h3>
                <p class="mt-1 text-xs font-semibold text-slate-500" id="candidate-voters-modal-subtitle"></p>
            </div>
            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" data-close-candidate-voters aria-label="Close">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-left text-xs">
                    <thead class="bg-slate-50">
                        <tr class="text-[10px] font-black uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-2">Member</th>
                            <th class="px-3 py-2">Email</th>
                            <th class="px-3 py-2">Mobile</th>
                            <th class="px-3 py-2">Voted At</th>
                        </tr>
                    </thead>
                    <tbody id="candidate-voters-table-body"></tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
                <p id="candidate-voters-page-info" class="text-xs font-semibold text-slate-500"></p>
                <div class="flex items-center gap-2">
                    <button type="button" id="candidate-voters-prev" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Previous</button>
                    <button type="button" id="candidate-voters-next" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const dataMap = @json($candidateVotersMap);
        const modal = document.getElementById('candidate-voters-modal');
        if (!modal) return;

        const tbody = document.getElementById('candidate-voters-table-body');
        const subtitle = document.getElementById('candidate-voters-modal-subtitle');
        const pageInfo = document.getElementById('candidate-voters-page-info');
        const prevBtn = document.getElementById('candidate-voters-prev');
        const nextBtn = document.getElementById('candidate-voters-next');

        const pageSize = 10;
        let rows = [];
        let currentPage = 1;

        function render() {
            const total = rows.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * pageSize;
            const pageRows = rows.slice(start, start + pageSize);

            tbody.innerHTML = '';
            if (!pageRows.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-3 py-6 text-center text-xs font-semibold text-slate-500">No voters found for this candidate.</td></tr>';
            } else {
                pageRows.forEach((row) => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t border-slate-200 text-slate-700';
                    tr.innerHTML = `
                        <td class="px-3 py-2 font-semibold">${row.name || '-'}</td>
                        <td class="px-3 py-2">${row.email || '-'}</td>
                        <td class="px-3 py-2">${row.mobile || '-'}</td>
                        <td class="px-3 py-2">${row.voted_at || '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            pageInfo.textContent = `Showing ${total ? start + 1 : 0}-${Math.min(start + pageSize, total)} of ${total}`;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
            prevBtn.classList.toggle('opacity-50', prevBtn.disabled);
            nextBtn.classList.toggle('opacity-50', nextBtn.disabled);
        }

        function setOpen(open) {
            modal.classList.toggle('hidden', !open);
            modal.classList.toggle('flex', open);
            modal.setAttribute('aria-hidden', open ? 'false' : 'true');
            document.body.style.overflow = open ? 'hidden' : '';
        }

        document.querySelectorAll('[data-open-candidate-voters]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const candidateId = btn.getAttribute('data-candidate-id') || '';
                const candidateName = btn.getAttribute('data-candidate-name') || 'Candidate';
                rows = Array.isArray(dataMap[candidateId]) ? dataMap[candidateId] : [];
                currentPage = 1;
                subtitle.textContent = `${candidateName} • ${rows.length} vote(s)`;
                render();
                setOpen(true);
            });
        });

        prevBtn?.addEventListener('click', () => {
            if (currentPage <= 1) return;
            currentPage -= 1;
            render();
        });
        nextBtn?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(rows.length / pageSize));
            if (currentPage >= totalPages) return;
            currentPage += 1;
            render();
        });

        modal.querySelectorAll('[data-close-candidate-voters]').forEach((el) => {
            el.addEventListener('click', () => setOpen(false));
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                setOpen(false);
            }
        });
    })();
</script>
@endpush
