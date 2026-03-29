@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6 gap-3" x-data="defaultSettingsPage()">
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Default Settings</h1>
            <p class="text-xs text-slate-500 mt-1">Set default country, currency and time format. Choose from the list and set one as default.</p>
        </div>
    </div>

    <div class="bg-white flex-1 rounded-[24px] shadow-sm flex flex-col p-6 overflow-hidden">
        <!-- Add form -->
        <div class="mb-8 p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
            <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-widest">Add default setting</h2>
            <form action="{{ route('admin.settings.default-settings.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Country: searchable dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Country</label>
                        <div class="relative">
                            <input type="text"
                                x-model="countrySearch"
                                @focus="countryOpen = true"
                                @click.away="countryOpen = false"
                                placeholder="Search and select country..."
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <input type="hidden" name="country_code" :value="selectedCountry ? selectedCountry.code : ''">
                            <input type="hidden" name="country_name" :value="selectedCountry ? selectedCountry.name : ''">
                            <div x-show="countryOpen" x-cloak
                                class="absolute z-20 mt-1 w-full max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg custom-scroll">
                                <template x-for="c in filteredCountries" :key="c.code">
                                    <div @click="selectCountry(c)"
                                        class="px-4 py-2.5 text-sm cursor-pointer hover:bg-indigo-50 rounded-lg"
                                        x-text="c.name">
                                    </div>
                                </template>
                                <div x-show="filteredCountries.length === 0" class="px-4 py-3 text-slate-400 text-sm">No country found</div>
                            </div>
                            <p class="mt-1 text-xs text-slate-500" x-show="selectedCountry" x-text="selectedCountry ? 'Selected: ' + selectedCountry.name : ''"></p>
                        </div>
                        @error('country_code')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency: dropdown (pre-filled from selected country) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Currency</label>
                        <select name="currency_code" required x-model="selectedCurrency"
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none cursor-pointer">
                            <option value="">Select currency</option>
                            @foreach(config('countries_currencies.currencies', []) as $code => $label)
                                <option value="{{ $code }}">{{ $code }} - {{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Filtered for selected country; you can change if needed.</p>
                        @error('currency_code')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Time format -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Time format</label>
                        <select name="time_format" required
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none cursor-pointer">
                            @foreach(config('countries_currencies.time_formats', []) as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('time_format')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Set as default toggle -->
                    <div class="flex items-center">
                        <div class="p-4 bg-white rounded-xl border border-slate-200 flex items-center justify-between flex-1">
                            <div>
                                <span class="text-sm font-bold text-slate-700 block">Set as default</span>
                                <p class="text-[10px] text-slate-400">Use this as the default country/currency/time format</p>
                            </div>
                            <label class="relative inline-block w-11 h-6">
                                <input type="checkbox" name="is_default" value="1" class="sr-only peer">
                                <span class="absolute inset-0 bg-slate-300 rounded-full transition peer-checked:bg-indigo-600"></span>
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition shadow peer-checked:translate-x-5"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                        Add to list
                    </button>
                </div>
            </form>
        </div>

        <!-- List of default settings -->
        <div>
            <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-widest">Saved default settings</h2>
            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left island-row">
                    <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-widest sticky top-0 bg-white z-10">
                        <tr>
                            <th class="px-6 py-4">Country</th>
                            <th class="px-6 py-4">Currency</th>
                            <th class="px-6 py-4">Time format</th>
                            <th class="px-6 py-4 text-center">Default</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @forelse($settings as $s)
                        <tr class="group transition-all">
                            <td class="px-6 py-4 bg-white border-y border-l border-slate-100 first:rounded-l-2xl">
                                <span class="font-bold text-slate-800">{{ $s->country_name }}</span>
                                <span class="text-slate-400 ml-1">({{ $s->country_code }})</span>
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100">
                                <span class="font-medium text-slate-700">{{ $s->currency_code }}</span>
                                @if($s->currency_name)
                                    <span class="text-slate-400"> - {{ $s->currency_name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-slate-600">{{ $s->time_format }}</td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-center">
                                @if($s->is_default)
                                    <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-100 font-black text-[9px] uppercase">Default</span>
                                @else
                                    <form action="{{ route('admin.settings.default-settings.set-default', $s) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:underline text-[10px] font-bold">Set as default</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-r border-slate-100 last:rounded-r-2xl text-right">
                                <form id="admin-delete-default-setting-{{ $s->id }}" action="{{ route('admin.settings.default-settings.destroy', $s) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg" title="Delete"
                                        data-delete-form="admin-delete-default-setting-{{ $s->id }}"
                                        data-delete-title="Remove this setting?"
                                        data-delete-message="This default country, currency, or timezone option will be deleted."
                                        onclick="adminOpenDeleteModalFromEl(this)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                <p>No default settings yet. Add one using the form above.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function defaultSettingsPage() {
    const countries = @json(config('countries_currencies.countries', []));
    return {
        countries,
        countrySearch: '',
        countryOpen: false,
        selectedCountry: null,
        selectedCurrency: '',
        get filteredCountries() {
            if (!this.countrySearch) return this.countries;
            const s = this.countrySearch.toLowerCase();
            return this.countries.filter(c => c.name.toLowerCase().includes(s));
        },
        selectCountry(c) {
            this.selectedCountry = c;
            this.countryOpen = false;
            this.countrySearch = c.name;
            this.selectedCurrency = c.currency || '';
        }
    };
}
</script>
@endsection
