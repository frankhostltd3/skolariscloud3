<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(): View
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('code')
            ->get();

        return view('admin.settings.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new currency.
     */
    public function create(): View
    {
        return view('admin.settings.currencies.create');
    }

    /**
     * Store a newly created currency.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'thousands_separator' => 'required|string|max:5',
            'decimal_separator' => 'required|string|max:5',
            'grade_levels' => 'nullable|string',
        ]);

        // Process grade_levels if provided
        if (!empty($validated['grade_levels'])) {
            $validated['grade_levels'] = array_map('trim', explode(',', $validated['grade_levels']));
        } else {
            $validated['grade_levels'] = null;
        }

        // Handle boolean fields properly (checkboxes don't send false values)
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);

        DB::transaction(function () use ($validated) {
            // If setting as default, remove default from others
            if ($validated['is_default']) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }

            Currency::create($validated);
        });

        return redirect()->route('tenant.settings.admin.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Display the specified currency.
     */
    public function show(Currency $currency): View
    {
        return view('admin.settings.currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit(Currency $currency): View
    {
        return view('admin.settings.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified currency.
     */
    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'thousands_separator' => 'required|string|max:5',
            'decimal_separator' => 'required|string|max:5',
            'grade_levels' => 'nullable|string',
        ]);

        // Process grade_levels if provided
        if (!empty($validated['grade_levels'])) {
            $validated['grade_levels'] = array_map('trim', explode(',', $validated['grade_levels']));
        } else {
            $validated['grade_levels'] = null;
        }

        // Handle boolean fields properly (checkboxes don't send false values)
        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_default'] = $request->boolean('is_default', false);

        DB::transaction(function () use ($validated, $currency) {
            // If setting as default, remove default from others
            if ($validated['is_default']) {
                Currency::where('id', '!=', $currency->id)
                       ->where('is_default', true)
                       ->update(['is_default' => false]);
            }

            $currency->update($validated);
        });

        return redirect()->route('tenant.settings.admin.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        // Check if currency is being used by tuition plans
        if ($currency->tuitionPlans()->exists()) {
            return redirect()->route('tenant.settings.admin.currencies.index')
                ->with('error', 'Cannot delete currency that is being used by tuition plans.');
        }

        // Check if it's the default currency
        if ($currency->is_default) {
            return redirect()->route('tenant.settings.admin.currencies.index')
                ->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('tenant.settings.admin.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }

    /**
     * Set a currency as the default.
     */
    public function setDefault(Currency $currency): RedirectResponse
    {
        DB::transaction(function () use ($currency) {
            // Remove default from all currencies
            Currency::where('is_default', true)->update(['is_default' => false]);

            // Set this currency as default
            $currency->update(['is_default' => true]);
        });

        return redirect()->route('tenant.settings.admin.currencies.index')
            ->with('success', 'Default currency updated successfully.');
    }

    /**
     * Toggle currency active status.
     */
    public function toggle(Currency $currency): RedirectResponse
    {
        // Don't allow deactivating the default currency
        if ($currency->is_default && $currency->is_active) {
            return redirect()->route('tenant.settings.admin.currencies.index')
                ->with('error', 'Cannot deactivate the default currency.');
        }

        $currency->update(['is_active' => !$currency->is_active]);

        $status = $currency->is_active ? 'activated' : 'deactivated';

        return redirect()->route('tenant.settings.admin.currencies.index')
            ->with('success', "Currency {$status} successfully.");
    }
}
