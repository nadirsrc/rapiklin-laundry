<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request by verifying the user is active and holds an allowed role.
     *
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi Admin.',
            ]);
        }

        if ($roles !== [] && ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman atau melakukan aksi ini.');
        }

        return $next($request);
    }
}
