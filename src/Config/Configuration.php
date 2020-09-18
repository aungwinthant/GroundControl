<?php

namespace GC\Config;

use InvalidArgumentException;

final class Configuration{

    protected $apikey;

    protected $apisecret;

    protected $url = "localhost:8000/gc/v1/exceptions";

    public function __construct($apikey, $apisecret)
    {
        $this->apikey = $apikey;

        $this->apisecret = $apisecret;
    }

    public function setUrl($url){
        if(empty($url)){
            throw new InvalidArgumentException("Invalid URL");
        }
        $this->url = trim($url);
    }

    public function getApiKey(){
        return $this->apikey;
    }

    public function getApiSecret(){
        return $this->apisecret;
    }

    public function getUrl(){
        return $this->url;
    }
}
