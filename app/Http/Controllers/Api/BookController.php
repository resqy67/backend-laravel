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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'filepdf' => 'required|mimes:pdf|max:102400',
            'availability' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return (new DataResource('error', 'data tidak valid', $validator->errors()))->response()->setStatusCode(422);
            // return new DataResource('error', $validator->errors(), $validator->errors());
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
            'availability' => $request->availability,
            'image' => 'books/images' . $image->hashName(),
            'filepdf' => 'books/pdfs' . $filepdf->hashName(),
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

    /**
     * Update
     *
     * Update book by uuid
     *
     */
    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'isbn' => 'required|string',
            'year' => 'required|integer',
            'pages' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'filepdf' => 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return (new DataResource('error', 'data tidak valid', $validator->errors()))->response()->setStatusCode(422);
            // return new DataResource('error', $validator->errors(), null);
        }

        $book = Book::where('uuid', $uuid)->first();
        $image = $request->file('image');
        $image->storeAs('public/books/images', $image->hashName());

        $filepdf = $request->file('filepdf');
        $filepdf->storeAs('public/books/pdfs', $filepdf->hashName());

        $book->update([
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

        return new DataResource('success', 'Data updated successfully', $book);
    }

    /**
     * Destroy
     *
     * Delete book by uuid
     *
     */
    public function destroy($uuid)
    {
        $book = Book::where('uuid', $uuid)->first();

        if (!$book) {
            return (new DataResource('error', 'Invalid UUID', ''))->response()->setStatusCode(404);
        }

        $book->delete();
        return new DataResource('success', 'Data deleted successfully', $book);
    }

    /**
     * Search
     *
     * Search book by keyword
     *
     */
    public function search(Request $request)
    {
        // $perPage = $request->input('per_page', 10);
        // $page = $request->input('page', 1);
        $keyword = $request->keyword;

        $query = Book::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('author', 'like', '%' . $keyword . '%')
                    ->orWhere('publisher', 'like', '%' . $keyword . '%')
                    ->orWhereHas('categories', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }
        $books = $query->get();
        return new DataResource('success', 'Data retrieved successfully', $books);
    }
}
