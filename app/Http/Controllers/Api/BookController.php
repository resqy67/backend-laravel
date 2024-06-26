<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Http\Resources\dataResource;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::latest()->paginate(5);
        return new dataResource('success', 'Data retrieved successfully', $books);
    }

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
            'filePdf' => 'required|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return new dataResource('error', $validator->errors(), null);
        }

        $image = $request->file('image');
        $image->storeAs('public/books/images', $image->hashName());

        $filePdf = $request->file('filePdf');
        $filePdf->storeAs('public/books/pdfs', $filePdf->hashName());

        $book = Book::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'year' => $request->year,
            'pages' => $request->pages,
            'image' => $image->hashName(),
            'filePdf' => $filePdf->hashName(),
        ]);

        return new dataResource('success', 'Data stored successfully', $book);
    }
}
