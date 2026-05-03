<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::withCount('listings')->get();
    }

    public function show(Category $category)
    {
        // 1-to-many in action: return a category with paginated listings
        $category->load([]);
        return [
            'category' => $category,
            'listings' => $category->listings()->with('tags')->paginate(10),
        ];
    }
}
