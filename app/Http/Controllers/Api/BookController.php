<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Http\Resources\BookResource;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::latest()->paginate(5);
        return new BookResource(true, 'List of all books', $books);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required',
            'description' => 'required',
            'coverImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'filePdf' => 'required|mimes:pdf|max:2048',
            'publisher' => 'required',
            'isbn' => 'required',
            'year' => 'required',
            'pages' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // $avatarPath = $request->file('avatar')->store('users/avatars', 'public');


        $coverImagePath = $request->file('coverImage')->store('books/images', 'public');
        $filePdfPath = $request->file('filePdf')->store('books/pdfs', 'public');


        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'coverImage' => $coverImagePath,
            'filePdf' =>  $filePdfPath,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'year' => $request->year,
            'pages' => $request->pages,
        ]);
        return new BookResource(true, 'Book created successfully', $book);
    }
}
