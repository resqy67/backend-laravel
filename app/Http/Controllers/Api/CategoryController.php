<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResource;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

/**
 * @group Category
 *
 * APIs for managing categories
 */
class CategoryController extends Controller
{
    /**
     * Category
     *
     * Get all categories
     *
     */
    public function index()
    {
        $categories = Category::get();
        return new DataResource(true, 'List of all categories ', $categories);
    }

    /**
     * Store
     *
     * Store new category
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            // return new DataResource(false, $validator->errors(), null);
            return (new DataResource(false, 'Data not valid', $validator->errors()))->response()->setStatusCode(422);
        }

        $category = Category::create($request->all());
        return (new DataResource(true, 'Category created successfully', $category))->response()->setStatusCode(201);
    }

    /**
     * Delete
     *
     * Delete category
     */

    public function destroy($uuid)
    {
        $category = Category::where('uuid', $uuid)->first();
        if ($category) {
            $category->delete();
            return new DataResource(true, 'Category deleted successfully', null);
        }
        return (new DataResource(false, 'Category not found', null))->response()->setStatusCode(404);
    }
}
