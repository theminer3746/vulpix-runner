<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runner:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a runner';

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
        $runner = (new \App\Models\Runner);
        $runner->device_ip = $this->ask("Device IP?");
        $runner->device_port = $this->ask("Device port? (Default 5555)") ?? 5555;
        $runner->proxy_ip = $this->ask("Proxy IP?");
        $runner->proxy_port = $this->ask("Proxy port?");
        $runner->system_port = $this->ask("System port? (Default 12000)") ?? 12000;
        $runner->appium_port = $this->ask("Appium port? (Default 4723)") ?? 4723;
        $runner->android_version = $this->ask("Android version? (Default 9.0)") ?? "9.0";
        $runner->status = 'available';

        if ($this->confirm('Save to database?')) {
            $runner->save();
        }

        return 0;
    }
}
