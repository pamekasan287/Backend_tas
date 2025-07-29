<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Produk",
 *     type="object",
 *     title="Produk",
 *     required={"nama", "harga", "stok"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="nama", type="string"),
 *     @OA\Property(property="harga", type="number", format="float"),
 *     @OA\Property(property="stok", type="integer"),
 *     @OA\Property(property="deskripsi", type="string"),
 *     @OA\Property(property="gambar", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Produk extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'stok',
        'deskripsi',
        'gambar',
    ];
}
