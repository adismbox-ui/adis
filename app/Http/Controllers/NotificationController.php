<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        // À remplacer par une vraie récupération de notifications
        $notifications = $apprenant ? $apprenant->notifications()->latest()->get() : collect();
        return view('apprenants.notifications', compact('notifications'));
    }
}
