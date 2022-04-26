<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        //Mengirim Data Session User Ke Halaman Dashboard
        $userModel = new UserModel();
        $loggedUserID = session()->get('loggedUser');
        $userinfo = $userModel->find($loggedUserID);
        $data = [
            'title' => 'dashboard',
            'userinfo' => $userinfo
        ];
        return view('user/index', $data);
    }
}
