<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Tag;
use Illuminate\Http\Request;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::query()
            ->with(['seller:id,name', 'category', 'tags', 'images'])
            ->when(!$request->status || $request->status === 'both',
                fn($q) => $q->whereIn('status', ['Active', 'Sold']))
            ->when($request->status && $request->status !== 'both',
                fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($x) => $x->where('make', 'like', "%$s%")
                                        ->orWhere('model', 'like', "%$s%")))
            ->when($request->make,         fn($q, $v) => $q->where('make', $v))
            ->when($request->year_from,    fn($q, $v) => $q->where('year', '>=', $v))
            ->when($request->year_to,      fn($q, $v) => $q->where('year', '<=', $v))
            ->when($request->price_min,    fn($q, $v) => $q->where('price', '>=', $v))
            ->when($request->price_max,    fn($q, $v) => $q->where('price', '<=', $v))
            ->when($request->fuel_type,    fn($q, $v) => $q->where('fuel_type', $v))
            ->when($request->transmission, fn($q, $v) => $q->where('transmission', $v))
            ->when($request->mileage_min, fn($q, $v) => $q->where('mileage', '>=', $v))
            ->when($request->mileage_max, fn($q, $v) => $q->where('mileage', '<=', $v))
            ->when($request->tags,         fn($q, $v) =>
                $q->whereHas('tags', fn($t) => $t->whereIn('tags.id', (array) $v)))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $tags  = Tag::all();
        $makes = Listing::select('make')->distinct()->orderBy('make')->pluck('make');
        $years = Listing::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $categories = Category::all();

        return view('listings.index', compact('listings', 'categories', 'tags', 'makes', 'years'));
    }

    public function show(Listing $listing)
{
        $listing->load(['seller','category','tags','images']);
        return view('listings.show', compact('listing'));
}

    public function create()
    {
        return view('listings.create', [
            'categories' => Category::all(),
            'tags'       => Tag::all(),
        ]);
    }

    public function store(StoreListingRequest $request)
    {
        $data = $request->validated();
        $data['seller_id'] = $request->user()->id;
        $data['status'] = auth()->user()->isAdmin() ? 'Active' : 'Pending';
        $listing = Listing::create($data);
        if (!empty($data['tags'])) $listing->tags()->sync($data['tags']);

        if ($request->hasFile('images')) {
            $coverIndex = (int) $request->input('cover_index', 0);
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('listings', 'public');
                $listing->images()->create([
                    'path' => $path,
                    'is_main' => $i === $coverIndex
                ]);
            }       
        }
        
        return redirect()->route('listings.show', $listing)
            ->with('status', 'Listing submitted, pending approval.');
    }

    public function destroy(Listing $listing, Request $request)
    {
        if (!$request->user()->isAdmin() && $listing->seller_id !== $request->user()->id) {
            abort(403);
        }
        $listing->delete();
        return redirect()->route('listings.index')->with('status', 'Listing deleted.');
    }

    public function markSold(Listing $listing, Request $request)
    {
        if ($listing->seller_id !== $request->user()->id) {
            abort(403);
        }
        if ($listing->status !== 'Active') {
            return back()->with('error', 'You can only mark an approved listing as sold.');
        }
        $listing->update(['status' => 'Sold']);
        return back()->with('status', 'Listing marked as sold!');
    }

    public function ajaxSearch(Request $request)
    {
        $listings = Listing::query()
            ->with(['tags', 'images'])
            ->whereIn('status', ['Active', 'Sold'])
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($x) => $x->where('make', 'like', "%$s%")
                                    ->orWhere('model', 'like', "%$s%")))
            ->latest()
            ->take(9)
            ->get()
            ->map(fn($car) => [
                'id'           => $car->id,
                'title'        => $car->year . ' ' . $car->make . ' ' . $car->model,
                'price'        => 'Rs ' . number_format($car->price),
                'mileage'      => number_format($car->mileage) . ' km',
                'fuel_type'    => $car->fuel_type,
                'transmission' => $car->transmission,
                'status'       => $car->status,
                'url'          => route('listings.show', $car),
                'image'        => $car->images->firstWhere('is_main', 1)
                                    ? asset('storage/' . $car->images->firstWhere('is_main', 1)->path)
                                    : ($car->images->first()
                                        ? asset('storage/' . $car->images->first()->path)
                                        : asset('images/car' . (($car->id % 3) + 1) . '.jpg')),
                'tags'         => $car->tags->pluck('name'),
            ]);

        return response()->json($listings);
    }

    // Produce JSON file + validate against schema at creation time
public function exportJson(Listing $listing)
{
    $listing->load(['seller:id,name', 'tags']);

    // Build the data
    $data = [
        'id'           => $listing->id,
        'title'        => $listing->year . ' ' . $listing->make . ' ' . $listing->model,
        'price'        => (float) $listing->price,
        'year'         => (int) $listing->year,
        'make'         => $listing->make,
        'model'        => $listing->model,
        'mileage'      => (int) $listing->mileage,
        'fuel_type'    => $listing->fuel_type,
        'transmission' => $listing->transmission,
        'condition'    => $listing->condition_status,
        'status'       => $listing->status,
        'description'  => $listing->description,
        'seller'       => [
            'id'   => $listing->seller->id,
            'name' => $listing->seller->name,
        ],
        'tags'        => $listing->tags->pluck('name')->toArray(),
        'exported_at' => now()->toIso8601String(),
    ];

    // Validate against schema BEFORE saving (creation time validation)
    $schema = json_decode(file_get_contents(public_path('json/listing-schema.json')));
    $dataObj = json_decode(json_encode($data));

    $validator = new Validator();
    $validator->validate($dataObj, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

    if (!$validator->isValid()) {
        $errors = collect($validator->getErrors())->pluck('message')->join(', ');
        return back()->with('error', 'JSON Schema validation failed at creation: ' . $errors);
    }

    // Save the JSON file
    $filename = 'listing-' . $listing->id . '.json';
    $path = public_path('json/' . $filename);
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

    return redirect()->route('listings.json.view', $listing)
        ->with('status', 'JSON exported and validated successfully!');
}

// Consume JSON file in PHP + validate at consumption time
public function viewJson(Listing $listing)
{
    $filename = 'listing-' . $listing->id . '.json';
    $path = public_path('json/' . $filename);

    if (!file_exists($path)) {
        return back()->with('error', 'No JSON export found for this listing. Please export it first.');
    }

    // Read the JSON file (PHP consumption)
    $jsonContent = file_get_contents($path);
    $data = json_decode($jsonContent, true);

    // Validate against schema at CONSUMPTION time
    $schema = json_decode(file_get_contents(public_path('json/listing-schema.json')));
    $dataObj = json_decode($jsonContent);

    $validator = new Validator();
    $validator->validate($dataObj, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

    $validationErrors = [];
    if (!$validator->isValid()) {
        $validationErrors = $validator->getErrors();
    }

    return view('listings.json-view', compact('data', 'listing', 'validationErrors', 'filename'));
}
}
