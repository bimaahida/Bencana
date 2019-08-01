<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;
use Banjir\UserModel;
use Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(
            'title' => "Please Sign In",
            'action' => "UserController@login",
            'button' => "Sign In",
            'metod' => "post"
        );

        return view('login.auth',$data);
    }
    public function login(Request $request){
        $rule = array(
            'username' => 'required',
            'password' => 'required',
        );
        
        $validator = Validator::make($request->all(),$rule);

        if($validator->fails()){
            return redirect()
                ->route('auth.index')
                ->withErrors($validator)
                ->withInput();
        }else{
            $data = UserModel::where('username',$request->username)->where('password',md5($request->password))->first();
            if(!empty($data)){
                $data_session = array(
                    'username' => $data->username, 
                    'name' => $data->name,
                );
                session()->put('sessionAuth', $data_session);
                // dd($data_session);

                return redirect()
                    ->route('firstPage')
                    ->with('alert-success','Insert record Sucsessed!');
            }else{
                return redirect()
                    ->route('auth.index')
                    ->withErrors('Failed login, wrong username or password')
                    ->withInput();
            }
        }
    }
    public function logout(){
        session()->flush();
        // $data = session()->get('sessionAuth');
        
        return redirect()
            ->route('firstPage')
            ->with('alert-success','Logout Success!');
    }
}
