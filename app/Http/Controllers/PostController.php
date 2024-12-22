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
        $posts = Post::with(['images', 'location'])->where('is_active', 1)->get();
        return response()->json($posts);
    }

    public function indexAll()
    {
        $posts = Post::with(['images', 'location'])->get();
        return response()->json($posts);
    }

    public function indexUser()
    {
        $posts = Post::where('user_id', Auth::id())->with(['images', 'location'])->get();
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
            'location_id' => 'required'
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

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search posts by title or content
        $posts = Post::with(['images', 'location'])->where('title', 'like', "%{$query}%")
                     ->orWhere('body', 'like', "%{$query}%")
                     ->get();

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
        ->with(['images', 'location'])
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
            'location_id' => 'required'
        ]);

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->phone_number = $validated['phone_number'];
        $post->price = $validated['price'];
        $post->location_id = $validated['location_id'];
        $post->save();

        $imageUrls = [];

        $images = $request->file('newImages');
        if ($images)
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
            'phone_number' => $validated['phone_number'],
            'urls' => $imageUrls,
        ], 201);

    }

    public function updateActive(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean', // Validate that is_active is a boolean
        ]);

        $post = Post::findOrFail($id); // Find the post by ID
        $post->is_active = $request->is_active; // Set is_active based on request
        $post->save(); // Save the changes

        return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
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
