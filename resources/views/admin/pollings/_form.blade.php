@php
    $isEdit = isset($polling);
    $action = $isEdit ? route('admin.pollings.update', $polling->id) : route('admin.pollings.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit ? $polling->positions->map(fn ($p) => ['position' => $p->position, 'member_user_id' => $p->member_user_id])->values()->all() : [['position' => '', 'member_user_id' => '']]);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" x-data="pollingForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Polling Title</label>
                <input type="text" name="title" value="{{ old('title', $polling->title ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black text-slate-600">Position & Member List</label>
                    <button type="button" @click="addRow()" class="text-xs font-black text-indigo-600">+ Add</button>
                </div>
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <input class="col-span-5 px-3 py-2 rounded-lg border border-slate-200 text-xs" :name="`positions[${idx}][position]`" x-model="row.position" placeholder="Position">
                        <select class="col-span-6 px-3 py-2 rounded-lg border border-slate-200 text-xs" :name="`positions[${idx}][member_user_id]`" x-model="row.member_user_id">
                            <option value="">Search Member</option>
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
                <div class="grid grid-cols-3 gap-2">
                    <input type="date" name="polling_date" value="{{ old('polling_date', optional($polling->polling_date ?? null)->format('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_from" value="{{ old('polling_from', $polling->polling_from ?? '') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_to" value="{{ old('polling_to', $polling->polling_to ?? '') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-2">Images</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Cover Image</span>
                        <input type="file" name="cover_image" class="hidden" accept="image/*">
                    </label>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Banner Image</span>
                        <input type="file" name="banner_image" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <label class="text-xs"><input type="checkbox" name="promote_front" value="1" {{ old('promote_front', $polling->promote_front ?? false) ? 'checked' : '' }}> Promote Front</label>
                <label class="text-xs"><input type="checkbox" name="show_stats" value="1" {{ old('show_stats', $polling->show_stats ?? true) ? 'checked' : '' }}> Show Stats</label>
                <div>
                    @php $publish = old('publish_status', $polling->publish_status ?? 'na'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-1">Publish Status</label>
                    <select name="publish_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="na" {{ $publish === 'na' ? 'selected' : '' }}>N/A</option>
                        <option value="pending" {{ $publish === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="published" {{ $publish === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    @php $status = old('polling_status', $polling->polling_status ?? 'ends'); @endphp
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
        addRow() { this.rows.push({ position: '', member_user_id: '' }); },
        removeRow(idx) { if (this.rows.length > 1) this.rows.splice(idx, 1); }
    }
}
</script>

