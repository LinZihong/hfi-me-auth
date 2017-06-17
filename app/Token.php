<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['value'];

    public function __construct(array $attributes = [])
    {
        $attributes['value'] = bin2hex(openssl_random_pseudo_bytes(32));
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeValue($query, $value)
    {
        return $query->where('value', $value);
    }
}
