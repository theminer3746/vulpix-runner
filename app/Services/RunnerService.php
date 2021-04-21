<?php

use App\Models\Runner;
use App\Models\Setting;

class RunnerService
{
    public function __construct(
        private Runner $runner,
        private Setting $setting
    )
    {}

    public function updateRunnerTable()
    {
        $command = "devices list";
        $result = $this->executeGenyMotionShell($command);
    }
    
    private function executeGenyMotionShell(string $command)
    {
        $genyMotionPath = env('GENYMOTION_PATH', '$HOME/genymotion');
        $shellOutput = shell_exec("cd $genyMotionPath && ./genyshell -q -c \"$command\"");
    }

    public function getAvailableRunner()
    {
        if ($runner->isRunnerAvailable())
        {
            return $this->runner
                ->where('status', 'available')
                ->first();
        }

        if ($runner->canCreateMoreRunner())
        {
            $this->createRunner();

            return; // TODO : return created runner
        }
    
        throw new NoRunnerException();
    }

    public function createRunner(bool $forced = false)
    {
        shell_exec();
    }
}
