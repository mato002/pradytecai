<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['title']);
        }

        if (!isset($data['published_at']) || !$data['published_at']) {
            $data['published_at'] = now();
        }

        $data['is_published'] = $request->boolean('is_published', true);

        BlogPost::create($data);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $post): View
    {
        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['title']);
        }

        if (!isset($data['published_at']) || !$data['published_at']) {
            $data['published_at'] = $post->published_at ?? now();
        }

        $data['is_published'] = $request->boolean('is_published', true);

        $post->update($data);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post deleted.');
    }
}


