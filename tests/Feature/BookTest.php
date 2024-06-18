<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
  use RefreshDatabase;

  public function test_visitors_cannot_access_books()
  {
    $response = $this->get('/books');
    $response->assertStatus(302);
    $response->assertRedirect('login');
  }

  public function test_team_owner_can_access_books()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    $team_id = $owner->ownedTeams()->first()->id;
    $bookTitle = Book::factory()->create(['team_id' => $team_id])->title;

    $response = $this->actingAs($owner)->get('books');

    $response->assertStatus(200);
    $response->assertSee($bookTitle);
    $response->assertSee('Add new book');
  }

  public function test_editor_from_other_team_cannot_delete_book()
  {
    $owner = User::factory()->withPersonalTeam()->create();

    $owner->currentTeam->users()->attach(
      $editor = User::factory()->withPersonalTeam()->create(), ['role' => 'editor']
    );

    $team_id = $owner->ownedTeams()->first()->id;
    $book_id = Book::factory()->create(['team_id' => $team_id])->id;

    $response = $this->actingAs($editor)->delete('books/' . $book_id);

    $response->assertStatus(403);
  }
}
