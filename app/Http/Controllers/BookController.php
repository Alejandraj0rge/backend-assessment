<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return Book::with('stores')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'isbn' => 'required|numeric',
            'value' => 'required|numeric'
        ]);

        $book = Book::create($validated);
        return response()->json($book, 201);
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'isbn' => 'sometimes|numeric',
            'value' => 'sometimes|numeric'
        ]);

        $book->update($validated);
        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(null, 204);
    }
}