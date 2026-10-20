<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Bidang;
use App\Models\Auth\User;
use App\Models\Sppd_user;
use Illuminate\Support\Facades\Redirect;

class CetakController extends Controller
{
    public function index()
    {
        return Redirect(Route('SPPD'));
    }

    /**
     * Siapkan data bersama untuk semua dokumen cetak (SPT, SPPD, Kwitansi).
     * Mengembalikan [sppd, sppduser, sppduserpegawai, lama, bidang, user]
     * atau null bila pengguna tidak punya akses.
     */
    protected function prepare($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if (!Sppd::CekUserSppd($user, $sppd)) {
            return [null, $user];
        }
        $sppd = $sppd->first();
        $sppd->update(['tgl_surat' => now()]);

        $sppduser = Sppd_user::orderBy('created_at', 'ASC')->where('sppd_id', $id)->get();
        $sppduserpegawai = User::get();
        $lama = Sppd::selisih($sppd->tgl_pergi, $sppd->tgl_kembali);
        $bidang = Bidang::first();

        return [compact('user', 'sppd', 'lama', 'sppduser', 'sppduserpegawai', 'bidang'), $user];
    }

    public function SPT($id)
    {
        [$data, $user] = $this->prepare($id);
        return $data
            ? view('cetak.spt', $data)
            : view('sppd.users.akses', compact('user'));
    }

    public function SPPD($id)
    {
        [$data, $user] = $this->prepare($id);
        return $data
            ? view('cetak.sppd', $data)
            : view('sppd.users.akses', compact('user'));
    }

    public function kwitansi($id)
    {
        [$data, $user] = $this->prepare($id);
        return $data
            ? view('cetak.kwitansi', $data)
            : view('sppd.users.akses', compact('user'));
    }
}
