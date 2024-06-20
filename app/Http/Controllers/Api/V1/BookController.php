<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\V1\BookResource;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
public function __construct()
{
  $this->authorizeResource(Book::class, 'book');
}

  public function index()
  {
    $books = Book::where('team_id', auth()->user()->currentTeam->id)->get();
    return BookResource::collection($books);
  }

  public function show(Book $book)
  {
    return BookResource::make($book);
  }

  public function store(StoreBookRequest $request)
  {
    $validated = $request->validated();
    $validated['team_id'] = auth()->user()->currentTeam->id;

    return BookResource::make(Book::create($validated));
  }

  public function update(UpdateBookRequest $request, Book $book)
  {
    $validated = $request->validated();

    $book->update($validated);

    return response()->json([
      'data' => BookResource::make($book),
      'message' => 'Book updated'
    ], Response::HTTP_OK);
  }

  public function destroy(Book $book)
  {
    $book->delete();

    return response()->json([
      'message' => 'Book deleted'
    ], Response::HTTP_OK);
  }
}
