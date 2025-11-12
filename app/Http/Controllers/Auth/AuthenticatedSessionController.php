<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LogHistory;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        LogHistory::create([
            'table_name' => 'users',
            'entity_id' => Auth::id(),
            'action' => 'login',
            'user' => Auth::id(),
            'timestamp' => Carbon::now(),
            'old_data' => null,
            'new_data' => json_encode([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]),
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            LogHistory::create([
                'table_name' => 'users',
                'entity_id' => Auth::id(),
                'action' => 'logout',
                'user' => Auth::id(),
                'timestamp' => Carbon::now(),
                'old_data' => json_encode([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]),
                'new_data' => null,
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
