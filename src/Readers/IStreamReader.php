<?php

namespace App\Readers;

/**
 *
 */
interface IStreamReader{


    /**
     * @param string $sku идентификатор продукта
     * @return mixed продукт
     */
    public function getProductNum($key, string $sku);



    /**
     * @return mixed
     */
    public function  getAllProducts($key);



}
