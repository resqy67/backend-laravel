<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookCategoryController extends Controller
{
    //
    public function index()
    {
        $bookCategories = BookCategory::latest()->get();
        return new dataResource(true, 'List of all book categories ', $bookCategories);
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

        $bookCategory = BookCategory::create([
            'book_id' => $request->book_id,
            'category_id' => $request->category_id,
        ]);
        return new dataResource(true, 'Book category created successfully', $bookCategory);
    }
}
