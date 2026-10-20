<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = User::getUser();

        // Query pengajuan approval
        $query = Sppd::with(['user', 'tempat', 'kabid'])->orderBy('id', 'desc');

        // Role filtering
        if ($user->role == 'Kepala Bidang') {
            // Atasan melihat pengajuan miliknya atau yang menunggu atasan
            $query->whereIn('status', ['pending_atasan', 'pending_keuangan', 'approved', 'rejected']);
        } elseif ($user->role == 'Keuangan') {
            // Keuangan melihat yang sudah disetujui atasan
            $query->whereIn('status', ['pending_keuangan', 'approved', 'completed', 'rejected']);
        } elseif ($user->role == 'Admin') {
            // Admin melihat semua
        } else {
            // Pegawai biasa hanya melihat miliknya jika terpaksa akses
            $query->whereHas('user', fn($q) => $q->where('users.id', $user->id));
        }

        if ($request->tab == 'pending') {
            if ($user->role == 'Kepala Bidang') {
                $query->where('status', 'pending_atasan');
            } elseif ($user->role == 'Keuangan') {
                $query->where('status', 'pending_keuangan');
            } else {
                $query->whereIn('status', ['pending_atasan', 'pending_keuangan']);
            }
        } elseif ($request->tab == 'approved') {
            $query->whereIn('status', ['approved', 'completed']);
        } elseif ($request->tab == 'rejected') {
            $query->where('status', 'rejected');
        }

        $sppdList = $query->paginate(10)->withQueryString();

        return view('sppd.approval.index', compact('user', 'sppdList'));
    }

    public function approveAtasan($id)
    {
        $user = User::getUser();
        if (!in_array($user->role, ['Kepala Bidang', 'Admin'])) {
            session()->flash('Failed', 'Anda tidak memiliki hak akses untuk persetujuan Atasan.');
            return back();
        }

        $sppd = Sppd::findOrFail($id);
        $sppd->update([
            'status' => 'pending_keuangan',
            'approved_by_atasan_at' => now(),
            'catatan_revisi' => null
        ]);

        session()->flash('Success', 'Pengajuan SPPD disetujui oleh Atasan dan diteruskan ke Bagian Keuangan.');
        return back();
    }

    public function approveKeuangan($id)
    {
        $user = User::getUser();
        if (!in_array($user->role, ['Keuangan', 'Admin'])) {
            session()->flash('Failed', 'Anda tidak memiliki hak akses untuk persetujuan Keuangan.');
            return back();
        }

        $sppd = Sppd::findOrFail($id);
        $sppd->update([
            'status' => 'approved',
            'approved_by_keuangan_at' => now(),
            'catatan_revisi' => null
        ]);

        session()->flash('Success', 'Pengajuan SPPD disetujui oleh Keuangan. SPT dan SPPD resmi siap dicetak.');
        return back();
    }

    public function reject(Request $request, $id)
    {
        $this->validate($request, [
            'catatan_revisi' => 'required|string|max:500'
        ]);

        $sppd = Sppd::findOrFail($id);
        $sppd->update([
            'status' => 'rejected',
            'catatan_revisi' => $request->catatan_revisi
        ]);

        session()->flash('Success', 'Pengajuan SPPD telah ditolak / diminta revisi.');
        return back();
    }
}
