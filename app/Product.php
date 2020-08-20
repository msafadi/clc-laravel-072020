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
}
