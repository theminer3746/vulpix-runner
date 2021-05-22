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
        $runner = DB::table('runners');
        
        if (!$this->option('force'))
        {
            // TODO : FIX BUG
            $runner = $runnerwhere(function($q){
                $q->select('tests.assigned_at')
                    ->from('tests')
                    ->whereColumn('runners.id', 'tests.runner_id')
                    ->whereNull('tests.done_at')
                    ->orderByDesc('tests.assigned_at')
                    ->limit(1);
            }, '<', now()->subminute(35));
        }

        $runner->update([
            'status' => 'available',
        ]);

        return 0;
    }
}
