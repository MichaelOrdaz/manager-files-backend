<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BugLoginResponseTest extends TestCase
{

    public function test_empty_data()
    {
        $response = $this->postJson('api/v1/auth/login');

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => ['email', 'password'],
            'success',
        ]);

        $this->assertFalse($response['success']);
    }

    public function test_error_empty_password()
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => 'webmaster@puller.mx',
            'password' => ''
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => ['password'],
            'success',
        ]);

        $this->assertFalse($response['success']);
    }

    public function test_error_empty_email()
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => '',
            'password' => '12345'
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => ['email'],
            'success',
        ]);

        $this->assertFalse($response['success']);
    }

    public function test_error_bad_data()
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => 'bad@mail.com',
            'password' => '12345'
        ]);

        $response->assertStatus(404);

        $response->assertJsonStructure([
            'errors',
            'success',
        ]);

        $this->assertFalse($response['success']);
    }

    public function test_error_good_data()
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => 'webmaster@puller.mx',
            'password' => '12345678'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'success',
        ]);

        $this->assertTrue($response['success']);
    }



}
