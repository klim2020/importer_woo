<?php

namespace App\Components;

use mysql_xdevapi\Exception;

class Attributes implements IAttributes
{
    protected $data = array();//массив с переработанными данными
    protected $ids = array(); //индексированый массив с ид
    protected $names = array();//индексированый массив с СКУ
    protected $hashes = array();//индексированый массив с хешсуммами


    public function __construct($arr)
    {
        $this->data = json_decode(json_encode($arr),true);
        foreach ($this->data as $key=>$val){
            $this->ids[$key] = $val['id'] ?? 0;
            $this->names[$key] =$this->removespacesandmakelowercase($val['name']);
            $this->hashes[$key] = $this->hash($val);

        }
    }


    protected function hash($data){
        return hash('md5',json_encode($data));
    }


    /**
     * @inheritDoc
     */
    public function getByID($id)
    {
        $idx = array_search($id,$this->ids);
        if ($idx === false){return false;}
        return $this->data[$idx];
    }

    public function getNumByID($id){
        $idx = array_search($id,$this->ids);
        return $idx;
    }

    /**
     * @inheritDoc
     */
    public function getByName($sku)
    {
        $val = $this->removespacesandmakelowercase($sku);
        $idx = array_search($val, $this->names);
        return $idx === false ? false : $this->data[$idx];
    }

    /**
     * @inheritDoc
     */
    public function getFieldByID($id, $key)
    {
        $idx = array_search($id,$this->ids);
        return $this->data[$idx][$key];
    }

    /** Обновляем продукт, если данный продукт существует, обновляем запись, если его нет,  добавляем в массив! но не обновляемся
     * @inheritDoc
     */
    public function update($data)
    {
        $data = json_decode(json_encode($data),true);
        $idx = $this->getNumByID($data['id']);
        if ($idx ===false){ $this->create($data);return;}
        if (count($this->data)==0){ $this->create($data);return;}
        if (!$this->checkProductHash($data['id'],$data)){//
            $this->data[$idx] = $data;
        }





    }

    public function checkProductHash($id, $data)
    {

        $idx = array_search($id,$this->ids);
        if ($idx===false){return false;}
        if (strcmp($this->hash($data),$this->hashes[$idx])==0){
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($id)
    {
        $idx = array_search($id,$this->ids);
        return new Attributes($this->data[$idx]['attributes']);
    }

    /**
     * @inheritDoc
     */
    public function checkAttribute($id, $data)
    {

        $idx = array_search($id,$this->ids);
        if (strcmp($this->hash($data),$this->hashes[$idx])==0){
            return true;
        }
        return false;
    }

    public function getAllAttributes()
    {
        return $this->data;
    }

    private function create($data){
        $this->data[] = $data;
        $this->ids[] = $data['id'];
        $this->names[] = $this->removespacesandmakelowercase($data['name']);
        $this->hashes[] = 0;
    }

    private function removespacesandmakelowercase($string){
        $re = '/[^A-zёА-я0-9]/iu';
        $ret = preg_replace($re, "", $string);
        $ret = mb_strtolower($ret);
        return $ret;
    }

    public function getNumBySKU($sku)
    {
        $idx = array_search($this->removespacesandmakelowercase($sku),$this->names);
        return $idx;
    }
}