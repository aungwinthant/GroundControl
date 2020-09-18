<?php

namespace GC\Models;

use ErrorException;
use GC\Config\Configuration;
use InvalidArgumentException;
use Throwable;

class Exception {

    protected $exception;

    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setException($exception){
        if(!$exception instanceof Throwable && !$exception instanceof Exception ){
            throw new InvalidArgumentException("Exception must be type of Throwable or Exception");
        }
        $this->exception = $exception;
    }

    public function getException(){
        return $this->exception;
    }

    public function send(){
        if(!$this->exception){
            $error = error_get_last();
            $error['type'] = $this->formatErrorType($error['type']);
        }
        else{
            $error = $this->formatExceptionAsArray($this->exception);
        }
        $ch = curl_init($this->configuration->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-app-id:' . $this->configuration->getApiKey(),
            'x-app-secret:' . $this->configuration->getApiSecret()
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $error);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $errno    = curl_errno($ch);
        if (is_resource($ch)) {
            curl_close($ch);
        }
        if (0 !== $errno) {
            throw new \RuntimeException($error, $errno);
        }
        return $response;
    }

    public function formatExceptionAsArray($exception)
    {
        $response = [
            'type'          => get_class($exception),
            'message'       => $exception->getMessage(),
            'file'          => $exception->getFile(),
            'line'          => $exception->getLine(),
            'errorcode'     => $exception->getCode(),
            'trace'         => json_encode($exception->getTrace()),
            'tracestr'      => $exception->getTraceAsString(),
        ];
        if($exception instanceof ErrorException){
            $response['severity'] = $exception->getSeverity();
        }
        return $response;
    }

    function formatErrorType($type)
    {
        switch($type)
        {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }

}
