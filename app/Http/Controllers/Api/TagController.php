<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        return Tag::withCount('listings')->get();
    }

    public function show(Tag $tag)
    {
        // Many-to-many in action
        return [
            'tag'      => $tag,
            'listings' => $tag->listings()->with(['seller:id,name','category'])->paginate(10),
        ];
    }
}
