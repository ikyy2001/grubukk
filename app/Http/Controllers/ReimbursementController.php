<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\SppdReimbursement;
use App\Models\SppdReimbursementItem;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $user = User::getUser();
        $sppd = Sppd::with(['user', 'tempat', 'reimbursement.items'])->findOrFail($id);

        // Pastikan pengguna berhak (Admin, Keuangan, atau pemilik SPPD)
        $isOwner = $sppd->user->contains('id', $user->id);
        $canAccess = $isOwner || in_array($user->role, ['Admin', 'Keuangan', 'Kepala Bidang']);

        if (!$canAccess) {
            return view('sppd.users.akses', compact('user'));
        }

        // Ambil atau inisialisasi reimbursement
        $reimbursement = $sppd->reimbursement;
        if (!$reimbursement) {
            $reimbursement = SppdReimbursement::create([
                'sppd_id'                => $sppd->id,
                'total_pengeluaran_riil' => 0,
                'uang_muka'              => $sppd->uang_muka ?? 0,
                'selisih'                => 0 - ($sppd->uang_muka ?? 0),
                'status_pembayaran'      => 'pending'
            ]);
            $reimbursement->load('items');
        }

        return view('sppd.reimbursement.index', compact('user', 'sppd', 'reimbursement'));
    }

    public function addItem(Request $request, $id)
    {
        $this->validate($request, [
            'kategori'   => 'required|string',
            'keterangan' => 'required|string|max:255',
            'biaya'      => 'required|numeric|min:0',
            'file_nota'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $sppd = Sppd::findOrFail($id);
        $reimbursement = SppdReimbursement::firstOrCreate(
            ['sppd_id' => $sppd->id],
            [
                'total_pengeluaran_riil' => 0,
                'uang_muka'              => $sppd->uang_muka ?? 0,
                'selisih'                => 0 - ($sppd->uang_muka ?? 0),
                'status_pembayaran'      => 'pending'
            ]
        );

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
        $totalRiil = $reimbursement->items()->sum('biaya');
        $selisih = $totalRiil - $reimbursement->uang_muka;

        $reimbursement->update([
            'total_pengeluaran_riil' => $totalRiil,
            'selisih'                => $selisih
        ]);

        session()->flash('Success', 'Item pengeluaran riil berhasil ditambahkan.');
        return back();
    }

    public function deleteItem($itemId)
    {
        $item = SppdReimbursementItem::findOrFail($itemId);
        $reimbursement = $item->reimbursement;
        $item->delete();

        // Recalculate totals
        $totalRiil = $reimbursement->items()->sum('biaya');
        $selisih = $totalRiil - $reimbursement->uang_muka;

        $reimbursement->update([
            'total_pengeluaran_riil' => $totalRiil,
            'selisih'                => $selisih
        ]);

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

        $sppd = Sppd::findOrFail($id);
        $reimbursement = SppdReimbursement::where('sppd_id', $sppd->id)->firstOrFail();

        $reimbursement->update([
            'status_pembayaran' => $request->status_pembayaran ?? 'verified',
            'catatan_keuangan'  => $request->catatan_keuangan
        ]);

        if ($request->status_pembayaran == 'paid') {
            $sppd->update(['status' => 'completed']);
        }

        session()->flash('Success', 'Status pertanggungjawaban reimbursement berhasil diperbarui.');
        return back();
    }
}
