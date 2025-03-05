<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('author')
            ->published()
            ->latest()
            ->paginate(10);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $create = Post::create([
                'user_id' => auth()->id(),
                'slug' => $this->slugify($request->title),
                'title' => $request->title,
                'content' => $request->content,
                'published_at' => $request->published_at,
                'is_draft' => $request->is_draft ?? 0,
            ]);

        if(!$create){
            return redirect()->back()->with('error', 'Failed to create post.');
        }
        return redirect()->route('home')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = Post::with('author')->where('slug', $slug)->where('is_draft', 0)->where('published_at', '<=', now())->firstOrFail();
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        $post = Post::findOrFail($post->id);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        Gate::authorize('update', $post);
        $post->update([
            'slug' => $this->slugify($request->title),
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'is_draft' => $request->is_draft ?? 0,
        ]);
        return redirect()->route('home')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $post->delete();
        return redirect()->route('home')->with('success', 'Post deleted successfully.');
    }

    private function slugify($title)
    {
        $slug = \Str::slug($title);
        $count = Post::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        return $slug;
    }
}