<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Product;
use App\Comment;

class ProductBrowsing extends Controller
{
    const PRODS_PER_PAGE = 6;

    public function showList(Request $req) {

        dd(Auth()->user()->getPermissionsViaRoles());

        $categoryId = $req->get('category_id');
        $sortBy = $req->get('sortBy');
        $sortMethod = $req->get('sortMethod');
        $categories = Category::all();
        if (!isset($sortBy)) {
            $sortBy = 'created_at';
            $sortMethod = 'desc';
        }

        if ($categoryId != null) {

            $products = Product::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
                ->orderBy($sortBy, $sortMethod)
                ->paginate(self::PRODS_PER_PAGE);
            $products->appends(['category_id' => $categoryId])->links();
        } else {
            $products = Product::with(['categories', 'comments:product_id,rate'])->get();
            foreach ($products as $product) {
                $avs = $product->comments()->avg('comments.rate');
                $product->averageRate = $avs;
            }


//                ->orderBy($sortBy, $sortMethod)
//                ->paginate(self::PRODS_PER_PAGE);



//            dd(Product::find(1)->commentCount);



//            foreach ($products as $prod) {
//                dd($prod->comments[1]);
//            }
        }
        return response()->json(['products' =>  $products], 200);
    }

    public function showFull($id) {
        $product = Product::with('categories', 'comments')->findOrFail($id);
        $averageRate = $product->comments()->avg('comments.rate');
        return response()->json(['product' =>  $product, 'averageRate' =>  $averageRate],  200);
    }



}
