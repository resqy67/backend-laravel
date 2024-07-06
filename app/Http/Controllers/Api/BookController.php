<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Book
     *
     * Get all books
     *
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $books = Book::with('categories')->latest()->simplePaginate($perPage, ['*'], 'page', $page);
        return new DataResource('success', 'Data retrieved successfully', $books);
    }

    /**
     * Store
     *
     * Store new book
     *
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'isbn' => 'required|string',
            'year' => 'required|integer',
            'pages' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'filepdf' => 'required|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return new DataResource('error', $validator->errors(), null);
        }

        $image = $request->file('image');
        $image->storeAs('public/books/images', $image->hashName());

        $filepdf = $request->file('filepdf');
        $filepdf->storeAs('public/books/pdfs', $filepdf->hashName());

        $book = Book::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'year' => $request->year,
            'pages' => $request->pages,
            'image' => $image->hashName(),
            'filepdf' => $filepdf->hashName(),
        ]);

        return new DataResource('success', 'Data stored successfully', $book);
    }

    /**
     * Show
     *
     * Show book by uuid
     *
     */
    public function show($uuid)
    {
        $book = Book::with('categories')->where('uuid', $uuid)->first();
        return new DataResource('success', 'Data retrieved successfully', $book);
    }
}
