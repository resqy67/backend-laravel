<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookCopyController extends Controller
{
    //
    public function index()
    {
        $bookCopies = BookCopy::latest()->paginate(5);
        return new dataResource('success', 'Data retrieved successfully', $bookCopies);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return new dataResource('error', $validator->errors(), null);
        }

        $bookCopy = BookCopy::create([
            'status' => $request->status,
        ]);

        return new dataResource('success', 'Data stored successfully', $bookCopy);

        // return new dataResource('success', 'Data stored successfully', $bookCopy);
    }
}
