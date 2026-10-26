<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Sppd;
use App\Models\SppdReimbursement;
use App\Models\SppdReimbursementItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReimbursementTest extends TestCase
{
    use DatabaseTransactions;

    protected Sppd $sppd;

    protected User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        // Data uji tersendiri supaya tidak bergantung pada data SPPD yang sudah ada.
        $this->pegawai = User::where('role', 'Staff')->first() ?? User::firstOrFail();

        $this->sppd = Sppd::create([
            'no_surat'     => 999,
            'tahun_surat'  => '2026',
            'tgl_pergi'    => now(),
            'tgl_kembali'  => now()->addDay(),
            'acara'        => 'Uji coba reimbursement',
            'status'       => 'approved',
            'uang_muka'    => 1500000,
        ]);

        $this->pegawai->sppd()->attach($this->sppd->id);
    }

    public function test_halaman_reimbursement_tidak_menampilkan_nilai_tak_entah_dan_selisih_palsu()
    {
        $response = $this->actingAs($this->pegawai)->get(route('Reimbursement', $this->sppd->id));

        $response->assertStatus(200);
        $response->assertSee('Belum ditetapkan');
        $response->assertSee('Menunggu data uang muka');
        $response->assertSee('Simpan Panjar');

        $reimbursement = SppdReimbursement::where('sppd_id', $this->sppd->id)->first();
        $this->assertNotNull($reimbursement);
        $this->assertFalse((bool) $reimbursement->uang_muka_ditetapkan);
        $this->assertEquals(0, (float) $reimbursement->uang_muka);
        $this->assertEquals(0, (float) $reimbursement->selisih);
    }

    public function test_uang_muka_bisa_diinput_dan_menghitung_selisih()
    {
        $this->actingAs($this->pegawai)
            ->post(route('Reimbursement.setUangMuka', $this->sppd->id), ['uang_muka' => 500000])
            ->assertStatus(302);

        $reimbursement = SppdReimbursement::where('sppd_id', $this->sppd->id)->firstOrFail();
        $this->assertTrue((bool) $reimbursement->uang_muka_ditetapkan);
        $this->assertEquals(500000, (float) $reimbursement->uang_muka);
        $this->assertEquals(0, (float) $reimbursement->selisih, 'Belum ada nota, selisih harus 0');

        // Tambah nota pengeluaran riil
        $this->actingAs($this->pegawai)
            ->post(route('Reimbursement.addItem', $this->sppd->id), [
                'kategori'   => 'Transportasi',
                'keterangan' => 'Tiket Kereta Api',
                'biaya'      => 750000,
            ])
            ->assertStatus(302);

        $reimbursement->refresh();
        $this->assertEquals(750000, (float) $reimbursement->total_pengeluaran_riil);
        $this->assertEquals(250000, (float) $reimbursement->selisih, 'Selisih = total riil - uang muka');

        // Hapus nota
        $item = SppdReimbursementItem::where('reimbursement_id', $reimbursement->id)->firstOrFail();
        $this->actingAs($this->pegawai)
            ->delete(route('Reimbursement.deleteItem', $item->id))
            ->assertStatus(302);

        $reimbursement->refresh();
        $this->assertEquals(0, (float) $reimbursement->total_pengeluaran_riil);
        $this->assertEquals(0, (float) $reimbursement->selisih);
    }

    public function test_pegawai_lain_tidak_bisa_menambah_item()
    {
        $other = User::where('id', '!=', $this->pegawai->id)->where('role', '!=', 'Admin')->first()
            ?? User::where('id', '!=', $this->pegawai->id)->firstOrFail();

        $this->actingAs($other)
            ->post(route('Reimbursement.addItem', $this->sppd->id), [
                'kategori'   => 'Transportasi',
                'keterangan' => 'Percobaan',
                'biaya'      => 1000,
            ])
            ->assertStatus(302);

        $reimbursement = SppdReimbursement::where('sppd_id', $this->sppd->id)->firstOrFail();
        $this->assertEquals(0, (float) $reimbursement->total_pengeluaran_riil);
        $this->assertEquals(0, SppdReimbursementItem::where('reimbursement_id', $reimbursement->id)->count());
    }

    public function test_nota_terkunci_setelah_diverifikasi_keuangan()
    {
        $finance = User::where('role', 'Admin')->firstOrFail();

        $this->actingAs($finance)
            ->post(route('Reimbursement.verify', $this->sppd->id), [
                'status_pembayaran' => 'verified',
                'catatan_keuangan'  => 'Nota sudah dicocokkan',
            ])
            ->assertStatus(302);

        $reimbursement = SppdReimbursement::where('sppd_id', $this->sppd->id)->firstOrFail();
        $this->assertEquals('verified', $reimbursement->status_pembayaran);
        $this->assertEquals('Nota sudah dicocokkan', $reimbursement->catatan_keuangan);

        // Setelah terverifikasi, pegawai tidak boleh menambah item
        $this->actingAs($this->pegawai)
            ->post(route('Reimbursement.addItem', $this->sppd->id), [
                'kategori'   => 'Transportasi',
                'keterangan' => 'Percobaan',
                'biaya'      => 1000,
            ])
            ->assertStatus(302);

        $reimbursement->refresh();
        $this->assertEquals(0, (float) $reimbursement->total_pengeluaran_riil);
    }

    public function test_sppd_belum_disetujui_tidak_bisa_melapor()
    {
        $this->sppd->update(['status' => 'pending_atasan']);

        $this->actingAs($this->pegawai)
            ->get(route('Reimbursement', $this->sppd->id))
            ->assertRedirect(route('SPPD'));

        $this->assertNull(SppdReimbursement::where('sppd_id', $this->sppd->id)->first());
    }
}
