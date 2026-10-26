<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SppdReimbursement extends Model
{
    protected $table = 'sppd_reimbursements';
    protected $guarded = ['id'];
    protected $casts = [
        'total_pengeluaran_riil' => 'decimal:2',
        'uang_muka'              => 'decimal:2',
        'selisih'                => 'decimal:2',
        'uang_muka_ditetapkan'  => 'boolean',
    ];

    public function sppd()
    {
        return $this->belongsTo(Sppd::class, 'sppd_id');
    }

    public function items()
    {
        return $this->hasMany(SppdReimbursementItem::class, 'reimbursement_id');
    }

    /**
     * Hitung ulang total pengeluaran riil & selisih.
     *
     * Total   = jumlah seluruh item nota.
     * Selisih = (total pengeluaran riil) - (uang muka / panjar yang dicairkan).
     *           Positif = perlu reimburse ke pegawai.
     *           Negatif = pegawai mengembalikan kelebihan panjar.
     *
     * Selisih hanya bermakna jika uang muka sudah ditetapkan dan sudah ada
     * pengeluaran riil, selain itu selisih disimpan 0 supaya tidak menampilkan
     * angka yang menyesatkan (mis. "Lebih Bayar" padahal belum ada nota).
     */
    public function hitungUlang()
    {
        $total = (float) $this->items()->sum('biaya');
        $uangMuka = $this->uang_muka_ditetapkan ? (float) $this->uang_muka : 0;
        $selisih = $total > 0 ? $total - $uangMuka : 0;

        if ((float) $this->total_pengeluaran_riil !== $total || (float) $this->selisih !== $selisih) {
            $this->forceFill([
                'total_pengeluaran_riil' => $total,
                'selisih'                => $selisih,
            ])->save();
        }

        return $this;
    }

    /**
     * Data sudah bisa diedit itemnya? Item dikunci setelah diverifikasi keuangan.
     */
    public function masihBisaDiubah(): bool
    {
        return $this->status_pembayaran === 'pending';
    }
}
