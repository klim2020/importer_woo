<?php
namespace App\Readers;


use App\Components\IProducts;
use App\Components\JSONProducts;
use App\Components\Products;
use App\Readers\IStreamReader;


class  SandiStreamReader implements  IStreamReader
{
   private $products;

    public function __construct($data)
    {
        foreach ($data as $key=>$value){
            $this->products[$key] = new JSONProducts($value);
            //$this->prods[$key] = new JSONProducts("https://b2b-sandi.com.ua/export/view/36ce20096675493583a131edbb836408-8675-1629136354/json");
        }


    }


    public function getProductNum($key, string $sku)
    {
        return $this->products[$key]->getByID($sku);
    }

    public function getAllProducts($key)
    {
        return $this->products[$key]->getAllProducts();
    }
}
