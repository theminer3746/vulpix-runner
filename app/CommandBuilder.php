<?php

namespace App;

class CommandBuilder
{
    protected string $proxyPort;
    protected string $systemPort;
    protected string $appiumPort;
    protected string $endPoint;
    protected string $timeout;
    protected string $uuid;

    public function __construct(
        protected string $deviceIp,
        protected string $devicePort,
        protected string $appId,
        protected string $proxyIp
    ) {
    }

    public function setProxyPort(string $proxyPort)
    {
        $this->proxyPort = $proxyPort;

        return $this;
    }

    public function setSystemPort(string $systemPort)
    {
        $this->systemPort = $systemPort;

        return $this;
    }

    public function setAppiumPort(string $appiumPort)
    {
        $this->appiumPort = $appiumPort;

        return $this;
    }

    public function setEndPoint(string $endPoint)
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    public function setTimeout(string $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCommand()
    {
        $command = [
            'python3',
            'main.py',
            "$this->deviceIp:$this->devicePort",
            $this->appId,
            $this->proxyIp,
        ];
        // $command = "python3 main.py $this->deviceIp:$this->devicePort $this->appId $this->proxyIp";

        if (isset($this->proxyPort)) {
            $command[] = "--proxy_port $this->proxyPort";
        }

        if (isset($this->systemPort)) {
            $command[] = "--system_port $this->systemPort";
        }

        if (isset($this->appiumPort)) {
            $command[] = "--appium_port $this->appiumPort";
        }

        if (isset($this->endPoint)) {
            $command[] = "--endpoint $this->endPoint";
        }

        if (isset($this->timeout)) {
            $command[] = "--timeout $this->timeout";
        }

        if (isset($this->uuid)) {
            $command[] = "--uuid $this->uuid";
        }

        return implode(" ", $command);
    }
}
