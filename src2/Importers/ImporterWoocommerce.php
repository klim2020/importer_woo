<?php

namespace Apps\Importers;

use Apps\Components\IAttribute;
use Apps\Components\IAttributeOption;
use Apps\Components\IProduct;
use Apps\TransportProvider\ITransportProvider;

class ImporterWoocommerce implements IImporter
{
    private $provider;
    private $productlist;
    /**
     * @param TransportProviderWoocommerce $param
     */
    public function __construct(ITransportProvider $provider)
    {
        $this->provider = $provider;
        $this->provider->getConnection();



    }

    public function addProduct(IProduct $product)
    {
        // TODO: Implement addProduct() method.
    }

    public function addAttribute(IProduct $product, IAttribute $attribute)
    {
        // TODO: Implement addAttribute() method.
    }

    public function addOption(IProduct $product, IAttribute $attribute, IAttributeOption $option)
    {
        // TODO: Implement addOption() method.
    }

    public function addGlobalAttribute(IAttribute $attr)
    {
        // TODO: Implement addGlobalAttribute() method.
    }


    public function addGlobalOption(IAttribute $attr, IAttributeOption $option)
    {
        // TODO: Implement addGlobalOption() method.
    }

    public function getAllGlobalAttributes()
    {
        // TODO: Implement getAllGlobalAttributes() method.
    }
}