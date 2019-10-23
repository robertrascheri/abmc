<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Response;
use App\User;
use App\role;

class UsuariosController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('admin.usuarios.index', compact('users'));
    }

     public function nuevoUsuario(Request $request){      
    
        $usuario=DB::table('users')
                    ->where('email', $request->email)
                    ->get();
        
        if(!empty($usuario[0])){
           return Response::json(array('status' => 'error'));
        }else{
        
        $hoy = date("Y-m-d H:i:s"); 
        
        $id = DB::table('users')->insertGetId(
              [ 'name' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => $hoy,
                'updated_at' => $hoy
              ]
          );
        $rol = $request->rol;
        if($rol == '1'){
        	$usuario = User::find($id);
        	$usuario->roles()->attach(Role::where('name', 'admin')->first());
        	$usuario->save();
        }
           return Response::json(array('status' => 'correcto', 'id' => $id));
        }  
    }

    public function editarUsuario(Request $request){      
    
        $item = User::find($request->id_user);
        $item->fill(['name' => $request->nombre, 'email' => $request->email]);
        $item->save();
        if ($item->hasRole('admin')){$rol='Si';}else{$rol='No';}
        return Response::json(array('status' => 'correcto', 'rol' => $rol));
     
    }

    public function destroy(Request $request){
        $item = User::find($request->id_user);
        $item->delete();

         return Response::json(array('status' => 'correcto'));
    }
}
