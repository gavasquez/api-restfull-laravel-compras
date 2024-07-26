<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "nombre",
        "descripcion",
    ];

    public function productos(){ // Una marca puede tener muchos productos asociados.
        return $this->hasMany(Producto::class);
    }
}
