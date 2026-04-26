<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest()->paginate(9);
        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $post->increment('views');
        
        // Ambil artikel terkait (kategori sama, kecuali diri sendiri)
        $relatedPosts = Post::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
