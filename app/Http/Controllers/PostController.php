<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    //
    public function index()
    {
        $posts = Post::latest()->paginate(5); //latest bertujuan mengurutkan data yg paling terbaru, paginate untuk membatasi jumlah data yg diambil

        return view('posts.index',compact('posts')); //compact merupakan method bawaan php yg akan digunakan untuk mengrim data ke view
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'

        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts',$image->hashName());

        //create post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        //redirect tto index
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil disimpan..!']);
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    
    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'image'     => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        if ($request->hasFile('image')) {
            //upload image baru
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete image lama
            Storage::delete('public/post'.$post->image);

            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        }
        else {
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);     
        }
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil diubah!']);
    }

    public function destroy(Post $post)
    {
        Storage::delete('public/posts'.$post->image);

        $post->delete();

        return redirect()->route('posts.index')->with(['success' => 'data berhasil dihapus']);
    }
    
    
}
