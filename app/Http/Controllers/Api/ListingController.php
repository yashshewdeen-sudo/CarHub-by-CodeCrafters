<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /** GET /api/listings — paginated, with filters and eager-loaded relations. */
    public function index(Request $request)
    {
        $q = Listing::query()
            ->with(['seller:id,name', 'category', 'tags'])
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($x) => $x->where('make', 'like', "%$s%")
                                       ->orWhere('model', 'like', "%$s%")))
            ->when($request->fuel_type,    fn ($q, $f) => $q->where('fuel_type', $f))
            ->when($request->transmission, fn ($q, $t) => $q->where('transmission', $t))
            ->when($request->category_id,  fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->min_price,    fn ($q, $p) => $q->where('price', '>=', $p))
            ->when($request->max_price,    fn ($q, $p) => $q->where('price', '<=', $p))
            ->when($request->status,       fn ($q, $s) => $q->where('status', $s),
                                          fn ($q)     => $q->where('status', 'Active'))
            ->when($request->tag_id, function ($q, $tagId) {
                $q->whereHas('tags', fn ($x) => $x->where('tags.id', $tagId));
            })
            ->latest();

        return ListingResource::collection($q->paginate($request->per_page ?? 10));
    }

    /** GET /api/listings/{listing} */
    public function show(Listing $listing)
    {
        $listing->load(['seller:id,name,email', 'category', 'tags']);
        return new ListingResource($listing);
    }

    /** POST /api/listings */
    public function store(StoreListingRequest $request)
    {
        $data = $request->validated();
        $data['seller_id'] = $request->user()->id;
        $data['status']    = 'Pending';

        $listing = Listing::create($data);
        if (!empty($data['tags'])) {
            $listing->tags()->sync($data['tags']);
        }
        $listing->load(['seller:id,name', 'category', 'tags']);

        return (new ListingResource($listing))
            ->response()
            ->setStatusCode(201);
    }

    /** PUT /api/listings/{listing} */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        if (!$request->user()->isAdmin() && $listing->seller_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validated();
        $listing->update($data);
        if (array_key_exists('tags', $data)) {
            $listing->tags()->sync($data['tags'] ?? []);
        }
        $listing->load(['seller:id,name', 'category', 'tags']);
        return new ListingResource($listing);
    }

    /** DELETE /api/listings/{listing} */
    public function destroy(Request $request, Listing $listing)
    {
        if (!$request->user()->isAdmin() && $listing->seller_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $listing->delete();
        return response()->json(['message' => 'Listing deleted.']);
    }
}
