@php
    $p = $polling ?? null;
    $isEdit = $p !== null;
    $action = $isEdit ? route('admin.pollings.update', $p->id) : route('admin.pollings.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit ? $p->positions->map(fn ($row) => ['position' => $row->position, 'member_user_id' => $row->member_user_id])->values()->all() : [['position' => '', 'member_user_id' => '']]);

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
                <p class="text-[11px] text-slate-500 mb-2">For each position, choose the member who will receive votes. Members see this poll on their dashboard during the scheduled window.</p>
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <input class="col-span-5 px-3 py-2 rounded-lg border border-slate-200 text-xs" :name="`positions[${idx}][position]`" x-model="row.position" placeholder="Position title">
                        <select class="col-span-6 px-3 py-2 rounded-lg border border-slate-200 text-xs" :name="`positions[${idx}][member_user_id]`" x-model="row.member_user_id">
                            <option value="">Select candidate (member)</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->mobile ?: $member->email }})</option>
                            @endforeach
                        </select>
                        <button type="button" @click="removeRow(idx)" class="col-span-1 text-rose-600 font-black">x</button>
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
                    @php $publish = old('publish_status', $p?->publish_status ?? 'na'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-1">Publish Status</label>
                    <select name="publish_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="na" {{ $publish === 'na' ? 'selected' : '' }}>N/A</option>
                        <option value="pending" {{ $publish === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="published" {{ $publish === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    @php $status = old('polling_status', $p?->polling_status ?? 'ends'); @endphp
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

<script>
function pollingForm() {
    return {
        rows: @json($rows),
        coverUrl: @json($initialCoverUrl),
        bannerUrl: @json($initialBannerUrl),
        addRow() { this.rows.push({ position: '', member_user_id: '' }); },
        removeRow(idx) { if (this.rows.length > 1) this.rows.splice(idx, 1); },
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
