<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopSettingController extends Controller
{
    public function edit()
    {
        $settings = ShopSetting::first();
        return Inertia::render('Admin/Settings', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_number' => ['required', 'string', 'regex:/^628\d{8,12}$/'], // starts with 628, followed by 8 to 12 digits
            'whatsapp_template' => 'required|string',
        ], [
            'whatsapp_number.regex' => 'Nomor WhatsApp wajib menggunakan kode negara (contoh: 6281234567890).'
        ]);

        $settings = ShopSetting::first();
        if (!$settings) {
            $settings = new ShopSetting();
        }

        $settings->whatsapp_number = $request->whatsapp_number;
        $settings->whatsapp_template = $request->whatsapp_template;
        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
