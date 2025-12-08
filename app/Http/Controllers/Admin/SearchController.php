<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * API endpoint for live search (returns JSON)
     */
    public function api(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        $results = [];
        
        if (!empty($query) && strlen($query) >= 2) {
            $searchTerm = "%{$query}%";
            $queryLower = strtolower($query);
            
            // Search admin pages
            $pages = collect([
                ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'description' => 'Command overview and metrics'],
                ['name' => 'Products', 'route' => 'admin.products.index', 'description' => 'Manage products and solutions'],
                ['name' => 'Enquiries', 'route' => 'admin.enquiries.index', 'description' => 'View contact form submissions'],
                ['name' => 'Users', 'route' => 'admin.users.index', 'description' => 'Manage admin users'],
                ['name' => 'Blog', 'route' => 'admin.blog.index', 'description' => 'Manage blog posts'],
                ['name' => 'Positions', 'route' => 'admin.positions.index', 'description' => 'Manage job positions'],
                ['name' => 'Applications', 'route' => 'admin.applications.index', 'description' => 'Review job applications'],
                ['name' => 'Settings', 'route' => 'admin.settings.index', 'description' => 'System settings'],
                ['name' => 'Profile', 'route' => 'admin.profile.show', 'description' => 'Your profile'],
            ])->filter(function ($page) use ($queryLower) {
                return str_contains(strtolower($page['name']), $queryLower) || 
                       str_contains(strtolower($page['description']), $queryLower);
            })->map(function ($page) {
                return [
                    'type' => 'page',
                    'title' => $page['name'],
                    'subtitle' => $page['description'],
                    'url' => route($page['route']),
                    'icon' => 'page',
                ];
            });
            
            // Search blog posts
            $blogPosts = BlogPost::where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(title) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(excerpt) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(category) LIKE ?', [strtolower($searchTerm)]);
                })
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'type' => 'blog',
                        'title' => $post->title,
                        'subtitle' => $post->category ?? 'Uncategorized',
                        'url' => route('admin.blog.edit', $post),
                        'icon' => 'blog',
                    ];
                });
            
            // Search users
            $users = User::where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                })
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user',
                        'title' => $user->name,
                        'subtitle' => $user->email,
                        'url' => route('admin.users.edit', $user),
                        'icon' => 'user',
                    ];
                });
            
            // Search enquiries
            $enquiries = ContactMessage::where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(subject) LIKE ?', [strtolower($searchTerm)]);
                })
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function ($enquiry) {
                    return [
                        'type' => 'enquiry',
                        'title' => $enquiry->name,
                        'subtitle' => $enquiry->subject ?? $enquiry->email,
                        'url' => route('admin.enquiries.index') . '#enquiry-' . $enquiry->id,
                        'icon' => 'enquiry',
                    ];
                });
            
            // Search applications
            $applications = JobApplication::with('position')
                ->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                })
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function ($app) {
                    return [
                        'type' => 'application',
                        'title' => $app->name,
                        'subtitle' => $app->position ? $app->position->title : $app->email,
                        'url' => route('admin.applications.show', $app),
                        'icon' => 'application',
                    ];
                });
            
            // Search positions
            $positions = Position::where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(title) LIKE ?', [strtolower($searchTerm)])
                      ->orWhereRaw('LOWER(description) LIKE ?', [strtolower($searchTerm)]);
                })
                ->limit(5)
                ->get()
                ->map(function ($position) {
                    return [
                        'type' => 'position',
                        'title' => $position->title,
                        'subtitle' => $position->is_active ? 'Active' : 'Inactive',
                        'url' => route('admin.positions.edit', $position),
                        'icon' => 'position',
                    ];
                });
            
            $results = $pages->concat($blogPosts)
                                ->concat($users)
                                ->concat($enquiries)
                                ->concat($applications)
                                ->concat($positions)
                                ->take(12)
                                ->values();
        }
        
        return response()->json(['results' => $results]);
    }

    public function index(Request $request): View
    {
        $query = trim($request->get('q', ''));
        $results = [
            'blog_posts' => collect(),
            'users' => collect(),
            'enquiries' => collect(),
            'applications' => collect(),
            'positions' => collect(),
        ];
        
        if (!empty($query)) {
            $searchTerm = "%{$query}%";
            
            try {
                // Search blog posts (all, not just published) - case insensitive
                $blogPosts = BlogPost::where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(title) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(excerpt) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(body) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(category) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
                
                $results['blog_posts'] = $blogPosts;
            } catch (\Exception $e) {
                \Log::error('Blog search error: ' . $e->getMessage());
            }
            
            try {
                // Search users - case insensitive
                $users = User::where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->limit(10)
                    ->get();
                
                $results['users'] = $users;
            } catch (\Exception $e) {
                \Log::error('User search error: ' . $e->getMessage());
            }
            
            try {
                // Search enquiries/contact messages - case insensitive
                $enquiries = ContactMessage::where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(company) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(subject) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(message) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
                
                $results['enquiries'] = $enquiries;
            } catch (\Exception $e) {
                \Log::error('Enquiry search error: ' . $e->getMessage());
            }
            
            try {
                // Search job applications - case insensitive
                $applications = JobApplication::where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(phone) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(cover_letter) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
                
                $results['applications'] = $applications;
            } catch (\Exception $e) {
                \Log::error('Application search error: ' . $e->getMessage());
            }
            
            try {
                // Search positions - case insensitive
                $positions = Position::where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(title) LIKE ?', [strtolower($searchTerm)])
                          ->orWhereRaw('LOWER(description) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orderBy('order')
                    ->limit(10)
                    ->get();
                
                $results['positions'] = $positions;
            } catch (\Exception $e) {
                \Log::error('Position search error: ' . $e->getMessage());
            }
        }
        
        return view('admin.search', compact('query', 'results'));
    }
}

