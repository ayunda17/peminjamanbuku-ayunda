<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nis',
        'address',
        'phone',
    ];

    /**
     * Relasi: Member memiliki banyak Loan
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
