<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SppdReimbursement extends Model
{
    protected $table = 'sppd_reimbursements';
    protected $guarded = ['id'];

    public function sppd()
    {
        return $this->belongsTo(Sppd::class, 'sppd_id');
    }

    public function items()
    {
        return $this->hasMany(SppdReimbursementItem::class, 'reimbursement_id');
    }
}
