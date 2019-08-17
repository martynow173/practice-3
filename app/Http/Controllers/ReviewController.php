<?php

namespace App\Http\Controllers;


use App\Comment;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    const RATES = [0, 1, 2, 3, 4, 5];




    public function save(Request $req) {
        $data = $req->all();
        $data['rates'] = self::RATES;
//        $userId = $req->user()->id;
        $validation = Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'title' => 'required|max:50',
            'description' => 'required|max:255',
            'rate' => 'required|in_array:rates.*'
        ]);
        if ($validation->fails()) {
            return response()->json(['message' => 'Incorrect input', 'errors' => $validation->errors()], 422);
        } else {
            $review = Comment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'rate' => $data['rate'],
                'user_id' => auth('api')->user()->id,
                'product_id' => $data['product_id']
            ]);
            return response()->json(['message' => 'Your review is saved with id:', 'id' => $review->id], 200);
        }


    }
    public function delete(Request $req) {
        $id = $req['id'];
        Comment::findOrFail($id)->delete();
        return response()->json(['message' => 'Successful review deleting'], 200);
    }
}
