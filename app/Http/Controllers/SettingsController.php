<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\AuditService;

class SettingsController extends Controller
{
    private const SETTING_DEFINITIONS = [
        'check_in_time' => ['label' => 'Check-in time', 'type' => 'time', 'default' => '14:00'],
        'check_out_time' => ['label' => 'Check-out time', 'type' => 'time', 'default' => '10:00'],
        'currency' => ['label' => 'Currency', 'type' => 'text', 'default' => 'KES'],
        'timezone' => ['label' => 'Timezone', 'type' => 'text', 'default' => 'Africa/Nairobi'],
        'tax_rate' => ['label' => 'Tax rate (%)', 'type' => 'number', 'default' => '16.00'],
    ];

    public function index()
    {
        $this->authorize('settings.view');

        $branchId = auth()->user()->branch_id;
        $stored = Setting::where('branch_id', $branchId)
            ->whereIn('setting_key', array_keys(self::SETTING_DEFINITIONS))
            ->pluck('setting_value', 'setting_key');

        $settings = collect(self::SETTING_DEFINITIONS)->mapWithKeys(
            fn (array $definition, string $key) => [$key => $stored->get($key, $definition['default'])]
        );

        return view('settings.index', [
            'settings' => $settings,
            'definitions' => self::SETTING_DEFINITIONS,
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('settings.manage');

        $validated = $request->validate([
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
            'currency' => ['required', 'string', 'size:3', 'uppercase'],
            'timezone' => ['required', 'timezone'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $branchId = auth()->user()->branch_id;
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['branch_id' => $branchId, 'setting_key' => $key],
                [
                    'setting_value' => (string) $value,
                    'setting_type' => 'string',
                    'is_public' => false,
                ]
            );
        }
        AuditService::log('SETTINGS_UPDATED', null, null, ['keys' => array_keys($validated)], $branchId);

        return back()->with('success', 'System settings updated successfully.');
    }
}
