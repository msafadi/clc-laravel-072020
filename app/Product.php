<?php

namespace App;

use App\Scopes\QuantityScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'category_id', 'description', 'image',
        'price', 'quantity',
    ];

    public function category()
    {
        
        return $this->belongsTo(
            Category::class,    // Realted model
            'category_id', // Foreign key in the current table
            'id'   // Primary key in the realted model
        )->withDefault([
            'name' => 'No Category'
        ]);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,         // Realted model
            'products_tags',    // Pivot table
            'product_id',       // Forigen key in the pivot table
            'tag_id',           // Related key in the pivot table
            'id',               // Primary key
            'id'                // Related primary key
        );
    }

    public function desc()
    {
        return $this->hasOne(
            ProductDescription::class,
            'product_id',
            'id'
        )->withDefault();
    }

    public function scopeWithImages(Builder $query)
    {
        $query->whereNotNull('image');
        // WHERE image IS NOT NULL
    }

    public function scopePrice(Builder $query, $price1, $price2 = null)
    {
        $query->where('price', '>=', $price1);
        if ($price2 !== null) {
            $query->where('price', '<=', $price2);
        }
    }

    protected static function booted()
    {
        /*static::addGlobalScope('quantity', function(Builder $query) {
            $query->where('quantity', '>', 0);
        });*/
        //static::addGlobalScope(new QuantityScope);

        static::forceDeleted(function($product) {
            Storage::disk('public')->delete($product->image);
        });
    }

    public function scopeFilter($query, $filters = [])
    {
        $defaults = [
            'name' => null,
            'category_id' => null,
        ];
        $filters = array_merge($defaults, $filters);

        $query->when($filters['name'], function($query, $name) {
            return $query->where('name', 'LIKE', "%$name%");
        })
        ->when($filters['category_id'], function($query, $category_id) {
            return $query->where('category_id', $category_id);
        });
    }
}
