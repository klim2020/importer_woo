<?php

namespace App\Components;

class Products
{

    protected $data = array();//массив с переработанными данными
    protected $ids = array(); //индексированый массив с ид
    protected $skus = array();//индексированый массив с СКУ
    protected $hashes = array();//индексированый массив с хешсуммами

    protected function hash($data){
        return hash('md5',json_encode($data));
    }

    /**возвращает порядковый номер в массиве  по id
     * @inheritDoc
     */
    public function getByID($id)
    {

        return array_search($id,$this->ids);
    }

    /**
     * @inheritDoc
     */
    public function getBySKU($sku)
    {
        return array_search($sku,$this->skus);
    }

    /**
     * @inheritDoc
     */
    public function getFieldByID($id)
    {
        $idx = array_search($id,$this->ids);
        return $this->data[$idx];
    }

    /**
     * @inheritDoc
     */
    public function update($data)
    {
        $data = json_decode(json_encode($data),true);
        if ($this->checkProductHash($data['id'],$data)!==false){
            $this->data[$this->getByID($data['id'])] = $data;
        }else{
            $this->data[] = $data;
            $this->skus[] = $data['sku'];
            $this->hashes[]=$this->hash($data);
            $this->ids[]=$data['id'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($id):IAttributes
    {
       $idx = array_search($id,$this->ids);
       return new Attributes($this->data[$idx]['attributes']);
    }

    /** проверяет на соответствие продукта и его хеш суммы
     * если хеш сумма продукта с номером айди соответствует массиву дата
     * то возвращает тру
     * если не то фалс
     * @inheritDoc
     */
    public function checkProductHash($id, $data)
    {

        $idx = array_search($id,$this->ids);
        if ($idx === false){
            return false;
        }
        if (strcmp($this->hash($data),$this->hashes[$idx])===0){
            return true;
        }
        return false;
    }

    public function getAllProducts()
    {
        return $this->data;
    }

    protected function removespacesandmakelowercase($string){
        $re = '/[^A-zёА-я0-9]/iu';
        $ret = preg_replace($re, "", $string);
        $ret = mb_strtolower($ret);
        return $ret;
    }

}