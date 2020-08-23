<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
}
