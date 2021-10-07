<?php

namespace Apps\Exporters;

use Apps\TransportProvider\ITransportProvider;

interface IExporter
{

    /**
     *
     * Создаем Импортера товаров
     * получаем список товаров, аттрибутов, опций с сервера
     * @param ITransportProvider $provider
     */
    public function __construct(ITransportProvider $provider);


}