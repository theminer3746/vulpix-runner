<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetAJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vulpix:getJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a job to run if there is a runner available';

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
        // Check if a runner is available

        // Assign the runner

        // Execute the command
        $result = shell_exec("python3 test_script.py");
        echo($result);

        // Interpret the result

        // Free the runner

        return 0;
    }
}
