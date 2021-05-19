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
        $runner = (new \App\Models\Runner)->where('status', 'available')->first();
        if (is_null($runner))
        {
            // No runner available
            echo("No runner available\n");

            return 0;
        }

        // Get a job
        $endpoint = env('VULPIX_REAL_BACKEND_DOMAIN', 'https://vulpix-real-backend.theminerdev.com');
        $response = Http::withToken(config('runner.id'))
        ->get("$endpoint/api/tests", [
            'status' => 'available',
            'limit' => 1,
            'mark_as_running' => true,
        ]);

        if(!$response->successful())
        {
            echo("Application queue is empty\n");

            return 0;
        }

        if (count($response->json()) == 1)
        {
            $baseDir = getcwd();
            $application_id = $response->json()[0]["application_id"];

            $test = (new \App\Models\Test);
            $test->application_id = $application_id;
            $test->assigned_at = now();

            // Assign a runner
            $runner->status = 'running';
            $runner->tests()->save($test);
            $runner->save();

            // Execute the command
            // $command = "python3 main.py 192.168.56.106:5555 " . $application_id . " 10.0.112.2 --proxy_port 8090 --system_port 12000 --appium_port 3000 --endpoint https://vulpix-real-backend.theminerdev.com/api/results";
            $dynamicCommand = (new \App\CommandBuilder($runner->device_ip, $runner->device_port, $application_id, $runner->proxy_ip))
                ->setProxyPort($runner->proxy_port)
                ->setSystemPort($runner->system_port)
                ->setAppiumPort($runner->appium_port)
                ->setEndPoint("https://vulpix-real-backend.theminerdev.com/api/results")
                ->getCommand();

            echo("Running : $dynamicCommand");

            exec("cd $baseDir && cd automated-gui-tester && $dynamicCommand", $dynamicResult, $dynamicResultCode);
            var_dump($dynamicResult);

            // Handle error
            if ($dynamicResultCode !== 0)
            {
                // Tell manager there's an error
            }

            // Free the runner
            $runner->status = 'available';
            $runner->save();

            $staticCommand = implode(" ", [
                'python3',
                'main.py',
                $application_id,
            ]);
            exec("cd $baseDir && cd flowdroid-automated && $staticCommand", $staticResult, $staticResultCode);
            var_dump($staticResult);
        }
        else 
        {
            throw new \Exception();
        }

        return 0;
    }
}
