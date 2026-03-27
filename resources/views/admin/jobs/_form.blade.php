@php
    $isEdit = isset($job);
    $action = $isEdit ? route('admin.jobs.update', $job->id) : route('admin.jobs.store');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Search Hospital *</label>
                <input type="text" name="hospital" value="{{ old('hospital', $job->hospital ?? '') }}" placeholder="Enter Hospital / Account No"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('hospital')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Job Title *</label>
                <input type="text" name="title" value="{{ old('title', $job->title ?? '') }}" placeholder="Enter Job Title"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Job Code *</label>
                <input type="text" name="code" value="{{ old('code', $job->code ?? '') }}" placeholder="Enter Job Code"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('code')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Vacancy Type *</label>
                <div class="flex items-center gap-4 text-sm">
                    <label><input type="checkbox" name="vacancy_permanent" value="1" {{ old('vacancy_permanent', $job->vacancy_permanent ?? false) ? 'checked' : '' }}> Permanent</label>
                    <label><input type="checkbox" name="vacancy_temporary" value="1" {{ old('vacancy_temporary', $job->vacancy_temporary ?? false) ? 'checked' : '' }}> Temporary</label>
                    <label><input type="checkbox" name="vacancy_any" value="1" {{ old('vacancy_any', $job->vacancy_any ?? true) ? 'checked' : '' }}> Any</label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Preference *</label>
                <div class="flex items-center gap-4 text-sm">
                    <label><input type="checkbox" name="preference_wfh" value="1" {{ old('preference_wfh', $job->preference_wfh ?? false) ? 'checked' : '' }}> WFH</label>
                    <label><input type="checkbox" name="preference_onsite" value="1" {{ old('preference_onsite', $job->preference_onsite ?? false) ? 'checked' : '' }}> Onsite</label>
                    <label><input type="checkbox" name="preference_any" value="1" {{ old('preference_any', $job->preference_any ?? true) ? 'checked' : '' }}> Any</label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">No. of Openings *</label>
                <input type="number" min="0" name="no_of_openings" value="{{ old('no_of_openings', $job->no_of_openings ?? 0) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @error('no_of_openings')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4 min-h-40">
                <label class="block text-xs font-black text-slate-600 mb-2">Job Description</label>
                <textarea name="description" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('description', $job->description ?? '') }}</textarea>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 min-h-40">
                <label class="block text-xs font-black text-slate-600 mb-2">Key Skills or Roles & Responsibility</label>
                <textarea name="key_skills" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('key_skills', $job->key_skills ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <label class="text-xs"><input type="checkbox" name="promote_front" value="1" {{ old('promote_front', $job->promote_front ?? false) ? 'checked' : '' }}> Promote Front</label>
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1">Listing</label>
                    @php $listing = old('listing_status', $job->listing_status ?? 'listed'); @endphp
                    <select name="listing_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="listed" {{ $listing === 'listed' ? 'selected' : '' }}>Listed</option>
                        <option value="unlisted" {{ $listing === 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                    </select>
                </div>
                <label class="text-xs"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $job->is_active ?? true) ? 'checked' : '' }}> Active</label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.jobs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">{{ $isEdit ? 'Update' : 'Create' }}</button>
    </div>
</form>

