<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Http\Resources\ProdukResource;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;

class ProdukController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/produk",
     *     tags={"Produk"},
     *     summary="Tampilkan semua produk",
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Produk")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return ProdukResource::collection(Produk::latest()->get());
    }

    /**
     * @OA\Post(
     *     path="/api/produk",
     *     tags={"Produk"},
     *     summary="Buat produk baru",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nama", "harga", "stok"},
     *                 @OA\Property(property="nama", type="string"),
     *                 @OA\Property(property="harga", type="number", format="float"),
     *                 @OA\Property(property="stok", type="integer"),
     *                 @OA\Property(property="deskripsi", type="string"),
     *                 @OA\Property(property="gambar", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produk berhasil dibuat"
     *     )
     * )
     */
    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();

        // Buat dulu produk TANPA gambar agar dapat ID-nya
        $produk = Produk::create(array_diff_key($data, ['gambar' => '']));

        if ($request->hasFile('gambar')) {
            $ext = $request->file('gambar')->getClientOriginalExtension();
            $filename = $produk->id . '.' . $ext;
            $path = $request->file('gambar')->storeAs('produk', $filename, 'public');
            $produk->update(['gambar' => $filename]);
        }

        return new ProdukResource($produk);
    }


    /**
     * @OA\Get(
     *     path="/api/produk/{id}",
     *     tags={"Produk"},
     *     summary="Ambil detail produk",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail produk ditemukan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produk tidak ditemukan"
     *     )
     * )
     */
    public function show(Produk $produk)
    {
        return new ProdukResource($produk);
    }

    /**
     * @OA\Put(
     *     path="/api/produk/{id}",
     *     tags={"Produk"},
     *     summary="Perbarui produk",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nama", type="string"),
     *                 @OA\Property(property="harga", type="number", format="float"),
     *                 @OA\Property(property="stok", type="integer"),
     *                 @OA\Property(property="deskripsi", type="string"),
     *                 @OA\Property(property="gambar", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produk berhasil diperbarui"
     *     )
     * )
     */
    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete('produk/' . $produk->gambar);
            }

            $ext = $request->file('gambar')->getClientOriginalExtension();
            $filename = $produk->id . '.' . $ext;
            $path = $request->file('gambar')->storeAs('produk', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $produk->update($data);
        return new ProdukResource($produk);
    }


    /**
     * @OA\Delete(
     *     path="/api/produk/{id}",
     *     tags={"Produk"},
     *     summary="Hapus produk (soft delete)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produk berhasil dihapus"
     *     )
     * )
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
