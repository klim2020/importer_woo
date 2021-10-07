<?php

namespace App\Components;

/**
 * стандартизированный список продуктов,  в формате для импорта в wp api
 */
interface IProducts
{

    /**
     * задача,  скачать, открыть файл с продуктами,  стандартизировать,
     * сохдать массивы с ключами для поиска по sku  id , создать массив с хешсуммами hash
     * @param $path
     */
    public function __construct($path);

    /**получает товар по id
     * @return mixed
     */
    public  function getByID($id);

    /**
     * получает птовар по $sku
     * @return mixed
     */
    public  function getBySKU($sku);

    /**
     * возвращает запись , объект типа
     * @param $key
     * @return mixed
     */
    public  function getFieldByID($id);

    /**
     * @param $data
     * @return mixed
     */
    public  function update($data);

    /** получает спиcок аттрибутов
     * @return IAttributes
     */
    public  function getAttributes($id):IAttributes;

    /**
     * Проверяет изменился ли продукт
     * @return mixed true если не изменился,  false если изменился
     */
    public  function checkProductHash($id, $data);



    /**
     * @return mixed
     */
    public function getAllProducts();
}