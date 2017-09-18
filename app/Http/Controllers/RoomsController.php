<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use Response;

class RoomsController extends Controller
{
    public function index()
    {
      $rooms = Room::all();

      return Response::json(['rooms' => $rooms]);
    }
}
