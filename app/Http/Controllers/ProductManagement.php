<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ProductManagement extends Controller
{
    const MAX_IMAGE_SIZE_KB = 8192;

    public function store(Request $req)
    {
        $data = $req->all();
        $validation = Validator::make($data, [
            'name' => 'required|max:40',
            'description' => 'required|max:255',
            'categories_id' => 'required|array|exists:categories,id',
            'image' => 'mimes:jpeg, jpg, bmp, png, gif, svg|max:' . self::MAX_IMAGE_SIZE_KB
        ]);
        if (isset($data['id'])) {
            if ($validation->fails()) {
                return response()->json(['message' => 'Invalid data', 'errors' => $validation->errors()], 422);
            }
            if (isset($data['image'])) {
                $image = $data['image'];
                $path = $image->store('/public/' . $req->user()->id);
                $product = Product::with('categories')->findOrFail($data['id']);
                $oldImg = $product->first()->image;
                $product->update(['image' => $path]);
                if (Storage::exists($oldImg)) {
                    Storage::delete($oldImg);
                }
            }
            /*Product::where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'],
            ])->categories()->attach($data['categories_id']);*/
            $product = Product::whereId($data['id'])->first();
            $product->categories()->detach();
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);
            $product->categories()->attach($data['categories_id']);
            $product->categories;

            return response()->json(['message' => 'Successful editing', 'product' => $product], 200);
        } else {
            $validation->addRules(['image' => 'required|mimes:jpeg, jpg, bmp, png, gif, svg|max:' . self::MAX_IMAGE_SIZE_KB]);//disallow default image for null - comment if necessary
            if ($validation->fails()) {
                //return back()->withErrors($validation->errors())->withInput($data);
                return response()->json(['message' => 'Invalid data', 'errors' => $validation->errors()], 422);
            }
            if (isset($data['image'])) {
                $image = $data['image'];
//                $path = 'default.png';
                $path = $image->store('/public/' . $req->user()->id);
            } else {
                $path = 'default.png';
            }
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $path
            ])->categories()->attach($data['categories_id']);
            $product->categories;

            return response()->json(['message' => 'Successful adding', 'product' => $product], 200);
        }
    }
    public function delete(Request $req)
    {
        $id = $req->id;
        $product = Product::findOrFail($id);
        $product
            ->categories()
            ->detach();
        $product
            ->delete();
        return response()->json(['message' => 'Successful deleting'], 200);

    }




}
