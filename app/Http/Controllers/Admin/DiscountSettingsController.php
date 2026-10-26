<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class DiscountSettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            'discount_percentage' => (int) SiteSetting::getValue('black_friday.discount_percentage', config('discounts.black_friday.discount_percentage', 30)),
            'month' => (int) SiteSetting::getValue('black_friday.month', config('discounts.black_friday.month', 11)),
            'start_day' => (int) SiteSetting::getValue('black_friday.start_day', config('discounts.black_friday.start_day', 25)),
            'end_day' => (int) SiteSetting::getValue('black_friday.end_day', config('discounts.black_friday.end_day', 30)),
            'force_active' => (int) SiteSetting::getValue('black_friday.force_active', 0),
        ];

        return view('admin.settings.discount', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'discount_percentage' => 'required|integer|min:1|max:90',
            'month' => 'required|integer|min:1|max:12',
            'start_day' => 'required|integer|min:1|max:31',
            'end_day' => 'required|integer|min:1|max:31|gte:start_day',
        ]);

        SiteSetting::setValue('black_friday.discount_percentage', $data['discount_percentage']);
        SiteSetting::setValue('black_friday.month', $data['month']);
        SiteSetting::setValue('black_friday.start_day', $data['start_day']);
        SiteSetting::setValue('black_friday.end_day', $data['end_day']);
        // Respect schedule only: Black Friday applies on configured date window.
        SiteSetting::setValue('black_friday.force_active', 0);

        return back()->with('success', 'Black Friday settings updated successfully.');
    }
}
