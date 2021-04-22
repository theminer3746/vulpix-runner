<?php

namespace App;

class CommandBuilder
{
    public function __construct(
        protected string $deviceIp,
        protected string $devicePort,
        protected string $appId,
        protected string $proxyIp,
        protected string $proxyPort
    ) {

    }

    public function getCommand()
    {
        return "python3 main.py $this->deviceIp:$this->devicePort $this->appId $this->proxyIp";
    }
}
