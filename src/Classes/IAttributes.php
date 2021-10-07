<?php

namespace App\Classes;

interface IAttributes
{

    /** получает распарсиваемый массив
     * @param $arr
     */
    public function __construct($arr);




    /**получает товар по id
     * @return mixed
     */
    public  function getByID($id);

    /**
     * получает птовар по $sku
     * @return mixed
     */
    public  function getByName($name);



    /**
     * @param $data
     * @return mixed
     */
    public  function update($data);


    /**
     * Проверяет изменился ли продукт
     * @return mixed true если не изменился,  false если изменился
     */
    public  function checkAttribute($id,$data);

    public function getAllAttributes();

    public function getNumByID($id);



}