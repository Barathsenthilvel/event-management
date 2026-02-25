<?php

namespace App\Http\Controllers;

use App\Models\DefaultSetting;
use Illuminate\Http\Request;

class DefaultSettingsController extends Controller
{
    public function index()
    {
        $settings = DefaultSetting::orderBy('is_default', 'desc')->orderBy('country_name')->get();
        $countries = config('countries_currencies.countries', []);
        $currencies = config('countries_currencies.currencies', []);
        $timeFormats = config('countries_currencies.time_formats', []);

        return view('admin.settings.default-settings', compact('settings', 'countries', 'currencies', 'timeFormats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|max:3',
            'country_name' => 'required|string|max:255',
            'currency_code' => 'required|string|max:10',
            'currency_name' => 'nullable|string|max:255',
            'time_format' => 'required|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            DefaultSetting::query()->update(['is_default' => false]);
        }

        $currencies = config('countries_currencies.currencies', []);
        $currencyName = $validated['currency_name'] ?? ($currencies[$validated['currency_code']] ?? null);

        DefaultSetting::create([
            'country_code' => $validated['country_code'],
            'country_name' => $validated['country_name'],
            'currency_code' => $validated['currency_code'],
            'currency_name' => $currencyName,
            'time_format' => $validated['time_format'],
            'is_default' => !empty($validated['is_default']),
        ]);

        return redirect()->route('admin.settings.default-settings')
            ->with('success', 'Default setting added successfully.');
    }

    public function setDefault(DefaultSetting $defaultSetting)
    {
        DefaultSetting::query()->update(['is_default' => false]);
        $defaultSetting->update(['is_default' => true]);

        return redirect()->route('admin.settings.default-settings')
            ->with('success', 'Default setting updated.');
    }

    public function destroy(DefaultSetting $defaultSetting)
    {
        $defaultSetting->delete();
        return redirect()->route('admin.settings.default-settings')
            ->with('success', 'Setting removed.');
    }
}
