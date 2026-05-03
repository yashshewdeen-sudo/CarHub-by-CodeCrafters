<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function upgradeSeller(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'Buyer') {
            return back()->with('error', 'Only buyers can upgrade to seller.');
        }

        $user->update(['role' => 'Seller']);

        return back()->with('status', 'Your account has been upgraded to Seller!');
    }
}