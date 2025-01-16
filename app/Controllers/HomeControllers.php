<?php

namespace App\Controllers;


class HomeControllers extends BaseController
{
    public function index(): string
    {
        echo view('dashboard/dashboard');
        return view('home');
    }


}
