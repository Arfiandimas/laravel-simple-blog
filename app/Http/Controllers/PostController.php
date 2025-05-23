<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateUpdateRequest;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $posts = Post::where([['is_draft', '=', false], ['publish_date', '<=', now()]])->orderBy('publish_date', 'desc')->orderBy('created_at', 'desc')->paginate(10);
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
                'created_by' => Auth::id()
            ]);

            return redirect()->route('posts.show', ['post' => $post->id])->with(['status'=> 'success', 'message'=> 'Post created successfully']);
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
    public function show(Request $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $isInternal = $request->route()->getName() === 'posts.internal';

            if (!$isInternal && ($post->is_draft || $post->publish_date > now()->format('Y-m-d'))) {
                abort(404);
            }
            return view('posts.show', compact('post'));
        } catch (ModelNotFoundException $e) {
            Log::warning('Post not found', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            abort(404);
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
    public function edit(string $id)
    {
        try {
            $post = Post::findOrFail($id);
            return view('posts.edit', compact('post'));
        } catch (ModelNotFoundException $e) {
            Log::warning('Post not found', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            abort(404);
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
    public function update(CreateUpdateRequest $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);

            // Validate ownership
            if ($post->created_by !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            $post->update($request->validated());
            return redirect()->route('posts.show', ['post' => $id])->with(['status'=> 'success', 'message'=> 'Post updated successfully']);
        } catch (ModelNotFoundException $e) {
            Log::warning('Post not found', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            abort(404);
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
    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);

            // Validate ownership
            if ($post->created_by !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            $post->delete();

            return redirect()->back()->with(['status'=> 'success', 'message'=> 'Successfully deleted the post']);
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
