<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Runner;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'application_version',
        'assigned_at',
        'done_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'done_at' => 'datetime',
    ];

    public function runner()
    {
        return $this->belongsTo(Runner::class);
    }
}
