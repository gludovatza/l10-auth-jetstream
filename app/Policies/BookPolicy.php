<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\Team;
use App\Models\User;

class BookPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return true;
  }

  /**
   * Determine whether the user can view the model.
   */
public function view(User $user, Book $book): bool
{
  $team = Team::find($book->team_id);

  return $user->belongsToTeam($team) &&
    $user->hasTeamPermission($team, 'books:read') &&
    $user->tokenCan('books:read');
}

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    $team_id = request('currentTeam') ?? $user->currentTeam->id;
    $team = Team::find($team_id);

    return $user->belongsToTeam($team) &&
      $user->hasTeamPermission($team, 'books:create') &&
      $user->tokenCan('books:create');
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Book $book): bool
  {
    $team = Team::find($book->team_id);

    return $user->belongsToTeam($team) &&
      $user->hasTeamPermission($team, 'books:update') &&
      $user->tokenCan('books:update');
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Book $book): bool
  {
    $team = Team::find($book->team_id);

    return $user->belongsToTeam($team) &&
      $user->hasTeamPermission($team, 'books:delete') &&
      $user->tokenCan('books:delete');
  }

  // /**
  //  * Determine whether the user can restore the model.
  //  */
  // public function restore(User $user, Book $book): bool
  // {
  //     //
  // }

  // /**
  //  * Determine whether the user can permanently delete the model.
  //  */
  // public function forceDelete(User $user, Book $book): bool
  // {
  //     //
  // }
}
