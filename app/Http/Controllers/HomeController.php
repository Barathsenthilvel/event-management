<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Public marketing homepage (GNAT Donation).
     * Content is static via config('homepage') until wired to the database.
     */
    public function index()
    {
        return view('home.index', config('homepage', []));
    }
}
