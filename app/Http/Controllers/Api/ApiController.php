<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use App\User;

class ApiController extends Controller
{
  public function __construct()
  {
    $this->middleware('jwt.auth', ['except' => ['auth']]);
  }

  public function index()
  {
    return User::all();
  }

  public function signup(Request $req)
  {
    $credentials = $req->all();

    try {
      $user = User::create($credentials);
    } catch (JWTException $e) {
      return response()->json(['error' => 'User already exists.'], 401);
    }

    $token = JWTAuth::fromUser($user);
    return response()->json(compact('token'));
  }

  public function auth(Request $req)
  {
    $this->validate($req, [
      'email'     => 'required|email',
      'password'  => 'required'
    ]);

    $credentials = $req->only('email', 'password');

    try {
      // verify the credentials and create a token for user
      if(!$token = JWTAuth::attempt($credentials)){
    		return response()->json(['error' => 'Invalid credentials.'], 401);
    	}
    } catch (JWTException $e) {
      return response()->json(['error' => 'Could not create token.'], 500);
    }

  	return response()->json(compact('token'));
  }

  public function online($email)
  {
    return User::where('email', $email)->first();
  }
}
