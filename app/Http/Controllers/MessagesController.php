<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use Purifier;
use Hash;
use Auth;
use JWTAuth;

use App\User;

class MessagesController extends Controller
{
  public function sendMessage()
  {
    $rules = [
      'message' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);

    if($validator->fails())
    {
      return Response::json(['error' => 'Missing field']);
    }

    $message = $request->input('message');
    
    $messages = new Message;
    $messages->content = $message;
    $messages->save();

    return Response::json(['success' => 'Message saved']);
  }
}
