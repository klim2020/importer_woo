<?php

namespace Apps\TransportProvider;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use DOMDocument;

class TransportProviderWoocommerce implements ITransportProvider
{
    private $woocommerce;
    public function __construct($config)
    {
       ['address'=>$a,'key1'=>$b,'key2'=>$c,'options'=>$d] = $config;
        $this->woocommerce = new Client($a,$b,$c,$d);
    }

    public function getConnection(){
        return $this->woocommerce;
    }

}