<?php
namespace App\Http\Controllers;
use App\Post;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Departamento;



class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request )
    {
        $departamento = Departamento::all();

        $Post = Post::all();
  
  //dd($Post);
        return view('PostsPublic.show',compact('Post', 'departamento'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $departamento = Departamento::all();
        if(Auth::check()){


            return view('Posts.create', compact('departamento'));
    
        }
        else{
    
            return view('Auth.login', compact('departamento'));
        }
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {


        $Campos=['Titular' => 'required|string|max:1000',
                'Cuerpo' => 'required|string|max:100000',
                'departamento_id' => 'required|integer',
                'Foto' => 'required|file'

                
                
        
    ];

    $Mensaje=["required" =>'El :attribute es requerido'];

    $this->validate($request, $Campos , $Mensaje);
    $newPost= new Post;

    
        $newPost->Foto=$request->file('Foto')->store('uploads', 'public');
    
        $departamento = Departamento::all();

    
    $newPost->Titular=$request->Titular;
    $newPost->Cuerpo=$request->Cuerpo;
    $newPost->departamento_id = $request->departamento_id; 
    $userId = Auth::id();
    $newPost->user_id=$userId;
    $newPost->save();
    return redirect('VerPosts');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $departamento = Departamento::all();

      $id= Auth::id();
      $Post = User::find($id)->post;

//dd($Post);
      return view('Posts.show',compact('Post', 'departamento'));

    }


    public function showPublic($id)
    {

      $Post = Post::find($id);
      $PostNext = Post::find($id + 1);
      $PostBefore = Post::find($id - 1);
     return view('PostsPublic.Post', compact('Post','PostNext','PostBefore' ));


    }


    public function showPublicDepartamento(Request $request)
    {
        $departamento = Departamento::all();

        $id= $request->input('departamento');
        $Post = Departamento::find($id)->post;
      
        return view('PostsPublic.show',compact('Post', 'departamento'));



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $departamento = Departamento::all();

        $Post= Post::findorFail($id);

        return view('Posts.edit',compact('Post', 'departamento'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $datosPost = request()->except(['_token','_method']);

        if ($request->hasFile('Foto')){

$Post= Post::findorFail($id);
             
            Storage::delete('public/'.$Post->Foto);

            $datosPost['Foto']=$request->file('Foto')->store('uploads', 'public');



        }

        Post::where('id', '=', $id)->update($datosPost);

        return redirect('VerPosts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::destroy($id);
        return redirect('VerPosts');
    }
}
