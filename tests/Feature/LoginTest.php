<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Concerns\TestDatabases;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $role = Role::create([
            "name" => "admin",
        ]);

        User::create([
            "name" => "super_admin",
            "email" => "admin@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $response = $this->post('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertOk();
    }
}
