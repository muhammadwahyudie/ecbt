<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        return redirect()->to("/{$role}/dashboard");
    }
}
