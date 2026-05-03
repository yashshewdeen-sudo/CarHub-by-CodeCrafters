<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /** Many-to-many: a tag (e.g. "Sport", "Imported", "Hybrid") belongs to many listings. */
    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_tag');
    }
}
