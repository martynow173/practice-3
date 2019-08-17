<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Product;
use App\Comment;


class ProductBrowsing extends Controller
{
    const PRODS_PER_PAGE = 2;



    public function showList(Request $req) {
        $categoryId = $req->get('category_id');
        $sortBy = $req->get('sortBy');
        $sortMethod = $req->get('sortMethod');
        $categories = Category::all();
        $page = $req->get('page');

        if ($page == null) {
            $page = 1;
        }
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
                $commentsNumber = $product->comments()->count();
                $product->averageRate = $avs;
                $product->commentsNumber = $commentsNumber;
            }
//            $products = $products->ToArray();
            if ($sortMethod != null && $sortBy != null ) {
                if ($sortMethod == 'ascent') {
                    $products = $products->sortBy('id');
                } else {
                    $products = $products->sortByDesc('id');
                }
            }
            $pageProducts = array();
            for ($i = ($page - 1) * self::PRODS_PER_PAGE ; $i < $page * self::PRODS_PER_PAGE && $i < sizeof($products); $i++) {
                $pageProducts[$i] = $products[$i];
            }
        }
        return response()->json(['products' =>  $pageProducts], 200);
    }


    public function showFull($id) {
        $product = Product::with('categories', 'comments')->findOrFail($id);
        $averageRate = $product->comments()->avg('comments.rate');
        return response()->json(['product' =>  $product, 'averageRate' =>  $averageRate],  200);
    }



}
