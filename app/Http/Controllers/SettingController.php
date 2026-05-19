<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'company_name', 'company_address', 'company_phone', 'company_email', 'company_website',
            'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number',
            'quotation_prefix', 'invoice_prefix', 'default_tax_rate', 'default_terms',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $ext  = $file->getClientOriginalExtension();
            $file->move(public_path('images'), 'company_logo.' . $ext);
            Setting::set('company_logo', 'images/company_logo.' . $ext);
        }

        return redirect()->route('settings.index')->with('success', 'Settings saved.');
    }
}
