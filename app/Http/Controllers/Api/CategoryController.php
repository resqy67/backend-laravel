<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categories;
use App\Http\Resources\CategoriesResource;

use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Categories::latest()->get();
        return new CategoriesResource(200, 'List of all categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CategoryName' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $category = Categories::create([
            'CategoryName' => $request->CategoryName,
        ]);
        return new CategoriesResource(true, 'Category created successfully', $category);
    }
}
