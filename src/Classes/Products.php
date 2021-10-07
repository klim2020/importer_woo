<?php

namespace App\Classes;

class Products implements IProducts
{
    private $records = array();
    private $skus = array();
    private $ids  = array();


    /**
     * @inheritDoc
     */
    public function __construct($path)
    {
        foreach ($path as $key=>$val){
            $this->records[] = new Record($val);
            $this->skus[] = $val['sku'];
            $this->ids[]=$val['id'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getByID($id)
    {
        $idx = array_search($id,$this->ids);
        if ($idx===false){
            return false;
        }
        return  $this->records[$idx];

    }

    /**
     * @inheritDoc
     */
    public function getBySKU($sku)
    {

    }

    /**
     * @inheritDoc
     */
    public function getRecordByID($id)
    {
        // TODO: Implement getRecordByID() method.
    }

    /**
     * @inheritDoc
     */
    public function update($data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($id): IAttributes
    {
        // TODO: Implement getAttributes() method.
    }

    /**
     * @inheritDoc
     */
    public function checkProductHash($id, $data)
    {
        // TODO: Implement checkProductHash() method.
    }

    /**
     * @inheritDoc
     */
    public function getAllProducts()
    {
        // TODO: Implement getAllProducts() method.
    }
}