<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
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
        $categories = Category::latest()->get();
        return new dataResource(true, 'List of all categories ', $categories);
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
            return new dataResource(false, $validator->errors(), null);
        }

        $category = Category::create($request->all());
        return new dataResource(true, 'Category created successfully', $category);
    }
}
