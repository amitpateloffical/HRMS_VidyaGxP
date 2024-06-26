<?php

namespace App\Http\Controllers\Admin;
use  App\Models\AcitivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(){
        $title = 'activity';
        $AcitivityLog =AcitivityLog::orderby('id', 'desc')->get();
        
        return view('backend.activity',compact(
            'title','AcitivityLog'
        ));
    }

    public function markAsRead(){
        foreach (auth()->user()->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return back()->with('success',"Notifications has been cleared");
    }
}
