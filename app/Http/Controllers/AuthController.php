<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return ['message' => $this->message->error('É necessário informar um endereço de e-mail válido!')->render()];
        }

        if(empty($request->password)){
            return ['message' => $this->message->error('É necessário informar uma senha!')->render()];
        }

        if(!Auth::validate($request->only(['email', 'password']))){
            return ['message' => $this->message->error('E-mail ou senha inválidos!')->render()];
        }

        Auth::attempt($request->only(['email', 'password']));
        return ['redirect' => route('chat.index')];
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('loginForm');
    }
}
