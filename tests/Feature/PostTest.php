<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_guest_can_view_posts_index()
    {
        $response = $this->get(route('posts.index'));
        $response->assertStatus(200);
    }

    public function test_guest_can_view_a_single_post()
    {
        $post = Post::factory()->create(['status' => 'published', 'published_at' => now()]);
        
        $response = $this->get(route('posts.show', $post->slug));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_post_creation_page()
    {
        $response = $this->get(route('posts.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $postData = Post::factory()->make()->toArray();
        
        $response = $this->post(route('posts.store'), $postData);
        $response->assertRedirect(route('home'));
    }

    public function test_guest_cannot_edit_a_post()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('posts.edit', $post->id));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_edit_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $response = $this->get(route('posts.edit', $post->id));
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_update_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        
        $updatedData = ['title' => 'Updated Title'];
        $response = $this->patch(route('posts.update', $post->id), $updatedData);
        $response->assertRedirect(route('home'));
    }

    public function test_authenticated_user_can_delete_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $response = $this->delete(route('posts.destroy', $post->id));
        $response->assertRedirect(route('home'));
    }
}