<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

test('register user with admin role', function () {
    $adminData = admin()->make();

    $response = postJson(route('register'), data: [
        "name"                  => $adminData->name,
        "email"                 => $adminData->email,
        "password"              => $adminData->password,
        "password_confirmation" => $adminData->password,
        "role"                  => $adminData->role,
    ]);

    $response->assertStatus(201)->assertJson(["message" => "Utilisateur Inscrit."]);

    assertDatabaseHas('users', [
        "name"                  => $adminData->name,
        "email"                 => $adminData->email,
        "role"                  => $adminData->role,
    ]);
});

test('register user with user role', function () {
    $userData = user()->make();

    $response = postJson(route('register'), data: [
        "name"                  => $userData->name,
        "email"                 => $userData->email,
        "password"              => $userData->password,
        "password_confirmation" => $userData->password,
        "role"                  => $userData->role,
    ]);

    $response->assertStatus(201)->assertJson(["message" => "Utilisateur Inscrit."]);

    assertDatabaseHas('users', [
        "name"                  => $userData->name,
        "email"                 => $userData->email,
        "role"                  => $userData->role,
    ]);
});


test('login as admin and return access token', function () {
    $admin = admin()->create();

    $response = postJson(route('login'), data: [
        "email"    => $admin->email,
        "password" => 'password',
    ]);

    $response->assertSuccessful()->assertJson([
        'message' => 'Utilisateur Connecté',
        'token'   => $response->json('token'),
    ]);
});

test('login as user and return access token', function () {
    $user = user()->create();

    $response = postJson(route('login'), data: [
        "email"    => $user->email,
        "password" => 'password',
    ]);

    $response->assertSuccessful()->assertJson([
        'message' => 'Utilisateur Connecté',
        'token'   => $response->json('token'),
    ]);
});

