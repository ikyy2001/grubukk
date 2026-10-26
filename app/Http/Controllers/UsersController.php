<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Auth\User;
use App\Models\Eselon;
use App\Models\Golongan;
use App\Models\Sppd;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{
    // Auth
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Dashboard
    public function dashboard()
    {
        $user = User::getUser();
        $isAdmin = $user->role === 'Admin';

        // Kueri dasar SPPD: admin melihat seluruh SPPD, yang lain miliknya sendiri.
        $base = function () use ($isAdmin, $user) {
            $q = Sppd::query();
            if (!$isAdmin) {
                $q->whereHas('user', fn ($qq) => $qq->whereKey($user->getKey()));
            }
            return $q;
        };

        $totalSppd = (clone $base())->count();
        $now = now();

        $bulanIni = (clone $base())
            ->whereBetween('tgl_pergi', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();
        $bulanLalu = (clone $base())
            ->whereBetween('tgl_pergi', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])
            ->count();
        $berjalan = (clone $base())
            ->whereDate('tgl_pergi', '<=', $now->toDateString())
            ->whereDate('tgl_kembali', '>=', $now->toDateString())
            ->count();

        // Deret 8 bulan terakhir untuk sparkline & grafik ringkas.
        $months = collect(range(7, 0))->map(fn ($i) => $now->copy()->subMonths($i)->startOfMonth());
        $perMonth = (clone $base())
            ->selectRaw("DATE_FORMAT(tgl_pergi, '%Y-%m') AS ym, COUNT(*) AS c")
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $series = $months->map(fn ($m) => (int) ($perMonth[$m->format('Y-m')] ?? 0))->values();
        $spark = $series->slice(-8)->values();

        // Seri pembanding: 8 bulan yang sama pada tahun lalu.
        $monthsLastYear = $months->map(fn ($m) => $m->copy()->subYear());
        $perMonthLastYear = (clone $base())
            ->selectRaw("DATE_FORMAT(tgl_pergi, '%Y-%m') AS ym, COUNT(*) AS c")
            ->whereBetween('tgl_pergi', [
                $monthsLastYear->first()->copy()->startOfMonth(),
                $monthsLastYear->last()->copy()->endOfMonth(),
            ])
            ->groupBy('ym')
            ->pluck('c', 'ym');

        $seriesLastYear = $monthsLastYear->map(fn ($m) => (int) ($perMonthLastYear[$m->format('Y-m')] ?? 0))->values();

        $thisYearTotal = (clone $base())->whereYear('tgl_pergi', $now->year)->count();
        $lastYearTotal = (clone $base())->whereYear('tgl_pergi', $now->year - 1)->count();

        $deltaPct = $bulanLalu > 0
            ? round(($bulanIni - $bulanLalu) / $bulanLalu * 100, 1)
            : ($bulanIni > 0 ? 100.0 : 0.0);

        $pegawaiAktif = User::where('cek', 1)->count();
        $roleCounts = User::where('cek', 1)
            ->selectRaw('role, COUNT(*) AS c')
            ->groupBy('role')
            ->pluck('c', 'role');

        $recent = (clone $base())
            ->with(['user', 'tempat'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $bulanLabels = $months->map(fn ($m) => $m->isoFormat('MMM'))->values();
        $periodeLabel = $months->first()->isoFormat('MMM YYYY') . ' – ' . $months->last()->isoFormat('MMM YYYY');

        return view('dashboard', [
            'user'           => $user,
            'totalSppd'      => $totalSppd,
            'bulanIni'       => $bulanIni,
            'deltaPct'       => $deltaPct,
            'berjalan'       => $berjalan,
            'spark'          => $spark,
            'series'         => $series,
            'seriesLastYear' => $seriesLastYear,
            'bulanLabels'    => $bulanLabels,
            'periodeLabel'   => $periodeLabel,
            'thisYearTotal'  => $thisYearTotal,
            'lastYearTotal'  => $lastYearTotal,
            'pegawaiAktif'   => $pegawaiAktif,
            'roleCounts'     => $roleCounts,
            'recent'         => $recent,
        ]);
    }
    // Pegawai & show Pegawai
    public function pegawai(Request $request)
    {
        $q = User::with(['jabatan', 'golongan'])
            ->where('cek', 1)
            ->orderBy('nama', 'asc');
        if ($request->search) {
            $q->where(function ($qq) use ($request) {
                $qq->where('nama', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }
        $users = $q->paginate(10)->withQueryString();
        return view('users.pegawai.pegawai', ['users' => $users, 'user' => User::getUser()]);
    }
    public function nip($nip)
    {
        $user_pegawai = User::select('*')->where('nip', $nip)->first();
        return view('users.pegawai.nip', ['user' => User::getUser()], ['user_pegawai' => $user_pegawai]);
    }

    // Administrator Show Users, Create User , Store User
    public function show(Request $request)
    {
        $q = User::with(['jabatan', 'golongan'])->orderBy('nama', 'asc');
        if ($request->search) {
            $q->where(function ($qq) use ($request) {
                $qq->where('nama', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }
        $users = $q->paginate(10)->withQueryString();
        return view('users.admin.users', ['users' => $users, 'user' => User::getUser()]);
    }
    public function create()
    {
        $golongan = User::enum_get('golongan', 'golongan');
        $eselon = User::enum_get('eselon', 'nama_eselon');
        $user = User::getUser();
        return view('users.admin.create', compact('golongan','eselon','user'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'Nama'      => 'required',
            'NIP'       =>  'required|min:10|numeric',
            'Password'  =>  'required|min:5',
            'Role'      =>  'required|numeric'
        ]);
        // Kolom `role` bertipe enum teks; peta angka 1-5 dari form ke nilai enum.
        $roleMap = [1 => 'Admin', 2 => 'Kepala Bidang', 3 => 'Kepala Seksi', 4 => 'Staff', 5 => 'Keuangan'];
        $Cek = User::where('nip', $request->NIP)->count();

        if ($Cek != True) {
            User::create([
                'nama'      => $request->Nama,
                'nip'       => $request->NIP,
                'alamat'    => $request->Alamat,
                'password'  => Hash::make($request->Password),
                'tanggal_lahir'  => $request->TglLahir,
                'created_at' => now(),
                'role'      => $roleMap[$request->Role] ?? 'Staff'
            ]);
            $user_temp = User::select('id')->where('nip', $request->NIP)->first();
            Jabatan::create([
                'user_id' => $user_temp->id,
                'jabatan' => $request->Jabatan
            ]);
            Golongan::create([
                'user_id' => $user_temp->id,
                'golongan' => $request->Golongan
            ]);
            Eselon::create([
                'user_id' => $user_temp->id,
                'nama_eselon' => $request->Eselon
            ]);
            session()->flash('Success', 'Berhasil Membuat User');
        } else {
            session()->flash('Failed', 'Gagal Membuat User (NIP Sudah Dipakai)');
        }
        return redirect(Route('Admin/Show'));
    }
    public function showuser($nip)
    {
        $user_pegawai = User::select('*')->where('nip', $nip)->first();
        $user = User::getUser();
        return view('users.admin.profile', compact('user','user_pegawai'));
    }
    public function changepass($nip)
    {
        $user_pegawai = User::select('*')->where('nip', $nip)->first();
        return view('users.admin.changepassword', ['user' => User::getUser()], ['user_pegawai' => $user_pegawai]);
    }
    public function storechangepass(Request $request, $nip)
    {
        $user_pegawai = User::select('*')->where('nip', $nip)->first();
        $this->validate($request, [
            'password'              =>  'required|min:5',
            'password_confirmation' =>  'required|min:5',
        ]);
        $pass    = $request->password;
        $passkon = $request->password_confirmation;
        $user = $user_pegawai;
        if ($pass == $passkon) {
            $user->update([
                'password'  => Hash::make($request->password),
                'updated_at' => now()
            ]);
            session()->flash('Success', '(Berhasil) Mengganti Password User ' . $user->nama);
            return redirect(Route('Admin/Show'));
        } else {
            session()->flash('Failed', '(Gagal) Password dan Password Konfirmasi Tidak Sama');
            return back();
        }
    }
    public function showedituser($nip)
    {
        $user_pegawai = User::select('*')->where('nip', $nip)->first();
        $golongan = User::enum_get('golongan', 'golongan');
        $eselon = User::enum_get('eselon', 'nama_eselon');
        $user = User::getUser();
        return view('users.admin.edit', compact('user_pegawai','golongan','eselon','user'));
    }
    public function storeedituser(Request $request, $nip)
    {
        $user_pegawai = User::where('nip', $nip)->firstOrFail();
        $this->validate($request, [
            'Nama'      => 'required',
            'NIP'       =>  'required|min:10|numeric'
        ]);
        $roleMap = [1 => 'Admin', 2 => 'Kepala Bidang', 3 => 'Kepala Seksi', 4 => 'Staff', 5 => 'Keuangan'];
        $user = $user_pegawai;
        $user->update([
            'nama'      => $request->Nama,
            'nip'       => $request->NIP,
            'alamat'    => $request->Alamat,
            'tanggal_lahir'  => $request->TglLahir,
            'role'      => $roleMap[$request->Role] ?? $user->role,
            'updated_at' => now()
        ]);
        Jabatan::updateOrCreate(
            ['user_id' => $user->id],
            ['jabatan' => $request->Jabatan]
        );
        Golongan::updateOrCreate(
            ['user_id' => $user->id],
            ['golongan' => $request->Golongan]
        );
        Eselon::updateOrCreate(
            ['user_id' => $user->id],
            ['nama_eselon' => $request->Eselon]
        );
        session()->flash('Success', '(Berhasil) Update User ' . $user_pegawai->nama);
        return redirect(Route('Admin/Show'));
    }
    public function deluser($nip)
    {
        $user_pegawai = User::where('nip', $nip)->firstOrFail();
        // Tandai tidak aktif lalu soft-delete (kolom `role` adalah enum, jangan diisi angka).
        $user_pegawai->update(['cek' => 0]);
        $user_pegawai->delete();
        session()->flash('Success', 'Berhasil Menghapus User');
        return Redirect(Route('Admin/Show'));
    }

    // Show Profile, Change Password, Store Password, Show Edit Profile
    public function profile()
    {
        return view('users.profile.profile', ['user' => User::getUser()]);
    }
    public function changepassword()
    {
        return view('users.profile.changepassword', ['user' => User::getUser()]);
    }
    public function storepass(Request $request)
    {
        $this->validate($request, [
            'password'              =>  'required|min:5',
            'password_confirmation' =>  'required|min:5',
        ]);
        $pass    = $request->password;
        $passkon = $request->password_confirmation;
        $user = User::getUser();
        if ($pass == $passkon) {
            $user->update([
                'password'  => Hash::make($request->password),
                'updated_at' => now()
            ]);
            session()->flash('Success', '(Berhasil) Mengganti Password');
            return redirect(Route('Dashboard'));
        } else {
            session()->flash('Failed', '(Gagal) Password dan Password Konfirmasi Tidak Sama');
            return redirect(Route('Users/Profile/ChangePassword'));
        }
    }
    public function showedit()
    {
        $golongan = User::enum_get('golongan', 'golongan');
        $eselon = User::enum_get('eselon', 'nama_eselon');
        $user = User::getUser();
        return view('users.profile.edit', compact('golongan','eselon','user'));
    }
    public function storeedit(Request $request)
    {
        $this->validate($request, [
            'Nama'      => 'required',
            'NIP'       =>  'required|min:10|numeric'
        ]);
        $user = User::select()->where('id', Auth::user()->id)->first();
        $user->update([
            'nama'      => $request->Nama,
            'nip'       => $request->NIP,
            'alamat'    => $request->Alamat,
            'tanggal_lahir'  => $request->TglLahir,
            'updated_at' => now()
        ]);
        Jabatan::updateOrCreate(
            ['user_id' => $user->id],
            ['jabatan' => $request->Jabatan]
        );
        Golongan::updateOrCreate(
            ['user_id' => $user->id],
            ['golongan' => $request->Golongan]
        );
        Eselon::updateOrCreate(
            ['user_id' => $user->id],
            ['nama_eselon' => $request->Eselon]
        );
        session()->flash('Success', '(Berhasil) Update Profil');
        return redirect(Route('Dashboard'));
    }
    public function del()
    {
        $user = User::getUser();
        if ($user) {
            $user->update(['cek' => 0]);
            $user->delete();
        }
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return Redirect()->route('login');
    }



    
}
