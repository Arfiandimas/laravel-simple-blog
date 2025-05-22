<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateUpdateRequest;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::where([['is_draft', '=', false], ['publish_date', '<=', now()]])->orderBy('publish_date', 'desc')->paginate(10);
            return view('posts.index', compact('posts'));
        } catch (\Throwable $th) {
            Log::error(self::class, [
                'Message ' => $th->getMessage(),
                'On file ' => $th->getFile(),
                'On line ' => $th->getLine()
            ]);
            abort(500);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::findOrFail($id);
            return view('posts.show', compact('post'));
        } catch (ModelNotFoundException $e) {
            Log::warning('Post not found', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            abort(404);
        } catch (\Throwable $th) {
            Log::error(self::class, [
                'Message ' => $th->getMessage(),
                'On file ' => $th->getFile(),
                'On line ' => $th->getLine()
            ]);
            abort(500);
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
        } catch (\Throwable $th) {
            Log::error(self::class, [
                'Message ' => $th->getMessage(),
                'On file ' => $th->getFile(),
                'On line ' => $th->getLine()
            ]);
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateUpdateRequest $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->update($request->validated());
            return redirect()->route('posts.show', ['post' => $id])->with(['status'=> 'success', 'message'=> 'Post updated successfully']);
        } catch (ModelNotFoundException $e) {
            Log::warning('Post not found', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            abort(404);
        } catch (\Throwable $th) {
            Log::error(self::class, [
                'Message ' => $th->getMessage(),
                'On file ' => $th->getFile(),
                'On line ' => $th->getLine()
            ]);
            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Post::findOrFail($id)->delete();
            return redirect()->back()->with(['status'=> 'success', 'message'=> 'Successfully deleted the post']);
        } catch (\Throwable $th) {
            Log::error(self::class, [
                'Message ' => $th->getMessage(),
                'On file ' => $th->getFile(),
                'On line ' => $th->getLine()
            ]);
            abort(500);
        }
    }
}
