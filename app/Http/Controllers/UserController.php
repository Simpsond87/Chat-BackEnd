<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use Purifier;
use Hash;
use Auth;
use JWTAuth;
use Vinkla\Pusher\PusherManager;

use App\User;

class UserController extends Controller
{
  protected $pusher;

  public function __construct(PusherManager $pusher)
  {
    $this->middleware('jwt.auth', ['only' => ['getUser', 'signOut', 'leaveRoom']]);
    $this->pusher = $pusher;
  }

  public function signUp(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);

    if($validator->fails())
    {
      return Response::json(['error' => 'Missing field']);
    }

    $username = $request->input('username');
    $password = $request->input('password');

    //checks to see if username already exists
    $duplicate = User::where('username', '=', $username)->select('id')->first();
    if(empty($duplicate))
    {
      $password = Hash::make($password);

      $user = new User;
      $user->username = $username;
      $user->password = $password;
      $user->isonline = 0;
      $user->roomID = 0;
      $user->save();

      return Response::json(['success' => 'User Signed Up']);
    }
    else
    {
      return Response::json(['error' => 'Username Unavailable']);
    }
  }

  public function signIn(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);

    if($validator->fails())
    {
      return Response::json(['error' => 'Missing field']);
    }

    $username = $request->input('username');
    $password = $request->input('password');
    $credentials = compact("username", "password");

    $token = JWTAuth::attempt($credentials);

    if($token ==  false)
    {
      return Response::json(['error' => 'Invalid Credentials']);
    }
    else
    {
      $user = User::where('username', '=', $username)->first();
      $user->isonline = 1;
      $user->save();
      return Response::json(['token' => $token]);
    }
  }

  public function getUser()
  {
    $user = Auth::user();
    $user = User::find($user->id);

    return Response::json(['user' => $user]);
  }

  public function getUsers()
  {
    $users = User::all();

    return Response::json(['usernames' => $users]);
  }

  public function getOnline($id)
  {
    $users = User::where('isonline', '=', 1)->where('roomID', '=', $id)->get();
    return Response::json(['users' => $users]);
  }

  public function signOut()
  {
    $user = Auth::user();
    $user = User::find($user->id);

    $user->roomID = 0;
    $user->isonline = 0;
    $user->save();

    return Response::json(['success' => 'goodbye']);
  }

  public function leaveRoom()
  {
    $user = Auth::user();
    $user = User::find($user->id);

    $user->roomID = 0;
    $user->save();

    return Response::json(['success' => 'goodbye']);
  }

}
