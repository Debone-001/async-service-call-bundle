<?php

namespace Krlove\AsyncServiceCallBundle;

/**
 * Class AsyncService
 * @package Krlove\AsyncServiceCallBundle
 */
class AsyncService
{
    /**
     * @var string
     */
    protected $consolePath;

    /**
     * @var string
     */
    protected $phpPath;

    /**
     * AsyncService constructor.
     * @param string $consolePath
     * @param string $phpPath
     */
    public function __construct(
        string $consolePath,
        string $phpPath
    ) {
        $this->consolePath = $consolePath;
        $this->phpPath = $phpPath;
    }

    /**
     * Method that constructs the command with the service to be called.
     *
     * @param string $service
     * @param string $method
     * @param array  $arguments
     * @return int
     */
    public function call(
        string $service,
        string $method,
        array  $arguments = []
    ) : int {
        $commandline = $this->createCommandString(
            $service,
            $method,
            $arguments
        );

        return $this->runProcess($commandline);
    }

    /**
     * Creates the command string to be executed in background.
     *
     * @param string $service
     * @param string $method
     * @param array $arguments
     * @return string
     */
    protected function createCommandString(
        string $service,
        string $method,
        array  $arguments
    ) : string {
        $arguments = escapeshellarg(base64_encode(serialize($arguments)));

        return sprintf(
            '%s %s krlove:service:call %s %s --args=%s > /dev/null 2>/dev/null & echo $!',
            $this->phpPath,
            $this->consolePath,
            $service,
            $method,
            $arguments
        );
    }

    /**
     * Executes the command that calls the service.
     * It will return the PID.
     *
     * @param string $commandline
     * @return int
     */
    protected function runProcess(string $commandline) : int
    {
        exec($commandline, $op);

        if (!\is_array($op)) {
            return 0;
        }

        return (int) $op[0];
    }
}
