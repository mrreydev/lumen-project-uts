<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string[]
     */
    protected $fillable = ['name'];

    public $timestamps = true;

    /**
     * * Relation to Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
