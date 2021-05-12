<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Test;

class Runner extends Model
{
    use HasFactory;

    protected $fillable = [
        'proxy_port',
        'adb_port',
        'appium_port',
        'system_port',
        'android_version',
        'status',
    ];

    protected $casts = [];

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function getAppiumPortsUnderUse()
    {
        return $this->where('status', '!=', 'exited')->select('appium_port')->get();
    }

    public function isRunnerAvailable()
    {
        return $this->where('status', 'available')->exists();
    }
    
    public function canCreateMoreRunner()
    {
        return $this->runner->where('status', '!=', 'exited')->count() < config('runner.max_runner');
    }
}
