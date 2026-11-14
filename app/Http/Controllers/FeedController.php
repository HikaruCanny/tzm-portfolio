<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()->latest('published_at')->take(20)->get();

        return response()
            ->view('feed', ['posts' => $posts])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
