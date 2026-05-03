<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function listings()
    {
        $listings = Listing::with(['seller', 'category'])
            ->whereIn('status', ['Pending', 'Active', 'Rejected'])
            ->latest()
            ->paginate(15);

        return view('admin.listings', compact('listings'));
    }

    public function approve(Listing $listing)
    {
        $listing->update(['status' => 'Active']);
        return back()->with('status', 'Listing approved!');
    }

    public function reject(Listing $listing)
    {
        $listing->update(['status' => 'Rejected']);
        return back()->with('status', 'Listing rejected.');
    }
}