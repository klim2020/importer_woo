<?php
namespace  App\Importer;

use App\Connectors;
use Readers\IStreamReader;

class WooImporter2 {
    private IConnector $source;
    private IStreamReader $reader;
    public function __construct()
    {
        //подключаемся  к источнику данных и создаем список подключений
        $this->source = new WooConnector();
        $this->source->getAllNormalizedProducts();
        $this->source->getProduct('ddd');
        $i=1;

    }

}