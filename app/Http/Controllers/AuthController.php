<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Redirect to login page.
     */
    public function index(): RedirectResponse
    {
        return redirect('/');
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();

        Auth::logoutOtherDevices($request->input('password'));

        DB::connection('SML')->update(
            'UPDATE dbperiode SET bulan = MONTH(GETDATE()), tahun = YEAR(GETDATE()) WHERE user_id = :user_id',
            ['user_id' => $request->input('username')]
        );

        return redirect()->intended(route('home'));
    }

    /**
     * Called by the login page on load. Legacy no-op: it used to reset every
     * user's online status, which would clear the "User sudah login" lock for
     * all users, so the body stays disabled.
     */
    public function updateIdle(): Response
    {
        return response()->noContent();
    }

    /**
     * Determine which field to use for authentication (email or username).
     */
    public function username(Request $request): string
    {
        return filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        User::where('id', Auth::id())->update([
            'status'    => 0,
            'hostid'    => '',
            'ipaddress' => '',
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}