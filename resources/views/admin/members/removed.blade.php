@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-rose-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Removed Members</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">Members removed by admin. You can restore them anytime.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.members.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:border-[#965995]/30 hover:text-[#351c42]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Members
                </a>
                <form method="GET" class="flex gap-2">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/email/mobile..."
                        class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-rose-500/20 w-64">
                    <button class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-rose-600 text-white text-xs font-extrabold shadow-lg transition-all">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        @if($members->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No removed members</p>
                <p class="mt-1 text-xs font-bold text-slate-500">Members you remove will appear here for restoring.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-6 py-4">Member</th>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4">Removed On</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($members as $m)
                            <tr class="bg-white">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center font-black text-rose-700">
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
                                <td class="px-6 py-4">
                                    <p class="text-[11px] font-bold text-slate-700">{{ $m->deleted_at?->format('M j, Y') ?? '—' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $m->deleted_at?->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right align-middle">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.members.restore', $m->id) }}"
                                              onsubmit="return confirm('Restore {{ addslashes($m->name) }} back to the members list?');">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-widest text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v6h6M3 13a9 9 0 109-9"/></svg>
                                                Restore
                                            </button>
                                        </form>
                                        <button type="button"
                                            data-open-force-modal
                                            data-force-url="{{ route('admin.members.force-delete', $m->id) }}"
                                            data-member-name="{{ $m->name }}"
                                            class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-widest text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-50"
                                            title="Delete permanently" aria-label="Delete {{ $m->name }} permanently">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete permanently
                                        </button>
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

<div id="force-delete-modal" class="fixed inset-0 z-[160] hidden items-center justify-center bg-[#111827]/60 p-4 backdrop-blur-[2px]" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="force-delete-modal-title">
    <div data-close-force-modal class="absolute inset-0" aria-hidden="true"></div>
    <div class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-rose-50/70 px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </span>
                <h3 id="force-delete-modal-title" class="text-base font-extrabold text-[#351c42]">Permanently delete?</h3>
            </div>
            <button type="button" data-close-force-modal class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/></svg>
            </button>
        </div>
        <div class="px-5 py-5 text-sm text-slate-700">
            <p class="font-semibold text-slate-900" data-force-member-name></p>
            <p class="mt-1">This will <span class="font-bold text-rose-700">permanently delete</span> this member. This action <span class="font-bold">cannot be undone</span> and they cannot be restored.</p>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" data-close-force-modal class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">Cancel</button>
            <form id="force-delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-extrabold text-white shadow-lg shadow-rose-900/20 transition hover:bg-rose-700">
                    Delete permanently
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('force-delete-modal');
        if (!modal) return;
        const form = modal.querySelector('#force-delete-form');
        const nameEl = modal.querySelector('[data-force-member-name]');

        const openModal = (url, name) => {
            if (form) form.setAttribute('action', url);
            if (nameEl) nameEl.textContent = name ? ('Member: ' + name) : '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            if (form) form.setAttribute('action', '');
        };

        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-open-force-modal]');
            if (trigger) {
                e.preventDefault();
                openModal(trigger.getAttribute('data-force-url'), trigger.getAttribute('data-member-name'));
                return;
            }
            if (e.target.closest('[data-close-force-modal]')) {
                e.preventDefault();
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    })();
</script>
@endsection
