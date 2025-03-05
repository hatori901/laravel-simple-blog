<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

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
                'slug' => \Str::slug($request->title),
                'title' => $request->title,
                'content' => $request->content,
                'published_at' => $request->published_at,
                'is_draft' => $request->is_draft,
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
        $post = Post::findOrFail($post->id);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        if($post->user_id != auth()->id()){
            return redirect()->back()->with('error', 'You are not authorized to update this post.');
        }
        $post->update([
            'slug' => \Str::slug($request->title),
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'is_draft' => $request->is_draft,
        ]);
        return redirect()->route('home')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post = Post::findOrFail($post->id);
        if($post->user_id != auth()->id()){
            return redirect()->back()->with('error', 'You are not authorized to delete this post.');
        }
        $post->delete();
        return redirect()->route('home')->with('success', 'Post deleted successfully.');
    }
}