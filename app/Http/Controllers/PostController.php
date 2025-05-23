<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateUpdateRequest;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::where('is_draft', false)->where('publish_date', '<=', now())->orderBy('publish_date', 'desc')->orderBy('created_at', 'desc')->paginate(10);
            return view('posts.index', compact('posts'));
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUpdateRequest $request)
    {
        try {
            $post = Post::create([
                ...$request->validated(),
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('posts.internal', $post)->with([
                'status' => 'success',
                'message' => 'Post created successfully',
            ]);
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post)
    {
        try {
            return view('posts.show', compact('post'));
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        try {
            return view('posts.edit', compact('post'));
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateUpdateRequest $request, Post $post)
    {
        try {
            $post->update($request->validated());

            return redirect()->route('posts.internal', $post)->with([
                'status' => 'success',
                'message' => 'Post updated successfully',
            ]);
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            $post->delete();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Successfully deleted the post',
            ]);
        } catch (Exception $e) {
            Log::error(self::class, [
                'Message ' => $e->getMessage(),
                'On file ' => $e->getFile(),
                'On line ' => $e->getLine()
            ]);
            throw $e;
        }
    }
}
