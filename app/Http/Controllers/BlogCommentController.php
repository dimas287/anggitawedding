<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|string|min:5',
        ]);

        $post->comments()->create($validated);

        return back()->with('success', 'Terima kasih! Komentar Anda telah dikirim dan menunggu persetujuan admin.');
    }
}
