<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index($room_id){
        return view('room',[
            'room_id' => $room_id
        ]);
    }

}
