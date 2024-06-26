<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Book Copy
 *
 * APIs for managing book copies
 */
class BookCopyController extends Controller
{
    /**
     * Book Copy
     *
     * Get all book copies
     *
     */
    public function index()
    {
        $bookCopies = BookCopy::latest()->paginate(5);
        return new dataResource('success', 'Data retrieved successfully', $bookCopies);
    }

    /**
     * Store
     *
     * Store new book copy
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_uuid' => 'required|string',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return new dataResource('error', $validator->errors(), null);
        }

        $bookCopy = BookCopy::create([
            'book_uuid' => $request->book_uuid,
            'status' => $request->status,
        ]);

        return new dataResource('success', 'Data stored successfully', $bookCopy);

        // return new dataResource('success', 'Data stored successfully', $bookCopy);
    }
}
