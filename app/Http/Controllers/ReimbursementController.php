<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\SppdReimbursement;
use App\Models\SppdReimbursementItem;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReimbursementController extends Controller
{
    /**
     * Status SPPD yang sudah boleh melakukan pelaporan biaya riil.
     */
    private const SPPD_TERIMA_LAPORAN = ['approved', 'completed'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $user = User::getUser();
        $sppd = Sppd::with(['user', 'tempat', 'reimbursement.items'])->findOrFail($id);

        // Pastikan pengguna berhak (Admin, Keuangan, Kepala Bidang, atau pemilik SPPD)
        if (!$this->bolehAkses($user, $sppd)) {
            return view('sppd.users.akses', compact('user'));
        }

        // Pelaporan biaya riil hanya setelah SPPD disetujui penuh oleh Keuangan
        if (!in_array($sppd->status, self::SPPD_TERIMA_LAPORAN)) {
            session()->flash('Failed', 'Pelaporan Biaya Riil & Reimbursement baru dapat diakses setelah SPPD disetujui oleh Bagian Keuangan.');
            return redirect()->route('SPPD');
        }

        $reimbursement = $this->ambilAtauBuatReimbursement($sppd);
        $reimbursement->load('items');

        $bolehEditItem     = $this->bolehEditItem($user, $sppd, $reimbursement);
        $bolehAturUangMuka = $this->bolehAturUangMuka($user, $sppd, $reimbursement);

        return view('sppd.reimbursement.index', [
            'user'              => $user,
            'sppd'              => $sppd,
            'reimbursement'     => $reimbursement,
            'bolehEditItem'     => $bolehEditItem,
            'bolehAturUangMuka' => $bolehAturUangMuka,
            'pesanKunciItem'    => $this->pesanKunciItem($sppd, $reimbursement, $bolehEditItem),
            'pesanKunciPanjar'  => $this->pesanKunciPanjar($reimbursement, $bolehAturUangMuka),
            'estimasiUangMuka'  => (float) ($sppd->uang_muka ?? 0),
        ]);
    }

    /**
     * Simpan uang muka / panjar yang benar-benar dicairkan.
     */
    public function setUangMuka(Request $request, $id)
    {
        $this->validate($request, [
            'uang_muka' => 'required|numeric|min:0|max:99999999999',
        ]);

        $user = User::getUser();
        $sppd = Sppd::with('user')->findOrFail($id);

        if (!$this->bolehAkses($user, $sppd)) {
            session()->flash('Failed', 'Anda tidak memiliki hak akses untuk SPPD ini.');
            return back();
        }

        $reimbursement = $this->ambilAtauBuatReimbursement($sppd);

        if (!$this->bolehAturUangMuka($user, $sppd, $reimbursement)) {
            session()->flash('Failed', 'Uang muka / panjar tidak dapat diubah (sudah diverifikasi keuangan atau hak akses terbatas).');
            return back();
        }

        $reimbursement->forceFill([
            'uang_muka'             => $request->uang_muka,
            'uang_muka_ditetapkan' => true,
        ])->save();

        $reimbursement->hitungUlang();

        session()->flash('Success', 'Uang muka / panjar dicairkan berhasil disimpan.');
        return back();
    }

    public function addItem(Request $request, $id)
    {
        $this->validate($request, [
            'kategori'   => 'required|string',
            'keterangan' => 'required|string|max:255',
            'biaya'      => 'required|numeric|min:0',
            'file_nota'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $user = User::getUser();
        $sppd = Sppd::with('user')->findOrFail($id);

        if (!$this->bolehAkses($user, $sppd)) {
            session()->flash('Failed', 'Anda tidak memiliki hak akses untuk SPPD ini.');
            return back();
        }

        $reimbursement = $this->ambilAtauBuatReimbursement($sppd);

        if (!$this->bolehEditItem($user, $sppd, $reimbursement)) {
            session()->flash('Failed', 'Rincian nota sudah diverifikasi keuangan dan tidak dapat diubah lagi.');
            return back();
        }

        $notaPath = null;
        if ($request->hasFile('file_nota')) {
            $notaPath = $request->file('file_nota')->store('nota_reimbursement', 'public');
        }

        SppdReimbursementItem::create([
            'reimbursement_id' => $reimbursement->id,
            'kategori'         => $request->kategori,
            'keterangan'       => $request->keterangan,
            'biaya'            => $request->biaya,
            'file_nota'        => $notaPath
        ]);

        // Recalculate totals
        $reimbursement->hitungUlang();

        session()->flash('Success', 'Item pengeluaran riil berhasil ditambahkan.');
        return back();
    }

    public function deleteItem(Request $request, $itemId)
    {
        $user = User::getUser();
        $item = SppdReimbursementItem::with('reimbursement.sppd.user')->findOrFail($itemId);
        $reimbursement = $item->reimbursement;
        $sppd = $reimbursement->sppd;

        if (!$this->bolehAkses($user, $sppd)) {
            session()->flash('Failed', 'Anda tidak memiliki hak akses untuk SPPD ini.');
            return back();
        }

        if (!$this->bolehEditItem($user, $sppd, $reimbursement)) {
            session()->flash('Failed', 'Rincian nota sudah diverifikasi keuangan dan tidak dapat diubah lagi.');
            return back();
        }

        if ($item->file_nota && Storage::disk('public')->exists($item->file_nota)) {
            Storage::disk('public')->delete($item->file_nota);
        }

        $item->delete();

        // Recalculate totals
        $reimbursement->hitungUlang();

        session()->flash('Success', 'Item pengeluaran berhasil dihapus.');
        return back();
    }

    public function verify(Request $request, $id)
    {
        $user = User::getUser();
        if (!in_array($user->role, ['Keuangan', 'Admin'])) {
            session()->flash('Failed', 'Hanya Bagian Keuangan yang dapat memverifikasi pengeluaran.');
            return back();
        }

        $this->validate($request, [
            'status_pembayaran' => 'required|in:pending,verified,paid',
            'catatan_keuangan'  => 'nullable|string|max:1000',
        ]);

        $sppd = Sppd::findOrFail($id);
        $reimbursement = $this->ambilAtauBuatReimbursement($sppd);

        $data = [
            'status_pembayaran' => $request->status_pembayaran,
        ];

        // Catatan tidak dihapus bila kolom dikirim kosong (yang tidak ingin diubah).
        if ($request->filled('catatan_keuangan')) {
            $data['catatan_keuangan'] = $request->catatan_keuangan;
        }

        $reimbursement->update($data);
        $reimbursement->hitungUlang();

        if ($request->status_pembayaran == 'paid') {
            $sppd->update(['status' => 'completed']);
        } elseif ($sppd->status == 'completed') {
            // Status dibalik ke pending (mis. kurang nota), SPPD kembali approved
            $sppd->update(['status' => 'approved']);
        }

        session()->flash('Success', 'Status pertanggungjawaban reimbursement berhasil diperbarui.');
        return back();
    }

    /**
     * Ambil data reimbursement milik SPPD, atau buat baru dengan kondisi kosong.
     * Nilai uang muka TIDAK lagi disalin otomatis dari estimasi pengajuan supaya
     * tidak muncul "tiba-tiba" sebelum ada inputan.
     */
    private function ambilAtauBuatReimbursement(Sppd $sppd): SppdReimbursement
    {
        $reimbursement = SppdReimbursement::firstOrCreate(
            ['sppd_id' => $sppd->id],
            [
                'total_pengeluaran_riil' => 0,
                'uang_muka'              => 0,
                'uang_muka_ditetapkan'  => false,
                'selisih'                => 0,
                'status_pembayaran'      => 'pending'
            ]
        );

        // Selaraskan total & selisih dengan data item terbaru
        $reimbursement->hitungUlang();

        return $reimbursement;
    }

    private function bolehAkses(User $user, Sppd $sppd): bool
    {
        if ($user->role === 'Admin' || $user->role === 'Keuangan' || $user->role === 'Kepala Bidang') {
            return true;
        }

        return $sppd->user->contains('id', $user->id);
    }

    /**
     * Item nota hanya boleh diubah oleh pegawai pemilik SPPD (atau Admin)
     * dan hanya selama belum diverifikasi keuangan.
     */
    private function bolehEditItem(User $user, Sppd $sppd, SppdReimbursement $reimbursement): bool
    {
        if (!$reimbursement->masihBisaDiubah()) {
            return false;
        }

        if ($user->role === 'Admin') {
            return true;
        }

        return $sppd->user->contains('id', $user->id);
    }

    /**
     * Uang muka / panjar boleh ditetapkan oleh pemilik SPPD, Keuangan atau Admin
     * selama belum diverifikasi keuangan.
     */
    private function bolehAturUangMuka(User $user, Sppd $sppd, SppdReimbursement $reimbursement): bool
    {
        if (!$reimbursement->masihBisaDiubah()) {
            return false;
        }

        if (in_array($user->role, ['Admin', 'Keuangan'])) {
            return true;
        }

        return $sppd->user->contains('id', $user->id);
    }

    private function pesanKunciItem(Sppd $sppd, SppdReimbursement $reimbursement, bool $bolehEditItem): string
    {
        if ($bolehEditItem) {
            return '';
        }

        if (!$reimbursement->masihBisaDiubah()) {
            return 'Rincian nota sudah diverifikasi Bagian Keuangan dan dikunci.';
        }

        return 'Anda hanya dapat melihat rincian nota pengeluaran SPPD ini.';
    }

    private function pesanKunciPanjar(SppdReimbursement $reimbursement, bool $bolehAturUangMuka): string
    {
        if ($bolehAturUangMuka) {
            return '';
        }

        if (!$reimbursement->masihBisaDiubah()) {
            return 'Data sudah diverifikasi Bagian Keuangan dan tidak dapat diubah.';
        }

        return 'Anda hanya dapat melihat data panjar SPPD ini.';
    }
}
