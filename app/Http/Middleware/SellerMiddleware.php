<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['Seller', 'Admin'])) {
            abort(403, 'Sellers only.');
        }
        return $next($request);
    }
}