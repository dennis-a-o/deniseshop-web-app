<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    /**
     * Get the comments for the blog post.
     */
    public function orderItem(): HasMany
    {
        return $this->hasMany(orderItem::class);
    }
}
