<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Loan;

class PenanggungJawab extends Model
{
    use HasFactory;

    protected $table = 'penanggung_jawabs';

    protected $fillable = [
        'nama',
        'nip',
        'jabatan',
        'no_hp',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'penanggung_jawab_id');
    }
}
