<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    use HasFactory;
    
    protected $table = 'media_folders';
    public $timestamps = true;
}
