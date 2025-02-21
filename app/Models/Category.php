<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, "parent_id");
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'category_brand','category_id', 'brand_id');
    }
}
