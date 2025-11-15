<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return view('settings.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new currency.
     */
    public function create()
    {
        return view('settings.currencies.create');
    }

    /**
     * Store a newly created currency.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:3|unique:tenant.currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001|max:999999999',
            'is_active' => 'boolean',
            'auto_update_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currency = Currency::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'is_default' => false,
            'is_active' => $request->has('is_active'),
            'auto_update_enabled' => $request->has('auto_update_enabled'),
        ]);

        return redirect()->route('settings.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('settings.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified currency.
     */
    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:3|unique:tenant.currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001|max:999999999',
            'is_active' => 'boolean',
            'auto_update_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currency->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'is_active' => $request->has('is_active'),
            'auto_update_enabled' => $request->has('auto_update_enabled'),
        ]);

        return redirect()->route('settings.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified currency.
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);

        // Prevent deleting the default currency
        if ($currency->is_default) {
            return redirect()->back()
                ->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('settings.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }

    /**
     * Set the specified currency as default.
     */
    public function setDefault($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->setAsDefault();

        return redirect()->route('settings.currencies.index')
            ->with('success', $currency->name . ' set as default currency.');
    }

    /**
     * Toggle currency active status.
     */
    public function toggleActive($id)
    {
        $currency = Currency::findOrFail($id);

        // Prevent deactivating the default currency
        if ($currency->is_default && $currency->is_active) {
            return redirect()->back()
                ->with('error', 'Cannot deactivate the default currency.');
        }

        $currency->is_active = !$currency->is_active;
        $currency->save();

        $status = $currency->is_active ? 'activated' : 'deactivated';

        return redirect()->route('settings.currencies.index')
            ->with('success', $currency->name . ' ' . $status . ' successfully.');
    }

    /**
     * Update all exchange rates from external API.
     */
    public function updateRates(Request $request)
    {
        try {
            $exchangeRateService = app(\App\Services\ExchangeRateService::class);

            // Check if service is available
            if (!$exchangeRateService->isAvailable()) {
                return redirect()->back()
                    ->with('error', 'Exchange rate service is unavailable. Please check your internet connection.');
            }

            // Fetch latest rates
            $rates = $exchangeRateService->fetchRates();

            if (!$rates) {
                return redirect()->back()
                    ->with('error', 'Failed to fetch exchange rates from API.');
            }

            // Update currencies
            $updated = 0;
            $skipped = 0;

            $currencies = Currency::where('code', '!=', 'USD')
                ->where('is_active', true)
                ->get();

            foreach ($currencies as $currency) {
                if (isset($rates[$currency->code])) {
                    $newRate = $rates[$currency->code];

                    // Only update if rate has changed
                    if (abs($currency->exchange_rate - $newRate) > 0.000001) {
                        $currency->exchange_rate = $newRate;
                        $currency->last_updated_at = now();
                        $currency->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $skipped++;
                }
            }

            if ($updated > 0) {
                return redirect()->route('settings.currencies.index')
                    ->with('success', "Exchange rates updated successfully! Updated: {$updated}, Skipped: {$skipped}");
            } else {
                return redirect()->route('settings.currencies.index')
                    ->with('info', 'No exchange rates were updated. All rates are current.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating exchange rates: ' . $e->getMessage());
        }
    }

    /**
     * Toggle auto-update for a currency.
     */
    public function toggleAutoUpdate($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->auto_update_enabled = !$currency->auto_update_enabled;
        $currency->save();

        $status = $currency->auto_update_enabled ? 'enabled' : 'disabled';

        return redirect()->route('settings.currencies.index')
            ->with('success', "Auto-update {$status} for {$currency->name}.");
    }
}
