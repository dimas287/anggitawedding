<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Package;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->get();
        $packages = Package::where('is_active', true)->get();

        return response()->view('sitemap', [
            'posts' => $posts,
            'packages' => $packages,
        ])->header('Content-Type', 'text/xml');
    }
}
