<?php

use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;


test('"Admin/User": can create a new task', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $taskData = Task::factory()->make();

    $response = postJson(route('create'), data: [
        'titre'         => $taskData->titre,
        'description'   => $taskData->description,
        'statut'        => $taskData->statut,
        'date_echeance' => $taskData->date_echeance,
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'titre'         => $taskData->titre,
            'description'   => $taskData->description,
            'statut'        => $taskData->statut,
            'date_echeance' => $taskData->date_echeance,
        ]);
});

test('"Admin": can access all tasks list', function () {
    $admin = admin()->create();

    Sanctum::actingAs($admin);
    assertAuthenticatedAs($admin, 'sanctum');

    $adminTask = Task::factory()->for($admin, 'user')->create();
    $userTask  = Task::factory()->for(user(), 'user')->create();

    $response = getJson(route('index'));

    $response->assertOk()
        ->assertJsonFragment([
            'user_id'       => $adminTask->user_id,
            'titre'         => $adminTask->titre,
            'description'   => $adminTask->description,
            'statut'        => $adminTask->statut,
            'date_echeance' => $adminTask->date_echeance,
        ])
        ->assertJsonFragment([
            'user_id'       => $userTask->user_id,
            'titre'         => $userTask->titre,
            'description'   => $userTask->description,
            'statut'        => $userTask->statut,
            'date_echeance' => $userTask->date_echeance,
        ]);
});

test('"User" : can access only his task list', function () {
    $user = user()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $userTask  = Task::factory()->for($user, 'user')->create();
    $user2Task = Task::factory()->for(user(), 'user')->create();
    $adminTask = Task::factory()->for(admin(), 'user')->create();

    $response = getJson(route('index'));

    $response->assertOk()
        ->assertJsonFragment([
            'user_id'       => $userTask->user_id,
            'titre'         => $userTask->titre,
        ])
        ->assertJsonMissing([
            'user_id'       => $user2Task->user_id,
            'titre'         => $user2Task->titre,
        ])
        ->assertJsonMissing([
            'user_id'       => $adminTask->user_id,
            'titre'         => $adminTask->titre,
        ]);
});

test('"Admin": can view any task', function () {
    $admin = admin()->create();

    Sanctum::actingAs($admin);
    assertAuthenticatedAs($admin, 'sanctum');

    $adminTask  = Task::factory()->for($admin, 'user')->create();
    $userTask = Task::factory()->for(user(), 'user')->create();

    $response  = getJson(route('view', $adminTask->id));
    $response2 = getJson(route('view', $userTask->id));

    $response->assertOk()
        ->assertJsonFragment([
            'user_id'       => $adminTask->user_id,
            'titre'         => $adminTask->titre,
            'description'   => $adminTask->description,
            'statut'        => $adminTask->statut,
            'date_echeance' => $adminTask->date_echeance,
        ]);
    $response2->assertOk()
        ->assertJsonFragment([
            'user_id'       => $userTask->user_id,
            'titre'         => $userTask->titre,
            'description'   => $userTask->description,
            'statut'        => $userTask->statut,
            'date_echeance' => $userTask->date_echeance,
        ]);
});

test('"User" : can view only his own task', function () {
    $user = user()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $userTask  = Task::factory()->for($user, 'user')->create();
    $user2Task = Task::factory()->for(user(), 'user')->create();

    $response  = getJson(route('view', $userTask->id));
    $response2 = getJson(route('view', $user2Task->id));

    $response->assertOk()
        ->assertJsonFragment([
            'user_id'       => $userTask->user_id,
            'titre'         => $userTask->titre,
            'description'   => $userTask->description,
            'statut'        => $userTask->statut,
            'date_echeance' => $userTask->date_echeance,
        ]);
    
    $response2->assertForbidden();
});

test('"Admin": can update a task', function () {
    $admin = admin()->create();

    Sanctum::actingAs($admin);
    assertAuthenticatedAs($admin, 'sanctum');

    $task     = Task::factory()->create();
    $taskData = Task::factory()->make();

    $response = putJson(route('update', $task->id), data: [
        'titre'         => $taskData->titre,
        'description'   => $taskData->description,
        'statut'        => $taskData->statut,
        'date_echeance' => $taskData->date_echeance,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'titre'         => $taskData->titre,
            'description'   => $taskData->description,
            'statut'        => $taskData->statut,
            'date_echeance' => $taskData->date_echeance,
        ]);
});

test('"User" : can update his own task', function () {
    $user = user()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $task     = Task::factory()->for($user, 'user')->create();
    $task2    = Task::factory()->for(user(), 'user')->create();
    $taskData = Task::factory()->make();

    $response = putJson(route('update', $task->id), data: [
        'titre'         => $taskData->titre,
        'description'   => $taskData->description,
        'statut'        => $taskData->statut,
        'date_echeance' => $taskData->date_echeance,
    ]);

    $response2 = putJson(route('update', $task2->id), data: [
        'titre'         => $taskData->titre,
        'description'   => $taskData->description,
        'statut'        => $taskData->statut,
        'date_echeance' => $taskData->date_echeance,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'titre'         => $taskData->titre,
            'description'   => $taskData->description,
            'statut'        => $taskData->statut,
            'date_echeance' => $taskData->date_echeance,
        ]);

    $response2->assertForbidden();
});


test('"Admin": can delete any task', function () {
    $admin = admin()->create();

    Sanctum::actingAs($admin);
    assertAuthenticatedAs($admin, 'sanctum');

    $adminTask = Task::factory()->for($admin, 'user')->create();
    $userTask  = Task::factory()->for(user(), 'user')->create();

    $response  = deleteJson(route('delete', $adminTask->id));
    $response2 = deleteJson(route('delete', $userTask->id));

    $response->assertOk()
        ->assertJson([
            'message' => "Tâche supprimée",
        ]);

    $response2->assertOk()
        ->assertJson([
            'message' => "Tâche supprimée",
        ]);
});

test('"User" : can delete only his own task', function () {
    $user = user()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $userTask  = Task::factory()->for($user, 'user')->create();
    $user2Task = Task::factory()->for(user(), 'user')->create();

    $response  = deleteJson(route('delete', $userTask->id));
    $response2 = deleteJson(route('delete', $user2Task->id));

    $response->assertOk()
        ->assertJson([
            'message' => "Tâche supprimée",
        ]);

    $response2->assertForbidden();
});

test('"Admin": can access deleted tasks list', function () {
    $admin = admin()->create();

    Sanctum::actingAs($admin);
    assertAuthenticatedAs($admin, 'sanctum');

    $task = Task::factory()->create();

    $delete   = deleteJson(route('delete', $task->id));
    $response = getJson(route('deleted'));

    $response->assertOk()
        ->assertJsonFragment([
            'user_id'       => $task->user_id,
            'titre'         => $task->titre,
            'description'   => $task->description,
            'statut'        => $task->statut,
            'date_echeance' => $task->date_echeance,
        ]);
});

test('"User" : can\'t access deleted tasks list', function () {
    $user = user()->create();

    Sanctum::actingAs($user);
    assertAuthenticatedAs($user, 'sanctum');

    $task = Task::factory()->create();

    $delete   = deleteJson(route('delete', $task->id));
    $response = getJson(route('deleted'));

    $response->assertForbidden();
});
