<?php

namespace App\Classes;

class Record
{
   private $data;
   private $hash;
    public function __construct($arr)
    {
        $this->data = json_decode(json_encode($arr),true);
        $this->hash();
    }
    private function hash(){
        $this->hash = hash('md5',json_encode($this->data));
    }
    protected  function getValue($key){
        return $this->data[$key];
    }
    protected function setValue($key,$val){
        $this->data[$key]= $val;
        $this->hash();
    }
    public function update($data){
        $datahash = hash('md5',json_encode($data));
        if (strcmp($datahash,$this->hash()!=0)){
            $this->data = array_replace($this->data,$data);
        }
    }
}