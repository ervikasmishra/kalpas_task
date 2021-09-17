<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class ApiController extends Controller
{
    //PASSPOSRT LOGIN & REGISTRATION
    
    //registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'phone'=> 'required_without:email',
            'email'=> 'required_without:phone|email',
            'password'=>'required',
            'c_password'=>'required|same:password'
        ]);
        if($validator->fails()){
            return response()->json([$validator->errors()], 202);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $responseArray = [];
        $responseArray['token'] = $user->createToken('MyApp')->accessToken;
        $responseArray['name'] = $user->name;
        $responseArray['phone'] = $user->phone;

        return response()->json($responseArray,200);
    }

    ///   login ///

    public function login(Request $request){
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) || Auth::attempt(['phone'=>$request->phone, 'password'=>$request->password])){
            $user = Auth::user();
            $responseArray = [];
            $responseArray['token'] = $user->createToken('MyApp')->accessToken;
            $responseArray['name'] = $user->name;
            $responseArray['msg'] = 'You are login';
    
            return response()->json($responseArray,200);
        }
        // elseif(Auth::attempt(['phone'=>$request->phone, 'password'=>$request->password])){
        //     $user = Auth::user();
        //     $responseArray = [];
        //     $responseArray['token'] = $user->createToken('MyApp')->accessToken;
        //     $responseArray['name'] = $user->name; 
        //     // $responseArray['phone'] = $user->phone; 

        //     return response()->json(['you are login successfully by phone password'],200);

        // }
        else{
            return response()->json(['error=>Unauthorized'],203);
        }
    }

    public function update($id, Request $request){

        $users = user::find($id);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $users->name = $input['name'];
        $users->phone = $input['phone'];
        $users->email = $input['email'];
        $users->password = $input['password'];
        $users->save();
  
        $data[] = [
          'id'=>$users->id,
          'name'=>$users->name,
          'phone'=>$users->phone,
          'email'=>$users->email,
          'status'=>200,
        ];
        return response()->json($data);
  
      }
}

