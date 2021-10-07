<?php

namespace Apps\Exporters;

use Apps\TransportProvider\ITransportProvider;

class ExporterSandi implements IExporter
{

    private $provider;


    public function __construct(ITransportProvider $provider)
    {
        $this->provider = $provider;
    }
}