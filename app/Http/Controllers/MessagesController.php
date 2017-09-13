<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use Purifier;
use Hash;
use Auth;
use JWTAuth;
use Vinkla\Pusher\Facades\Pusher;

use App\User;
use App\Message;

class MessagesController extends Controller
{
  public function __construct()
  {
    $this->middleware('jwt.auth', ['only' => ['sendMessage']]);
  }

  public function getMessages($id)
  {
    $messages = Message::where('roomID', '=', $id)
    ->orderBy('messages.created_at', 'desc')
    ->join('users', 'messages.userID', '=', 'users.id')
    ->select('messages.id', 'messages.content', 'messages.created_at', 'users.username')
    ->take(30)
    ->get()->toArray();

    $messages = array_reverse($messages);

    return Response::json(['messages' => $messages]);
  }

  public function sendMessage(Request $request)
  {
    $rules = [
      'message' => 'required',
      'roomID' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);

    if($validator->fails())
    {
      return Response::json(['error' => 'Missing field']);
    }

    $message = $request->input('message');
    $roomID = $request->input('roomID');
    $user = Auth::user();

    $messages = new Message;
    $messages->userID = $user->id;
    $messages->roomID = $roomID;
    $messages->content = $message;
    $messages->save();

    $messageData = Message::where('messages.id', '=', $messages->id)
    ->join('users', 'messages.userID', '=', 'users.id')
    ->select('messages.id', 'messages.content', 'messages.created_at', 'users.username')
    ->first();

    Pusher::trigger('room_' . $roomID, 'send-message', ['message' => $messageData]);

    return Response::json(['success' => 'Message saved']);
  }
}
