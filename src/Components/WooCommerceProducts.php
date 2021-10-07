<?php

namespace App\Components;

class WooCommerceProducts extends Products implements IProducts
{


    public function __construct($path)
    {
        $woocommerce =  $path;
        $this->data=array();
        $i=1;
        do {
            $prods = $woocommerce->get('products',['per_page'=>100,'page'=>$i]);
            $i++;
            $prods = json_decode(json_encode($prods),true);
            $this->data = array_merge($this->data,$prods);
            $this->skus = array_merge($this->skus,array_column($prods,'sku'));
            $this->ids = array_merge($this->ids,array_column($prods,'id'));
            foreach ($prods as $val){
                $this->hashes[] = $this->hash($val);
            }
        }while (count($prods)>0);

    }
}