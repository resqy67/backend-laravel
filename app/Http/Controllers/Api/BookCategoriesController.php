<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\BookCategories;
use App\Http\Resources\BookCategoriesResource;

use Illuminate\Support\Facades\Validator;


class BookCategoriesController extends Controller
{
    //
    public function index()
    {
        $bookCategories = BookCategories::latest()->get();
        return new BookCategoriesResource(200, 'List of all book categories', $bookCategories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'category_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $bookCategory = BookCategories::create([
            'book_id' => $request->book_id,
            'category_id' => $request->category_id,
        ]);
        return new BookCategoriesResource(true, 'Book category created successfully', $bookCategory);
    }
}
