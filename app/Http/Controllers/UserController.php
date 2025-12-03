<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function insertFakeUsers(Request $request)
    {
        $numberOfUsers = $request->input('number_of_users', 5);

        User::factory()->count($numberOfUsers)->create();

        return back()->with('status', "$numberOfUsers fake users inserted successfully!");
    }
}
