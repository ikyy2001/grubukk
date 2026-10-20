<?php

namespace App\Models\Auth;

use App\Models\Eselon;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\Sppd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    protected $fillable = ['nama', 'nip', 'alamat', 'password', 'tanggal_lahir', 'golongan','jabatan','created_at','updated_at','role', 'cek'];
    public function jabatan()
    {
        return $this->hasOne(Jabatan::class);
    }
    public function golongan()
    {
        return $this->hasOne(Golongan::class);
    }
    public function eselon()
    {
        return $this->hasOne(Eselon::class);
    }
    public function sppd()
    {
        return $this->belongsToMany(Sppd::class,'sppd_users','users_id','sppd_id');
    }

    public static function enum_get($table, $column)
    {
        $columns = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = ?", [$column]);
        if (empty($columns)) {
            return collect([]);
        }
        $type = $columns[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = [];
        $no = 1;
        if (!empty($matches[1])) {
            foreach (explode(',', $matches[1]) as $value) {
                $v = trim($value, "'");
                $enum[$no] = $v;
                $no++;
            }
        }
        return collect($enum);
    }
    public static function role_check($role)
    {
        $roles = collect($role);
        foreach ($roles as $role) {
            if (Auth::check() && $role == Auth::user()->role) {
                return True;
            }
        }
    }
    // Fungsi Diluar 
    public static function getUser(){
        return Auth::check() ? User::where('id', Auth::user()->id)->first() : null;
    }
    public static function destroy($ids)
    {
        if ($ids instanceof \Illuminate\Database\Eloquent\Model) {
            return $ids->delete();
        }
        return parent::destroy($ids);
    }
}
