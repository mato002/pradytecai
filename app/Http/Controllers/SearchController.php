<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $results = [];
        
        if ($query) {
            // Search blog posts
            $blogPosts = BlogPost::where('is_published', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%")
                      ->orWhere('body', 'like', "%{$query}%");
                })
                ->orderByDesc('published_at')
                ->limit(10)
                ->get();
            
            // Search job positions
            $positions = Position::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('order')
                ->limit(5)
                ->get();
            
            $results = [
                'blog_posts' => $blogPosts,
                'positions' => $positions,
            ];
        }
        
        return view('search', compact('query', 'results'));
    }
}

