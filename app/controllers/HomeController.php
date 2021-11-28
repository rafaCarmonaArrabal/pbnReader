<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        return include VIEW_PATH . '/home/indexView.php';
    }
}
