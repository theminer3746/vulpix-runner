<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ResetRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runner:reset {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all runners to available';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $runner = new \App\Models\Runner;
        
        if (!$this->option('force'))
        {
            $runner = $runner->whereHas('tests', function (Builder $q) {
                $q->where('done_at', null)
                    ->where('assigned_at', '<', now()->subminute(35));
            });
        }

        $runner->update([
            'status' => 'available',
        ]);

        return 0;
    }
}
