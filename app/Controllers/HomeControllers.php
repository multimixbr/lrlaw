<?php

namespace App\Controllers;

class HomeControllers extends BaseController
{
    public function index(): string
    {
        return $this->render('home');
    }

}
