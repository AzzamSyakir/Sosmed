<?php

namespace App\Http\Controllers\Posts;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class HomeController extends Controller
{
    //store posts 
  
        public function StorePosts(Request $request)
        {
            try {
                $validatedData = $request->validate([
                    'media' => 'required|file|image|max:2048',
                    'caption' => 'required|string|max:255',
                ]);
        
                $path = $validatedData['media']->store('public');
                $url = Storage::url($path);
        
                $post = new Post;
                $post->media = $url;
                $post->caption = $validatedData['caption'];
                $post->user_id = auth()->user()->id;
                $post->save();
        
                return response()->json([
                    'message' => 'Post created successfully',
                    'post' => $post
                ], 201);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => 'Gagal Tambah post',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }
        /**
 * Mengembalikan data pengguna dan semua postingannya
 *
 * @param  Request  $request
 * @return JsonResponse
 */
public function getPostbyUser(Request $request)
{
    try {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }
        $user_id = $user->getAttribute('id');
        $post = $user->post()->with('user')->distinct()->get();
        if (!$post) {
            return response()->json([
                'message' => 'Pengguna tidak memiliki postingan'
            ]);
        }
        return response()->json([
            'user' => $user,
            'post' => $post
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Gagal mengambil data pengguna dan postingannya',
            'error' => $e->getMessage()
        ], 500);
    }
}

        
}

