<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SppdReimbursementItem extends Model
{
    protected $table = 'sppd_reimbursement_items';
    protected $guarded = ['id'];

    public function reimbursement()
    {
        return $this->belongsTo(SppdReimbursement::class, 'reimbursement_id');
    }
}
