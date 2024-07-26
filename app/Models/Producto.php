<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "nombre",
        "descripcion",
        "precio",
        "cantidad_disponible",
        "categoria_id",
        "marca_id"
    ];

    public function categoria(){ // Un producto pertence a una unica categoria
        return $this->belongsTo(Categoria::class);
    }

    public function marca(){ // un producto pertenece a una unica marca
        return $this->belongsTo(Marca::class);
    }

    public function compra(){ // un prodcuto puede pertenecer a varios instancias de compra y viceversa
        return $this->belongsToMany(Compra::class)->withPivot("precio", "cantidad", "subtotal")->withTimestamps();
    }

}
