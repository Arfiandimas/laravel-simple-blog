<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = null;

            if (Auth::check()) {
                $posts = Post::where(['created_by' => Auth::user()->id])->orderBy('updated_at', 'desc')->paginate(10);
            }

            return view('home', compact('posts'));
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
