<?php

namespace Bludata\Lumen\Traits;

trait LogTrait
{
    public function emergency($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(\Psr\Log\LogLevel::DEBUG, $message, $context);
    }

    public function toLogMessage($value)
    {
        if (!is_string($value)) {
            return print_r($value, true);
        }

        return $value;
    }

    public function log($level, $message, array $context = [])
    {
        $logger = app(\Psr\Log\LoggerInterface::class);

        $log = $this->toLogMessage($message);

        $logger->log($level, $log, $context);
    }
}
