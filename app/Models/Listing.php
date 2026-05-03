<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'category_id', 'make', 'model', 'year', 'mileage',
        'price', 'fuel_type', 'transmission', 'condition_status',
        'description', 'status',
    ];

    protected $casts = [
        'year'    => 'integer',
        'mileage' => 'integer',
        'price'   => 'decimal:2',
    ];

    /** Inverse of one-to-many (User -> Listing). */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /** Inverse of one-to-many (Category -> Listing). */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Many-to-many (Listing <-> Tag) via pivot listing_tag. */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'listing_tag');
    }

    public function images() {
        return $this->hasMany(ListingImage::class);
    }
}
