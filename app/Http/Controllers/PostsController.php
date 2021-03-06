<?php

namespace App\Http\Controllers;

use App\Post;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use Carbon\Carbon;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('verifyCategoryCount')->only(['create','store']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('posts.index')->with('posts',Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create')->with('catagories',Category::all());;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        //upload the image
        $image = $request->file('image')->store('posts');
        $published_at = NULL;
        if($request->published_at)
        {
            $published_at = Carbon::parse($request->published_at)->format('Y-m-d H:i:s');
        }
        //create the post
        Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'published_at' => $published_at,
            'image' => $image,
            'category_id' => $request->category
        ]);

        session()->flash('success','Post created succesfully');
        
        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $post = Post::find($id);
         return view('posts.create')->with('post',$post)->with('catagories',Category::all());;;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, $id)
    {
        //upload the image
        $post = Post::find($id);
        
        if($request->hasFile('image'))
        {
            $image = $request->file('image')->store('posts');
            // delete previous file
            $post->deleteImage();
            $post->image = $image;
        }
        
        $published_at = NULL;
        if($request->published_at)
        {
            $published_at = Carbon::parse($request->published_at)->format('Y-m-d H:i:s');
        }

        $post->title = $request->title;
        $post->description = $request->description;
        $post->content = $request->content;
        $post->published_at = $request->published_at;
        $post->save();

        session()->flash('success','Posts updated succesfully');
        
        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::withTrashed()->where('id',$id)->firstorFail();

        if($post->trashed())
        {
            $post->deleteImage();
            $post->forceDelete();
            session()->flash('success','Post deleted succesfully');
        }
        else
        {
            $post->delete();
            session()->flash('success','Post trashed succesfully');
        }

        
        
        return redirect()->route('posts.index');
    }


    /**
     * Display all specified post
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $trashed = Post::onlyTrashed()->get();
        return view('posts.index')->with('posts',$trashed);
    }

    /**
     * restore a specific post
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {   
        $post = Post::withTrashed()->where('id',$id)->firstorFail();
        $post->restore();

        session()->flash('success','Post restored succesfully');
        return redirect()->back();
    }


    /**
     * Display a view of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPost(Request $request)
    {
        
        return view('posts.view')->with('catagories',Category::all());
    }


    /**
     * Display a view of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchPost()
    {
        $category_id = $_REQUEST['category_id']??'';
        if($category_id)
        {
            $post = Post::where('category_id',$category_id)->where('published_at','<=',date('Y-m-d H:i:s'))->get();
        }
        else
        {
           $post = Post::where('published_at','<=',date('Y-m-d H:i:s'))->get();
        }
        return ['data'=>$post, 'recordsFiltered'=>count($post), 'recordsTotal'=>count($post)];
    }

   
}
