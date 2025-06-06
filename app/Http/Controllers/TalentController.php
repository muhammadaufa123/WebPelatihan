<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TalentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Dashboard';
        $roles = 'Talent';
        $assignedKelas = [];

        return view('admin.talent.dashboard', compact('user', 'title', 'roles', 'assignedKelas'));
    }
}
