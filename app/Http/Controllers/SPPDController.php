<?php

namespace App\Http\Controllers;

use App\Models\Angkutan;
use App\Models\Tempat;
use App\Models\DasarSurat;
use App\Models\Kabid;
use App\Models\Sppd;
use App\Models\Dasar;
use App\Models\Nosurat;
use App\Models\Auth\User;
use App\Models\Bbsppd;
use App\Models\Kegiatan;
use App\Models\Keterangan;
use App\Models\Program;
use App\Models\Rekening;
use App\Models\Skpd;
use App\Models\Sppd_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SPPDController extends Controller
{
    public function index(Request $request)
    {
        $user = User::getUser();
        $q = $user->sppd()->with(['user:id,nama,nip,role', 'tempat'])->orderBy('id', 'desc');
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            // Nomor surat ditampilkan sebagai "no_surat / tahun_surat", jadi
            // pencarian juga harus mengenali format tersebut (dengan/tanpa spasi).
            $normalizedSearch = preg_replace('/\s*\/\s*/', '/', $search);
            $q->where(function ($qq) use ($search, $normalizedSearch) {
                $term = '%' . $search . '%';
                $qq->where('no_surat', 'LIKE', $term)
                    ->orWhere('acara', 'LIKE', $term)
                    ->orWhere('tahun_surat', 'LIKE', $term)
                    ->orWhereRaw("CONCAT(no_surat, '/', tahun_surat) LIKE ?", ['%' . $normalizedSearch . '%']);
            });
        }
        $sppd = $q->paginate(10)->withQueryString();
        return view('sppd.users.sppd', compact('user', 'sppd'));
    }
    public function entry()
    {
        $user = User::getUser();
        $tempatb = User::enum_get('tempat', 'tempat_berangkat');
        $pegawai = User::orderBy('nama', 'ASC')->get();
        $kabids = User::where('role', 'Kepala Bidang')->orderBy('nama', 'ASC')->get();
        if ($kabids->isEmpty()) {
            $kabids = User::orderBy('nama', 'ASC')->get();
        }
        return view('sppd.users.entry', compact('user', 'tempatb', 'pegawai', 'kabids'));
    }
    public function storeentry(Request $request)
    {
        $this->validate($request, [
            'Acara'             => 'required',
            'TempatBerangkat'   => 'required',
            'TempatTujuan'      => 'required',
            'Tanggal_Berangkat' => 'required|date',
            'Tanggal_Kembali'   => 'required|date|after_or_equal:Tanggal_Berangkat',
            'file_undangan'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'uang_muka'         => 'nullable|numeric|min:0'
        ]);

        $filePath = null;
        if ($request->hasFile('file_undangan')) {
            $filePath = $request->file('file_undangan')->store('dokumen_sppd', 'public');
        }

        $pegawaiId = $request->Pegawai_id ?? Auth::id();
        $assignedUser = User::find($pegawaiId) ?? User::find(Auth::id());

        if ($request->Kabid_id) {
            $kabid = User::find($request->Kabid_id);
        } else {
            $kabid = User::where('role', 'Kepala Bidang')->first() ?? $assignedUser;
        }

        $surat = Nosurat::getDataSurat();
        $sppd = Sppd::create([
            'no_surat'          => $surat->no_surat,
            'tahun_surat'       => $surat->tahun_surat,
            'tgl_pergi'         => $request->Tanggal_Berangkat,
            'tgl_kembali'       => $request->Tanggal_Kembali,
            'acara'             => $request->Acara,
            'status'            => 'pending_atasan',
            'file_undangan'     => $filePath,
            'uang_muka'         => $request->uang_muka ?? 0
        ]);

        // Attach pegawai yang ditugaskan
        if ($assignedUser) {
            $assignedUser->sppd()->attach($sppd->id);
        }
        // Jika yang menginput berbeda dari yang diperintah, lampirkan juga pembuatnya agar bisa memantau
        if (Auth::id() != $assignedUser->id) {
            $creator = User::find(Auth::id());
            if ($creator) {
                $creator->sppd()->syncWithoutDetaching([$sppd->id]);
            }
        }

        Tempat::create([
            'sppd_id'           => $sppd->id,
            'tempat_berangkat'  => $request->TempatBerangkat,
            'tempat_tujuan'     => $request->TempatTujuan
        ]);

        Kabid::create([
            'sppd_id'    => $sppd->id,
            'nama_kabid' => $kabid->nama ?? 'Kepala Bidang',
            'nip'        => $kabid->nip ?? '-',
            'jabatan'    => ($kabid && $kabid->jabatan) ? $kabid->jabatan->jabatan : 'Kepala Bidang'
        ]);

        Angkutan::create([
            'sppd_id' => $sppd->id
        ]);
        Keterangan::create([
            'sppd_id' => $sppd->id
        ]);
        Bbsppd::create([
            'sppd_id' => $sppd->id
        ]);

        // Update Surat
        $this->updatenosurat($surat);
        $dasar = Dasar::get();
        foreach ($dasar as $isi) {
            DasarSurat::create([
                'sppd_id'     => $sppd->id,
                'dasar_surat' => $isi->dasar
            ]);
        }

        session()->flash('Success', 'Pengajuan SPPD berhasil dibuat dan menunggu persetujuan Atasan.');
        return Redirect(Route('SPPD'));
    }
    public function updatenosurat($surat)
    {
        $nosurat = $surat->no_surat + 1;
        $surat->update(['no_surat' => $nosurat]);
    }
    public function showsppd($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if ($this->CekUserSppd($user, $sppd)) {
            $sppd = $sppd->first();
            if (!$sppd->tgl_surat) {
                $sppd->update([
                    'tgl_surat' => now()
                ]);
            }
            return view('sppd.users.sppdoption', compact('user', 'sppd'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }
    public static function CekUserSppd($id_user, $id_sppd)
    {
        foreach ($id_sppd as $sppd_data) {
            foreach ($sppd_data->user as $user) {
                if (($user->id == $id_user->id || $id_user->role == 'Admin')) {
                    return true;
                }
            }
        }
    }
    public function editsppd($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if ($this->CekUserSppd($user, $sppd)) {
            $sppd = $sppd->first();
            $tempatb = User::enum_get('tempat', 'tempat_berangkat');
            $pegawai = User::orderBy('nama', 'ASC')->get();
            $kabids = User::where('role', 'Kepala Bidang')->orderBy('nama', 'ASC')->get();
            if ($kabids->isEmpty()) {
                $kabids = User::orderBy('nama', 'ASC')->get();
            }
            $primaryPivot = Sppd_user::orderBy('created_at', 'ASC')->where('sppd_id', $sppd->id)->first();
            $primaryUserId = $primaryPivot ? $primaryPivot->users_id : null;
            return view('sppd.users.edit', compact('user', 'sppd', 'tempatb', 'pegawai', 'kabids', 'primaryUserId'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }

    public function updatesppd(Request $request, $id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if (!$this->CekUserSppd($user, $sppd)) {
            return view('sppd.users.akses', compact('user'));
        }
        $sppd = $sppd->first();

        $this->validate($request, [
            'Acara'             => 'required',
            'TempatBerangkat'   => 'required',
            'TempatTujuan'      => 'required',
            'Tanggal_Berangkat' => 'required|date',
            'Tanggal_Kembali'   => 'required|date|after_or_equal:Tanggal_Berangkat',
            'file_undangan'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'uang_muka'         => 'nullable|numeric|min:0'
        ]);

        $updateData = [
            'acara'       => $request->Acara,
            'tgl_pergi'   => $request->Tanggal_Berangkat,
            'tgl_kembali' => $request->Tanggal_Kembali,
            'uang_muka'   => $request->uang_muka ?? 0,
        ];

        // Jika sebelumnya ditolak, reset status kembali ke pending_atasan agar atasan bisa mereview ulang
        if ($sppd->status == 'rejected') {
            $updateData['status'] = 'pending_atasan';
            $updateData['catatan_revisi'] = null;
        }

        if ($request->hasFile('file_undangan')) {
            $updateData['file_undangan'] = $request->file('file_undangan')->store('dokumen_sppd', 'public');
        }

        $sppd->update($updateData);

        // Update Tempat
        Tempat::updateOrCreate(
            ['sppd_id' => $sppd->id],
            [
                'tempat_berangkat' => $request->TempatBerangkat,
                'tempat_tujuan'    => $request->TempatTujuan,
            ]
        );

        // Update Pejabat Kabid
        if ($request->Kabid_id) {
            $kabid = User::find($request->Kabid_id);
            if ($kabid) {
                Kabid::updateOrCreate(
                    ['sppd_id' => $sppd->id],
                    [
                        'nama_kabid' => $kabid->nama ?? 'Kepala Bidang',
                        'nip'        => $kabid->nip ?? '-',
                        'jabatan'    => ($kabid->jabatan) ? $kabid->jabatan->jabatan : 'Kepala Bidang',
                    ]
                );
            }
        }

        // Update Pegawai Utama
        if ($request->Pegawai_id) {
            $primaryPivot = Sppd_user::orderBy('created_at', 'ASC')->where('sppd_id', $sppd->id)->first();
            if ($primaryPivot) {
                $primaryPivot->update(['users_id' => $request->Pegawai_id]);
            } else {
                $sppd->user()->attach($request->Pegawai_id);
            }
        }

        session()->flash('Success', 'Data SPPD berhasil diperbarui' . ($sppd->status == 'pending_atasan' ? ' dan diajukan ulang ke Atasan.' : '.'));
        return redirect(route('SPPD') . '/' . $sppd->id . '/SPPD');
    }
    public function addfollower($id)
    {
        $user = User::getUser();
        $all_user = User::with('jabatan')->where('cek', 1)->orderBy('nama', 'asc')->get();
        $sppduser = Sppd_user::orderBy('created_at', 'ASC')->where('sppd_id', $id)->get();
        $sppduserpegawai = User::get();
        $sppd = Sppd::getSppd($id);
        if ($this->CekUserSppd($user, $sppd)) {
            $sppd = $sppd->first();
            return view('sppd.users.addfollower', compact('user', 'all_user', 'sppd', 'sppduser', 'sppduserpegawai'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }
    public function storeaddfollower(Request $request, $id)
    {
        if ($request->nip != null) {
            User::where('nip', $request->nip)->first()->sppd()->attach($id);
            session()->flash('Success', 'Berhasil Menambah Pengikut');
            return back();
        } else {
            session()->flash('Failed', 'Gagal Menambah Pengikut');
            return back();
        }
    }
    public function removefollower($sppd_id, $users_id)
    {
        $firstUser = Sppd_user::orderBy('created_at', 'ASC')->where('sppd_id', $sppd_id)->first();
        if ($firstUser && $firstUser->users_id == $users_id) {
            session()->flash('Failed', 'Pegawai utama yang diperintahkan tidak dapat dihapus dari SPPD.');
            return back();
        }

        Sppd_user::where('sppd_id', $sppd_id)->where('users_id', $users_id)->delete();
        session()->flash('Success', 'Berhasil Menghapus Pengikut');
        return back();
    }
    public function angkutan($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if ($this->CekUserSppd($user, $sppd)) {
            $tempatb = User::enum_get('tempat', 'tempat_berangkat');
            $sppd = $sppd->first();
            return view('sppd.users.angkutan', compact('user', 'sppd', 'tempatb'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }
    public function storeangkutan($id, Request $request)
    {
        $angkutan = Angkutan::where('sppd_id', $id)->first();
        $angkutan->update([
            'angkutan' => $request->Angkutan
        ]);
        session()->flash('Success', 'Berhasil Set Angkutan');
        return back();
    }
    public function storejenis($id, Request $request)
    {
        $angkutan = Angkutan::where('sppd_id', $id)->first();
        $angkutan->update([
            'jenis' => $request->Jenis
        ]);
        session()->flash('Success', 'Berhasil Set Jenis Kendaraan');
        return back();
    }
    public function storeplat($id, Request $request)
    {
        $angkutan = Angkutan::where('sppd_id', $id)->first();
        $angkutan->update([
            'plat' => $request->Plat
        ]);
        session()->flash('Success', 'Berhasil Set Nomor Plat');
        return back();
    }
    public function storeumum($id, Request $request)
    {
        $angkutan = Angkutan::where('sppd_id', $id)->first();
        $angkutan->update([
            'angkutan_umum' => $request->Umum
        ]);
        session()->flash('Success', 'Berhasil Set Kendaraan Umum');
        return back();
    }
    public function storesewa($id, Request $request)
    {
        $angkutan = Angkutan::where('sppd_id', $id)->first();
        $angkutan->update([
            'sewa' => $request->Sewa
        ]);
        session()->flash('Success', 'Berhasil Set Kendaraan Sewa');
        return back();
    }
    public function bebanbiaya($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        $skpd = Skpd::get();
        $program = Program::get();
        $kegiatan = Kegiatan::get();
        $rekening = Rekening::get();
        $bbsppd = Bbsppd::where('sppd_id', $id)->first();
        if ($this->CekUserSppd($user, $sppd)) {
            $sppd = $sppd->first();
            return view('sppd.users.bebanbiaya', compact('user', 'sppd','skpd','program','kegiatan','rekening','bbsppd'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }
    public function storebebanbiaya($id, Request $request)
    {
        $bbsppd = Bbsppd::where('sppd_id', $id)->first();
        $bbsppd->update([
            'skpd' => $request->SKPD,
            'program' => $request->Program,
            'kegiatan' => $request->Kegiatan,
            'rekening' => $request->KodeRekening
        ]);
        session()->flash('Success', 'Berhasil Set Pembebanan Anggaran');
        return back();
    }
    public function selesai($id)
    {
        return Redirect(Route('SPPD').'/'.$id.'/SPPD');
    }

    public function keterangan($id)
    {
        $user = User::getUser();
        $sppd = Sppd::getSppd($id);
        if ($this->CekUserSppd($user, $sppd)) {
            $sppd = $sppd->first();
            return view('sppd.users.keterangan', compact('user', 'sppd'));
        } else {
            return view('sppd.users.akses', compact('user'));
        }
    }
    public function storeketerangan($id, Request $request)
    {
        $keterangan = Keterangan::where('sppd_id', $id)->first();
        $keterangan->update([
            'keterangan' => $request->Keterangan
        ]);
        session()->flash('Success', 'Berhasil Set Keterangan');
        return back();
    }
    public function deletesppd($id)
    {
        $user = User::getUser();
        $sppd = Sppd::find($id);
        if (!$sppd) {
            session()->flash('Failed', 'SPPD tidak ditemukan');
            return back();
        }
        if (!$this->CekUserSppd($user, collect([$sppd]))) {
            session()->flash('Failed', 'Anda tidak memiliki hak untuk menghapus SPPD ini');
            return back();
        }

        DB::transaction(function () use ($sppd, $id) {
            DasarSurat::where('sppd_id', $id)->delete();
            $sppd->user()->detach();
            $sppd->delete();
        });

        session()->flash('Success', 'Berhasil Menghapus SPPD');
        return redirect()->route('SPPD');
    }
}
