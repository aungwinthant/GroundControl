<?php

namespace GC;

use GC\Models\Exception;
use GC\Config\Configuration;
use InvalidArgumentException;
use Throwable;

class GroundControl{

    protected $config; 

    protected $exception;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->exception = new Exception($this->config);
        register_shutdown_function([$this, 'send']);
    }
    public function recordException(Throwable $exception){
        if(!$exception instanceof Throwable && !$exception instanceof Exception ){
            throw new InvalidArgumentException("Exception must be type of Throwable or Exception");
        }
        $this->exception->setException($exception);
    }

    public function send(){
        $this->exception->send();
        unset($this->exception);
    }
}
