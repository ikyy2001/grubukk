<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\Eselon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $golongan = User::enum_get('golongan', 'golongan');
        $eselon = User::enum_get('eselon', 'nama_eselon');
        return view('auth.register', compact('golongan', 'eselon'));
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->create($request->all());

        session()->flash('Success', 'Akun berhasil dibuat! Silahkan login dengan NIP dan Password Anda.');
        return redirect()->route('login');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nama'      => ['required', 'string', 'max:125'],
            'nip'       => ['required', 'string', 'min:3', 'max:20', 'unique:users,nip'],
            'password'  => ['required', 'string', 'min:4'],
            'role'      => ['nullable', 'string', 'in:Staff,Kepala Seksi'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);
    }

    protected function create(array $data)
    {
        $role = in_array($data['role'] ?? '', ['Staff', 'Kepala Seksi']) ? $data['role'] : 'Staff';

        $user = User::create([
            'nama'          => $data['nama'],
            'nip'           => $data['nip'],
            'alamat'        => $data['alamat'] ?? null,
            'password'      => Hash::make($data['password']),
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'role'          => $role,
            'cek'           => 1,
        ]);

        Jabatan::create([
            'user_id' => $user->id,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        Golongan::create([
            'user_id' => $user->id,
            'golongan' => $data['golongan'] ?? null,
        ]);

        Eselon::create([
            'user_id' => $user->id,
            'nama_eselon' => $data['eselon'] ?? null,
        ]);

        return $user;
    }
}
