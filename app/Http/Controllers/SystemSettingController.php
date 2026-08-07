<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\SystemSetting;
use App\Support\KabupatenResourceGuard;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function editSupport()
    {
        KabupatenResourceGuard::requireAdmin(auth()->user());

        $settings = SystemSetting::current();

        return view('admin.settings.support', [
            'settings' => $settings,
            'defaults' => [
                'phone' => config('app.kanwil.phone'),
                'email' => config('app.kanwil.email'),
            ],
        ]);
    }

    public function updateSupport(Request $request)
    {
        KabupatenResourceGuard::requireAdmin(auth()->user());

        ValidationHelper::validate($request, [
            'support_phone' => ValidationHelper::teleponRules(),
            'support_email' => 'required|email|max:255',
        ]);

        $settings = SystemSetting::query()->first() ?? new SystemSetting();
        $settings->support_phone = $request->input('support_phone');
        $settings->support_email = $request->input('support_email');
        $settings->save();

        SystemSetting::resetCache();

        return redirect()
            ->route('settings.support.edit')
            ->with('success', 'Kontak support berhasil disimpan.');
    }
}
