<?php

namespace Apps\TransportProvider;

use Apps\TransportProvider\ITransportProvider;

class TransportProviderSandi implements ITransportProvider
{

    private $rawdata;

    /**
     * @param mixed $config
     */
    public function __construct($config)
    {
        $this->rawdata =  json_decode(file_get_contents($config['url']), true);
    }

    public function getConnection()
    {
        return $this->rawdata;
    }
}