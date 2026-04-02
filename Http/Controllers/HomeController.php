<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->is_admin) {
                return redirect('/admin');
            }

            return redirect('/portal/dashboard');
        }

        return view('welcome');
    }
}
