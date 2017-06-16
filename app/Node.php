<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = ['name', 'address'];

    public function scopeName($query, $name)
    {
        return $query->where('name', $name);
    }
}
