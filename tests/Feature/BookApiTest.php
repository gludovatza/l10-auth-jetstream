<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Team;

class BookApiTest extends TestCase
{
  use RefreshDatabase;

  // index +
  public function test_owner_can_access_books()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    Book::factory(5)->create(['team_id' => $owner->currentTeam->id]);

    $response = $this
      ->actingAs($owner)
      ->get('/api/v1/books');

    $response->assertStatus(200);
  }

  // index -
  public function test_visitor_cannot_access_books()
  {
    $response = $this->get('/api/v1/books');
    $response->assertStatus(302);
    $response->assertRedirect('login');
  }

  // show +
  public function test_team_owner_can_access_books()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    $team_id = $owner->currentTeam->id;
    Book::factory()->create(['team_id' => $team_id]);

    $response = $this->actingAs($owner)->get('/api/v1/books/1');

    $response->assertStatus(200);
  }

  // show -
  public function test_other_team_member_cannot_access_book()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    Book::factory()->create(['team_id' => $owner->currentTeam->id]);

    $foreignMember = User::factory()->withPersonalTeam()->create();

    $response = $this->actingAs($foreignMember)->get('/api/v1/books/1');

    $response->assertStatus(403);
  }

  // store +
  public function test_editor_can_create_a_book_in_another_team()
  {
    $owner = User::factory()->withPersonalTeam()->create();

    $owner->currentTeam->users()->attach(
      $editor = User::factory()->withPersonalTeam()->create(), ['role' => 'editor']
    );

    $response = $this->actingAs($editor)->post('/api/v1/books/', [
      'title' => "My first book",
      'team_id' => $owner->currentTeam->id
    ]);

    $response->assertStatus(201);
  }

  // store -
  public function test_user_cannot_create_a_book_in_another_team()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    $user = User::factory()->withPersonalTeam()->create();

    $response = $this->actingAs($user)->post('/api/v1/books/', [
      'title' => "My wrong book",
      'currentTeam' => $owner->currentTeam->id
    ]);

    $response->assertStatus(403);
  }

  // update +
  public function test_editor_can_update_the_book_title_in_another_team()
  {
    $owner = User::factory()->withPersonalTeam()->create();

    $owner->currentTeam->users()->attach(
      $editor = User::factory()->withPersonalTeam()->create(), ['role' => 'editor']
    );
    $owner_team_id = $owner->currentTeam->id;
    Book::factory()->create(['team_id' => $owner_team_id]);

    $response = $this->actingAs($editor)->put('/api/v1/books/1', [
      'title' => "My first book",
      'team_id' => $owner_team_id
    ]);

    $response->assertStatus(200);
  }

  // update -
  public function test_user_cannot_update_a_book_in_another_team()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    $user = User::factory()->withPersonalTeam()->create();
    $owner_team_id = $owner->currentTeam->id;
    Book::factory()->create(['team_id' => $owner_team_id]);

    $response = $this->actingAs($user)->put('/api/v1/books/1', [
      'title' => "My wrong book",
      'currentTeam' => $owner_team_id
    ]);

    $response->assertStatus(403);
  }

  // destroy +
  public function test_team_owner_can_delete_a_book()
  {
    $owner = User::factory()->withPersonalTeam()->create();
    Book::factory()->create(['team_id' => $owner->currentTeam->id]);

    $response = $this->actingAs($owner)->delete('/api/v1/books/1');

    $response->assertStatus(200);
  }

  // destroy -
  public function test_editor_from_other_team_cannot_delete_book()
  {
    $owner = User::factory()->withPersonalTeam()->create();

    $owner->currentTeam->users()->attach(
      $editor = User::factory()->withPersonalTeam()->create(), ['role' => 'editor']
    );

    $team_id = $owner->ownedTeams()->first()->id;
    $book_id = Book::factory()->create(['team_id' => $team_id])->id;

    $response = $this->actingAs($editor)->delete('/api/v1/books/' . $book_id);

    $response->assertStatus(403);
  }
}
