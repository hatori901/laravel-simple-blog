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
        $post = Post::factory()->create(['is_draft' => 0, 'published_at' => now()]);
        
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

    public function test_user_cannot_edit_other_users_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post->id));
        $response->assertStatus(404);
    }

    public function test_user_cannot_update_other_users_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->patch(route('posts.update', $post->id), [
            'title' => 'Updated Title',
            'content' => 'Updated Content'
        ]);

        $response->assertStatus(404);
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

    public function test_user_cannot_delete_other_users_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post->id));
        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_delete_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $response = $this->delete(route('posts.destroy', $post->id));
        $response->assertRedirect(route('home'));
    }

    public function test_only_published_posts_are_visible_to_guests()
    {
        Post::factory()->create([
            'title' => 'Published Post',
            'is_draft' => false,
            'published_at' => now()
        ]);
        Post::factory()->create([
            'title' => 'Draft Post',
            'is_draft' => true,
            'published_at' => now()
        ]);

        $response = $this->get(route('posts.index'));

        $response->assertSee('Published Post');
        $response->assertDontSee('Draft Post');
    }

    public function test_scheduled_posts_are_not_visible_until_published()
    {
        Post::factory()->create([
            'title' => 'Scheduled Post',
            'is_draft' => false,
            'published_at' => now()->addDays(2)
        ]);

        $response = $this->get(route('posts.index'));
        $response->assertDontSee('Scheduled Post');
    }
}