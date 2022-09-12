<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use App\Models\Bookings;
use App\Models\User;

class AdminSeadedController extends Controller
{
    //
    public function index(Request $request)
    {
        $menu = "seaded";
        return view('backend.seaded.index', compact("menu"));
    }

    public function saveNotificationEmail(Request $request) 
    {
        $user = User::find($request->id);
        $user->notification_email = $request->email;
        $user->save();
        return response(json_encode(['success' => true]));
    }

    public function setNeedification(Request $request) 
    {
        $user = User::find($request->id);
        $user->need_notification = $request->needNotification ? 1 : 0;
        $user->save();
        return response(json_encode(['success' => true]));
    }
}