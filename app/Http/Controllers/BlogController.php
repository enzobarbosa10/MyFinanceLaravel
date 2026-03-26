<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        return view('blog.show', compact('post'));
    }
}
