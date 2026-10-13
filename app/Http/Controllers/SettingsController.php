<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:ar,en'],
            'theme' => ['required', 'in:light,dark'],
            'brand_color' => [
                'required',
                'in:green,blue,purple,orange,rose,teal,indigo,amber',
            ],
        ]);

        $request->user()->update($validated);

return redirect()
    ->route('settings.edit')
    ->with(
        'success',
        $validated['locale'] === 'en'
            ? 'Settings saved successfully.'
            : 'تم حفظ الإعدادات بنجاح.'
    );

    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $request->user()->update([
            'theme' => $validated['theme'],
        ]);

        return response()->json([
            'success' => true,
            'theme' => $validated['theme'],
        ]);
    }
}
