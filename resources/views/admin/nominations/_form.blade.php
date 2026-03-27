@php
    $isEdit = isset($nomination);
    $action = $isEdit ? route('admin.nominations.update', $nomination->id) : route('admin.nominations.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit ? $nomination->positions->map(fn ($p) => ['position' => $p->position, 'member_user_id' => $p->member_user_id])->values()->all() : [['position' => '', 'member_user_id' => '']]);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" x-data="nominationForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $nomination->title ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
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
                @error('positions')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                @error('positions.*.position')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Terms</label>
                <textarea name="terms" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('terms', $nomination->terms ?? '') }}</textarea>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-black text-slate-700">Polling Dates</p>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <input type="date" name="polling_date" value="{{ old('polling_date', optional($nomination->polling_date ?? null)->format('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_from" value="{{ old('polling_from', $nomination->polling_from ?? '') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    <input type="time" name="polling_to" value="{{ old('polling_to', $nomination->polling_to ?? '') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-xs">
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
                <div>
                    @php $status = old('status', $nomination->status ?? 'draft'); @endphp
                    <label class="block text-xs font-black text-slate-600 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="text-xs"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $nomination->is_active ?? true) ? 'checked' : '' }}> Display Active</label>
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
        addRow() {
            this.rows.push({ position: '', member_user_id: '' });
        },
        removeRow(idx) {
            if (this.rows.length === 1) return;
            this.rows.splice(idx, 1);
        }
    }
}
</script>

