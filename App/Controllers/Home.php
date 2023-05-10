<?php

declare(strict_types=1);
namespace app\Controllers;

class Home extends \App\Core\BaseController
{
    public function index()
    {
        return view('welcome_message');
    }
}
