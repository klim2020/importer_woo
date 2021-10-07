<?php

namespace App\Components;

class Options extends Products implements IOptions
{

    public function checkOptionHash($id, $data)
    {
        return $this->checkProductHash($id,$data);
    }

    /**
     * @inheritDoc
     */
    public function __construct($path)
    {
        $this->data = json_decode(json_encode($path),true);
        foreach ($this->data as $key=>$val){
            $this->ids[$key] = $val['id'];
            $this->skus[$key] =$this->removespacesandmakelowercase($val['name']);
            $this->hashes[$key] = $this->hash($val);

        }
    }

    public function getByName($name)
    {
        $idx = array_search($this->removespacesandmakelowercase($name),$this->skus);
        if ($idx === false){
            return false;}
        return $this->data[$idx];
    }
}