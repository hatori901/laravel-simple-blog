<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_homepage_is_accessible()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
}