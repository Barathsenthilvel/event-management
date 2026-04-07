@php
    $p = $polling ?? null;
    $isEdit = $p !== null;
    $action = $isEdit ? route('admin.pollings.update', $p->id) : route('admin.pollings.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit
            ? $p->positions->map(fn ($row) => [
                'position' => $row->position,
                'candidate_ids' => $row->candidates->pluck('id')->values()->all(),
            ])->values()->all()
            : [['position' => '', 'candidate_ids' => []]]);

    $defaultPollingFrom = $isEdit && $p->polling_from
        ? \Illuminate\Support\Carbon::parse($p->polling_from)->format('H:i')
        : '';
    $defaultPollingTo = $isEdit && $p->polling_to
        ? \Illuminate\Support\Carbon::parse($p->polling_to)->format('H:i')
        : '';

    $initialCoverUrl = $isEdit && !empty($p->cover_image_path)
        ? asset('storage/' . ltrim($p->cover_image_path, '/'))
        : null;
    $initialBannerUrl = $isEdit && !empty($p->banner_image_path)
        ? asset('storage/' . ltrim($p->banner_image_path, '/'))
        : null;

    $membersJson = $members->map(fn ($m) => [
        'id' => $m->id,
        'name' => $m->name,
        'detail' => $m->mobile ?: $m->email,
    ])->values();
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" x-data="pollingForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Polling Title</label>
                <input type="text" name="title" value="{{ old('title', $p?->title ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black text-slate-600">Positions & candidates</label>
                    <button type="button" @click="addRow()" class="text-xs font-black text-indigo-600">+ Add</button>
                </div>
                <p class="text-[11px] text-slate-500 mb-2">For each position, search and select one or more members who will appear on the ballot. Members vote during the scheduled window.</p>
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50/40 p-3">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12 sm:items-start">
                            <div class="sm:col-span-5">
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Position</label>
                                <input class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs" :name="`positions[${idx}][position]`" x-model="row.position" placeholder="Position title">
                            </div>
                            <div class="relative sm:col-span-6" @click.outside="closeSelect()">
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Candidates</label>
                                <button type="button" @click="toggleSelect(idx)" class="flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-xs font-semibold text-slate-800 shadow-sm">
                                    <span class="truncate" x-text="selectSummary(row)"></span>
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div
                                    x-show="openSelectIdx === idx"
                                    x-cloak
                                    x-transition
                                    class="absolute left-0 right-0 top-full z-50 mt-1 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                                >
                                    <div class="border-b border-slate-100 p-2">
                                        <div class="relative">
                                            <span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </span>
                                            <input
                                                type="search"
                                                x-model="selectSearch"
                                                placeholder="Type to search..."
                                                class="w-full rounded-lg border border-indigo-200 py-2 pl-9 pr-2 text-xs outline-none ring-0 focus:border-indigo-400"
                                                @click.stop
                                            >
                                        </div>
                                    </div>
                                    <ul class="max-h-52 overflow-y-auto py-1">
                                        <template x-for="m in filteredMembers()" :key="m.id">
                                            <li>
                                                <button type="button" @click="toggleCandidate(row, m.id)" class="flex w-full items-start gap-2 px-3 py-2 text-left text-xs hover:bg-indigo-50" :class="isSelected(row, m.id) ? 'bg-indigo-50' : ''">
                                                    <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border" :class="isSelected(row, m.id) ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                                        <span x-show="isSelected(row, m.id)" class="text-[10px] leading-none text-white">✓</span>
                                                    </span>
                                                    <span>
                                                        <span x-text="m.name" class="font-semibold text-slate-800"></span>
                                                        <span class="block text-[10px] text-slate-500" x-text="m.detail"></span>
                                                    </span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                    <p class="border-t border-slate-100 px-3 py-2 text-[10px] text-slate-500" x-text="members.length + ' members available'"></p>
                                </div>
                                <template x-for="cid in row.candidate_ids" :key="'hid-' + idx + '-' + cid">
                                    <input type="hidden" :name="`positions[${idx}][candidate_ids][]`" :value="cid">
                                </template>
                            </div>
                            <div class="flex justify-end sm:col-span-1">
                                <button type="button" @click="removeRow(idx)" class="mt-6 rounded-lg px-2 py-1 text-rose-600 font-black sm:mt-0" title="Remove row">×</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-2">Polling Dates</p>
                <p class="text-[10px] text-slate-500 mb-2">Times are saved as 24-hour <span class="font-mono">HH:MM</span>.</p>
                <div class="grid grid-cols-3 gap-2">
                    <input type="date" name="polling_date" value="{{ old('polling_date', $p?->polling_date?->format('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_from" step="60" value="{{ old('polling_from', $defaultPollingFrom) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_to" step="60" value="{{ old('polling_to', $defaultPollingTo) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                </div>
                @error('polling_date')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('polling_from')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('polling_to')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-2">Images</p>
                <p class="text-[10px] text-slate-500 mb-3">Preview updates when you choose a new file.</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-3">
                        <p class="text-xs font-semibold text-slate-700 mb-2">Cover Image</p>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <template x-if="coverUrl">
                                <img x-bind:src="coverUrl" alt="Cover preview" class="h-36 w-full object-cover">
                            </template>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-indigo-700 hover:bg-indigo-50">
                            <span x-text="coverUrl ? 'Change cover image' : 'Upload cover image'"></span>
                            <input type="file" name="cover_image" class="hidden" accept="image/*" @change="pickCover($event)">
                        </label>
                        @error('cover_image')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-3">
                        <p class="text-xs font-semibold text-slate-700 mb-2">Banner Image</p>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <template x-if="bannerUrl">
                                <img x-bind:src="bannerUrl" alt="Banner preview" class="h-36 w-full object-cover">
                            </template>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-indigo-700 hover:bg-indigo-50">
                            <span x-text="bannerUrl ? 'Change banner image' : 'Upload banner image'"></span>
                            <input type="file" name="banner_image" class="hidden" accept="image/*" @change="pickBanner($event)">
                        </label>
                        @error('banner_image')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs"><input type="checkbox" name="promote_front" value="1" {{ old('promote_front', $p?->promote_front ?? false) ? 'checked' : '' }}> Promote Front</label>
                <label class="text-xs"><input type="checkbox" name="show_stats" value="1" {{ old('show_stats', $p?->show_stats ?? true) ? 'checked' : '' }}> Show Stats</label>
                <div>
                    @php $publish = old('publish_status', $p?->publish_status ?? 'published'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-1">Publish Status</label>
                    <select name="publish_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="na" {{ $publish === 'na' ? 'selected' : '' }}>N/A</option>
                        <option value="pending" {{ $publish === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="published" {{ $publish === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    @php $status = old('polling_status', $p?->polling_status ?? 'live'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-1">Polling Status</label>
                    <select name="polling_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="live" {{ $status === 'live' ? 'selected' : '' }}>In Live</option>
                        <option value="ends" {{ $status === 'ends' ? 'selected' : '' }}>Ends</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.pollings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white text-sm font-bold">{{ $isEdit ? 'Update' : 'Create' }}</button>
    </div>
</form>

<style>[x-cloak]{display:none!important}</style>

<script>
function pollingForm() {
    return {
        rows: @json($rows),
        members: @json($membersJson),
        coverUrl: @json($initialCoverUrl),
        bannerUrl: @json($initialBannerUrl),
        openSelectIdx: null,
        selectSearch: '',
        addRow() {
            this.rows.push({ position: '', candidate_ids: [] });
        },
        removeRow(idx) {
            if (this.rows.length > 1) {
                this.rows.splice(idx, 1);
                if (this.openSelectIdx === idx) {
                    this.openSelectIdx = null;
                    this.selectSearch = '';
                }
            }
        },
        closeSelect() {
            this.openSelectIdx = null;
            this.selectSearch = '';
        },
        toggleSelect(idx) {
            if (this.openSelectIdx === idx) {
                this.closeSelect();
            } else {
                this.openSelectIdx = idx;
                this.selectSearch = '';
            }
        },
        filteredMembers() {
            const q = (this.selectSearch || '').trim().toLowerCase();
            if (!q) return this.members;
            return this.members.filter(m =>
                (m.name || '').toLowerCase().includes(q) ||
                (m.detail || '').toLowerCase().includes(q) ||
                String(m.id).includes(q)
            );
        },
        isSelected(row, id) {
            return row.candidate_ids.includes(id);
        },
        toggleCandidate(row, id) {
            const i = row.candidate_ids.indexOf(id);
            if (i >= 0) {
                row.candidate_ids.splice(i, 1);
            } else {
                row.candidate_ids.push(id);
            }
        },
        selectSummary(row) {
            const n = row.candidate_ids.length;
            if (n === 0) return 'Select…';
            if (n === 1) {
                const m = this.members.find(x => x.id === row.candidate_ids[0]);
                return m ? m.name : '1 selected';
            }
            return n + ' selected';
        },
        pickCover(e) { this.setImagePreview(e, 'coverUrl'); },
        pickBanner(e) { this.setImagePreview(e, 'bannerUrl'); },
        setImagePreview(e, key) {
            const f = e.target.files && e.target.files[0];
            if (!f || !f.type.startsWith('image/')) return;
            const r = new FileReader();
            r.onload = () => { this[key] = r.result; };
            r.readAsDataURL(f);
        }
    };
}
</script>
