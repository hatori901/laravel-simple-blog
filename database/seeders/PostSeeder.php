<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a post seeder, including all possible statuses.
        Post::factory()->create([
            'user_id' => 1,
            'title' => 'Published post',
            'slug' => 'published-post',
            'published_at' => now(),
        ]);

        Post::factory()->create([
            'user_id' => 1,
            'title' => 'Draft post',
            'slug' => 'draft-post',
            'published_at' => now(),
            'is_draft' => true,
        ]);

        Post::factory()->create([
            'user_id' => 1,
            'title' => 'Scheduled post',
            'slug' => 'scheduled-post',
            'published_at' => now()->addDays(3),
        ]);

        Post::factory(10)->create();
    }
}