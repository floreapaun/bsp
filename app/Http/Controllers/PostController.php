<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Image;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['images', 'location', 'category'])->where('is_active', 1)->get();
        return response()->json($posts);
    }

    public function indexAll()
    {
        $posts = Post::with(['images', 'location', 'category'])->get();
        return response()->json($posts);
    }

    public function indexUser()
    {
        $posts = Post::where('user_id', Auth::id())->with(['images', 'location', 'category'])->get();
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required',
            'phoneNumber' => 'required',
            'price' => 'required',
            'location_id' => 'required',
            'category_id' => 'required',
            'condition' => 'required'
        ]);

        // Create a new post
        $post = new Post();

        $images = $request->file('images');
        if (!$images) {
            return response()->json(['error' => 'No images uploaded'], 400);
        }

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->phone_number = $validated['phoneNumber'];
        $post->price = $validated['price'];
        $post->location_id = $validated['location_id'];
        $post->category_id = $validated['category_id'];
        $post->condition = $validated['condition'];
        $post->user_id = auth()->id(); 
        $post->save();
        
        $imageUrls = [];

        foreach ($images as $image) {
            $imageToSave = new Image();
            $imageToSave->post_id = $post->id;

            // Save images in 'storage/app/public/images'
            $path = $image->storeAs('images', $image->getClientOriginalName(), 'public'); 

            $imageToSave->file_path = Storage::url($path);

            // Generate URL for each image
            $imageUrls[] = Storage::url($path); 

            $imageToSave->save();
        }

        return response()->json([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'urls' => $imageUrls,
        ], 201);

    }

    public function searchByFilter(Request $request)
    {
        // Fetch query parameters
        $query = $request->input('query');
        $location = $request->input('location');
        $category = $request->input('category');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        $condition = $request->input('condition');

        $posts = Post::query();

        // Search by title or body
        if ($query) {
            $posts->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('body', 'LIKE', '%' . $query . '%');
            });
        }

        // Filter by category
        if ($category) {
            $posts->where('category_id', $category);
        }

        // Filter by location
        if ($location) {
            $posts->where('location_id', $location);
        }

        // Filter by condition
        if ($condition) {
            $posts->where('condition', $condition);
        }

        // Filter by price range
        if ($minPrice !== null) {
            $posts->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $posts->where('price', '<=', $maxPrice);
        }

        // Include related models 
        $posts = $posts->with(['images', 'category', 'location'])->get();

        return response()->json($posts);
    }

    public function searchByFilterAdmin(Request $request)
    {
        // Fetch query parameters
        $query = $request->input('query');
        $location = $request->input('location');
        $category = $request->input('category');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        $condition = $request->input('condition');
        $status = intval($request->input('status'));

        $posts = Post::query();

        // Search by title or body
        if ($query) {
            $posts->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('body', 'LIKE', '%' . $query . '%');
            });
        }

        // Filter by category
        if ($category) {
            $posts->where('category_id', $category);
        }

        // Filter by location
        if ($location) {
            $posts->where('location_id', $location);
        }

        // Filter by condition
        if ($condition) {
            $posts->where('condition', $condition);
        }

        // Filter by status
        if ($status === 0 || $status === 1) {
            $posts->where('is_active', $status);
        }

        // Filter by price range
        if ($minPrice !== null) {
            $posts->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $posts->where('price', '<=', $maxPrice);
        }

        // Include related models 
        $posts = $posts->with(['images', 'category', 'location'])->get();

        return response()->json($posts);
    }

    public function searchUser(Request $request)
    {
        $query = $request->input('query');

        // Search posts by title or content
        $posts = Post::where('user_id', Auth::id())
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('title', 'like', "%{$query}%")
                         ->orWhere('body', 'like', "%{$query}%");
        })
        ->with(['images', 'location', 'category'])
        ->get();

        return response()->json($posts);
    }

    /**
     * Display the specified post.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
    
        // Validate the updated data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required',
            'phone_number' => 'required',
            'price' => 'required',
            'location_id' => 'required',
            'category_id' => 'required',
            'condition' => 'required'
        ]);
    
        // Check if any field is changed
        $changes = array_diff_assoc($validated, $post->only(array_keys($validated)));
    
        if (!empty($changes)) {
            $post->fill($validated);

            // Mark post as inactive for new admin approval if there are changes
            $post->is_active = 0;

            $post->save();
        }
    
        $imageUrls = [];
    
        if ($request->hasFile('newImages')) {
            foreach ($request->file('newImages') as $image) {
                $imageToSave = new Image();
                $imageToSave->post_id = $post->id;
    
                // Save images in 'storage/app/public/images'
                $path = $image->storeAs('images', $image->getClientOriginalName(), 'public');
    
                $imageToSave->file_path = Storage::url($path);
    
                // Generate URL for each image
                $imageUrls[] = Storage::url($path);
    
                $imageToSave->save();
            }
    
            // Mark post as inactive for new admin approval if new images are uploaded
            $post->is_active = 0;
            $post->save();
        }
    
        return response()->json([
            'title' => $post->title,
            'body' => $post->body,
            'phone_number' => $post->phone_number,
            'urls' => $imageUrls,
        ], 201);
    }

    public function updateActive(Request $request, $id)
    {
        $validated = $request->validate([
            'is_active' => 'required', 
        ]);

        $post = Post::findOrFail($id); 
        if ($validated['is_active'] === 1)
            $post->reject_message = '';
        $post->is_active = $validated['is_active']; 
        $post->save(); 

        return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
    }

    public function rejectMessage(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'nullable|string',
        ]);

        $post = Post::findOrFail($request->post_id);
        $post->reject_message = $request->content;
        $post->save();

        return response()->json([
            'message' => 'Reject message sent to seller successfully!',
            'post' => $post,
        ], 200);
    }

    /**
     * Remove the specified post from storage.
     *
     */

    public function destroy($id)
    {
        // Find the post by ID
        $post = Post::find($id);

        // Check if the post exists
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        try {
            $images = $post->images;

            foreach ($images as $image) {

                // Convert file path to match actual storage location
                // e.g., "public/images/filename.jpg"
                $relativePath = str_replace('/storage', 'public', $image->file_path); 

                // Full path: storage/app/public/images/filename.jpg
                $localPath = storage_path('app/' . $relativePath); 

                if (file_exists($localPath)) 
                    unlink($localPath);

                $image->delete();
            }

            $post->delete();
            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete post'], 500);
        }
    }
}
