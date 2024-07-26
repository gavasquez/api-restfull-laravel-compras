<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "subtotal",
        "total",
    ];

    public function productos(){ // una compra puede pertenecer a varias instancias de Producto y viceversa
        return $this->belongsToMany(Producto::class)->withPivot("precio", 'cantidad', 'subtotal')->withTimestamps();
    }
}