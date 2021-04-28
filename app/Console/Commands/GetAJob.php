<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

        // Get a job
        $response = Http::withToken('runner1')
        ->get('http://10.2.101.62:8090/api/tests', [
            'status' => 'available',
            'limit' => 1,
        ]);

        if(!$response->successful())
        {
            return 1;
        }

        // Assign a runner

        // Execute the command
        $command = "python3 main.py 192.168.56.106:5555 " . $response->json()[0]["application_id"] . " 10.0.112.2 --proxy_port 8090 --system_port 12000 --appium_port 3000 --endpoint https://vulpix-real-backend.theminerdev.com/api/results";
        $result = shell_exec("cd automated-gui-tester && $command");
        var_dump($result);

        // Interpret the result

        // Free the runner

        return 0;
    }
}
