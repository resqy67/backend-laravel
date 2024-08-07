<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResource;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookCategoryController extends Controller
{
    /**
     * Book Category
     *
     * Get all book categories
     *
     */
    public function index()
    {
        // $bookCategories = BookCategory::latest()->get();
        // $bookCategories = BookCategory::get()->paginate(5);
        $bookCategories = BookCategory::with('book')->simplePaginate(20);
        return new DataResource(true, 'List of all book categories ', $bookCategories);
    }

    /**
     * Store
     *
     * Store new book category
     *
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_uuid' => 'required|exists:books,uuid',
            'category_uuid' => 'required|exists:categories,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $bookCategory = BookCategory::create([
            'book_uuid' => $request->book_uuid,
            'category_uuid' => $request->category_uuid,
        ]);
        return new DataResource(true, 'Book category created successfully', $bookCategory);
    }

    /**
     * delete book category
     *
     * delete book category by uuid
     */
    public function destroy($uuid)
    {
        $bookCategory = BookCategory::where('uuid', $uuid)->first();
        if ($bookCategory) {
            $bookCategory->delete();
            return new DataResource(true, 'Book category deleted successfully', null);
        }
        return new DataResource(false, 'Book category not found', null);
    }
}
