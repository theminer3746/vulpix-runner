<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    private $error = [
        "UNKNOWN_ERROR" => 1,
        "DEVICE_OFFLINE" => 2,
        "DYNAMIC_TEST_ERROR" => 10,
        "TIMEOUT_ERROR" => 11,
        "PAID_APP_ERROR" => 12,
        "NOT_SUPPORTED_ERROR" => 13,
        "GAMES_CAT_ERROR" => 14,
        "APP_NOT_FOUND_ERROR" => 15,
        "ANALYZER_ERROR" => 20,
        "EXTERNAL_INTERFACE_ERROR" => 30,
        "BAD_INPUT_ERROR" => 40,
    ];

    private $emptyPII = [
        "advertiserId" => 0,
		"androidId" => 0,
		"deviceSerialNumber" => 0,
		"googleServicesId" => 0,
		"imei" => 0,
		"macAddress" => 0,
		"cellId" => 0,
		"simSerialNumber" => 0,
		"imsi" => 0,
		"localAreaCode" => 0,
		"phoneNumber" => 0,
		"age" => 0,
		"audioRecording" => 0,
		"calendar" => 0,
		"contactBook" => 0,
		"country" => 0,
		"ccv" => 0,
		"dob" => 0,
		"email" => 0,
		"gender" => 0,
		"name" => 0,
		"password" => 0,
		"photo" => 0,
		"physicalAddress" => 0,
		"relationshipStatus" => 0,
		"sms" => 0,
		"ssn" => 0,
		"timezone" => 0,
		"username" => 0,
		"video" => 0,
		"webBrowsingLog" => 0,
		"gps" => 0,
    ];

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
                ->setTimeout('600')
                ->getCommand();

            echo("Running : $dynamicCommand");

            exec("cd $baseDir && cd automated-gui-tester && $dynamicCommand", $dynamicResult, $dynamicResultCode);
            var_dump($dynamicResult);

            // Handle error
            if ($dynamicResultCode != 0)
            {
                // Tell manager there's an error
                $response = Http::post('https://vulpix-real-backend.theminerdev.com/api/results', [
                    'status' => 'error',
                    'appInfo' => [
                        'identifier' => $application_id,
                    ],
                    'result' => array_merge([
                        'version' => null,
                        'testingMethod' => 'DYNAMIC_ONLY',
                    ], $this->emptyPII),
                    'error' => array_search($dynamicResultCode, $this->error),
                ]);

                // Log::debug("Dynamic error reponse : " . $response->body());
            }

            // Free the runner
            $runner->status = 'available';
            $runner->save();

            $test->done_at = now();
            $test->save();
            
            $staticCommand = implode(" ", [
                'python3',
                'main.py',
                $application_id,
                '--endpoint', "https://vulpix-real-backend.theminerdev.com/api/results",
                '--timeout', '300',
            ]);
            exec("cd $baseDir && cd flowdroid-automated && $staticCommand", $staticResult, $staticResultCode);
            var_dump($staticResult);

            // Handle error
            if ($staticResultCode != 0)
            {
                // Tell manager there's an error
                $response = Http::post('https://vulpix-real-backend.theminerdev.com/api/results', [
                    'status' => 'error',
                    'appInfo' => [
                        'identifier' => $application_id,
                    ],
                    'result' => array_merge([
                        'version' => null,
                        'testingMethod' => 'STATIC_ONLY',
                    ], $this->emptyPII),
                    'error' => 'UNKNOWN_ERROR',
                ]);

                // Log::debug("Dynamic error reponse : " . $response->body());
            }
        }
        else 
        {
            throw new \Exception();
        }

        return 0;
    }
}
