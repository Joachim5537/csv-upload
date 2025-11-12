<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'UNIQUE_KEY';
    public $incrementing = false;

    protected $fillable = [
        'UNIQUE_KEY',
        'PRODUCT_TITLE',
        'PRODUCT_DESCRIPTION',
        'STYLE#',        
        'SANMAR_MAINFRAME_COLOR',  
        'SIZE',
        'COLOR_NAME', 
        'PIECE_PRICE',
    ];

    public function getStyleHashAttribute()
    {
        return $this->attributes['STYLE#'];
    }

    public function setStyleHashAttribute($value)
    {
        $this->attributes['STYLE#'] = $value;
    }
}
