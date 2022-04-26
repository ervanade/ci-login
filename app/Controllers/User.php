<?php

namespace App\Controllers;


use App\Models\UserModel;
use App\Libraries\Hash;

class User extends BaseController
{
    public function __construct()
    {
        //Inisialisasi helper yang dipakai menggunakan constructor
        helper(['url', 'form']);
    }
    public function index()
    {
        // Arahkan Ke View Login
        return view('auth/login');
    }
    public function register()
    {
        // Arahkan Ke View Register
        return view('auth/register');
    }
    public function save()
    {
        // Membuat Aturan Validasi Sebelum Di Insert Ke Database
        $validation = $this->validate(
            [
                'username' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Username Wajib Diisi!'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required' => 'Email Wajib Diisi!',
                        'valid_email' => 'Email Harus Valid',
                        'is_unique' => 'Email Sudah Dipakai'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[5]',
                    'errors' => [
                        'required' => 'Password Wajib Diisi!',
                        'min_length' => 'Password Minimal 5 Characters'
                    ]
                ],
                'password_confirm' => [
                    'rules' => 'required|min_length[5]|matches[password]',
                    'errors' => [
                        'required' => 'Password Confirm Wajib Diisi!',
                        'min_length' => 'Password Minimal 5 Characters',
                        'matches' => 'Konfirmasi Password Tidak Cocok'
                    ]
                ]
            ]
        );

        if (!$validation) {
            // JIka Validasi Gagal Kirim ke Halaman Registrasi Beserta Pesan Validator nya
            return view('auth/register', ['validation' => $this->validator]);
        } else {
            //INSERT Ke DATABASE
            $model = new UserModel();
            $newData = [
                'username' => $this->request->getVar('username'),
                'email' => $this->request->getVar('email'),
                'password' => Hash::make($this->request->getVar('password')),
            ];
            $model->insert($newData);
            // JIka Insert Data Berhasil Kirim ke Halaman Login Beserta Pesan Suksesnya
            return redirect()->to('/')->with('success', 'Registrasi Berhasil');
        }
    }
    public function login()
    {
        // Membuat Aturan Validasi Sebelum Login
        $validation = $this->validate(
            [
                'email' => [
                    'rules' => 'required|valid_email|is_not_unique[users.email]',
                    'errors' => [
                        'required' => 'Email Wajib Diisi!',
                        'valid_email' => 'Email Harus Valid',
                        'is_not_unique' => 'Email Belum Terdaftar'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password Wajib Diisi!'
                    ]
                ]
            ]
        );

        if (!$validation) {
            // JIka Validasi Gagal Kirim ke Halaman Registrasi Beserta Pesan Validator nya
            return view('auth/login', ['validation' => $this->validator]);
        } else {
            //Jika Validasi Berhasil Check Email & Password Ke Database
            $model = new UserModel();

            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');

            $user_info = $model->where('email', $email)->first();
            $check_password = Hash::check($password, $user_info['password']);

            if (!$check_password) {
                //Jika Password Salah Kirim ke Halaman Login dengan Pesan Errornya
                return redirect()->to('/')->with('Fail', 'Email / Password Salah');
            } else {
                //Jika Email & Password Benar Set Session dengan Data User
                $user_id = $user_info['id'];
                session()->set('loggedUser', $user_id);
                return redirect()->to('/dashboard');
            }
        }
    }

    public function logout()
    {
        if (session()->has('loggedUser')) {
            session()->remove('loggedUser');
            return redirect()->to('/?access=out')->with('Fail', 'Anda Telah Logout');
        }
    }
}
