<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()    { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt($data, true)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }
        $request->session()->regenerate();
        return redirect()->intended(route('listings.index'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'required|string|max:20',
            'role'     => 'required|in:Buyer,Seller',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Auth::login($user);
        return redirect()->route('listings.index')->with('status', 'Welcome!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('listings.index');
    }
}
