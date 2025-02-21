<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';
    protected $fillable = ['user_id','name', 'email','phone','country','state','city','address','zip_code','type','default'];
    public $timestamps = true;
}
