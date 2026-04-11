@php
    $n = $nomination ?? null;
    $isEdit = $n !== null;
    $action = $isEdit ? route('admin.nominations.update', $n->id) : route('admin.nominations.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit ? $n->positions->map(fn ($p) => ['position' => $p->position])->values()->all() : [['position' => '']]);

    $defaultPollingFrom = $isEdit && $n->polling_from
        ? \Illuminate\Support\Carbon::parse($n->polling_from)->format('H:i')
        : '';
    $defaultPollingTo = $isEdit && $n->polling_to
        ? \Illuminate\Support\Carbon::parse($n->polling_to)->format('H:i')
        : '';

    $initialCoverUrl = $isEdit && !empty($n->cover_image_path)
        ? asset('storage/' . ltrim($n->cover_image_path, '/'))
        : null;
    $initialBannerUrl = $isEdit && !empty($n->banner_image_path)
        ? asset('storage/' . ltrim($n->banner_image_path, '/'))
        : null;
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" x-data="nominationForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $n?->title ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black text-slate-600">Positions</label>
                    <button type="button" @click="addRow()" class="text-xs font-black text-indigo-600">+ Add</button>
                </div>
                <p class="text-[11px] text-slate-500 mb-2">Announcement-style: enter each open role by title only. Members tap “I’m interested” on their side; interested members appear under Submissions. Assign candidates to roles in <span class="font-semibold text-slate-600">Polling</span>, not here.</p>
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input class="min-w-0 flex-1 px-3 py-2 rounded-lg border border-slate-200 text-xs" :name="`positions[${idx}][position]`" x-model="row.position" placeholder="Position title">
                        <button type="button" @click="removeRow(idx)" class="shrink-0 px-2 text-rose-600 font-black" title="Remove row">×</button>
                    </div>
                </template>
                @error('positions')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('positions.*.position')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Terms</label>
                <textarea name="terms" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('terms', $n?->terms ?? '') }}</textarea>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-1">Interest window</p>
                <p class="text-[10px] text-slate-500 mb-3"><span class="font-semibold">From date</span> and <span class="font-semibold">To date</span> (leave To date empty for a single day). Times use 24-hour <span class="font-mono">HH:MM</span>.</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">From date</label>
                        <input type="date" name="polling_date" value="{{ old('polling_date', $n?->polling_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">To date</label>
                        <input type="date" name="polling_date_to" value="{{ old('polling_date_to', $n?->polling_date_to?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs" title="Optional — defaults to From date">
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Start time</label>
                        <input type="time" name="polling_from" step="60" value="{{ old('polling_from', $defaultPollingFrom) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">End time</label>
                        <input type="time" name="polling_to" step="60" value="{{ old('polling_to', $defaultPollingTo) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                </div>
                @error('polling_date')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('polling_date_to')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('polling_from')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('polling_to')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-2">Images</p>
                <p class="text-[10px] text-slate-500 mb-3">Preview updates when you choose a new file. Existing images stay until you replace them.</p>
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
                <div>
                    @php $status = old('status', $n?->status ?? 'draft'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="text-xs"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $n?->is_active ?? true) ? 'checked' : '' }}> Display Active</label>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.nominations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white text-sm font-bold">{{ $isEdit ? 'Update' : 'Create' }}</button>
    </div>
</form>

<script>
function nominationForm() {
    return {
        rows: @json($rows),
        coverUrl: @json($initialCoverUrl),
        bannerUrl: @json($initialBannerUrl),
        addRow() {
            this.rows.push({ position: '' });
        },
        removeRow(idx) {
            if (this.rows.length === 1) return;
            this.rows.splice(idx, 1);
        },
        pickCover(e) {
            this.setImagePreview(e, 'coverUrl');
        },
        pickBanner(e) {
            this.setImagePreview(e, 'bannerUrl');
        },
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
