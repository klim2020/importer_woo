<?php

namespace App\Connectors;

use App\Components\IAttributes;
use App\Components\IOptions;
use App\Components\IProducts;

interface IAPIConnector
{
    //public function __construct($source);

    /**
     * @param $data  массив с параметрами подключения
     * @return mixed true false успешно/неуспешно
     */
    public function initConnection($data);

    /**
     * Получает массив с продуктами с сервера, готовые для отправки
     * При этом перезаходит на сервер для перевыгрузки.
     * @return mixed массив с продуктами
     *
     */
    public function getAllNormalizedProducts():IProducts;

    /**
     * @param $sku //идентификатор товара
     * @return mixed //если ок -товар, если нет то ложь !!!проверка ===
     */
    public function getProduct($sku);

    public function getProductById($id);

    public function getAllAttributes():IAttributes;

    public function getAllProductAttributesById($id);

    public function createGlobalAttribute($data);

    public function globalAttributeExists($name);

    /**
     * @param $attributeId
     * @param {
                    "id": 23,
                    "name": "XXS",
                    "slug": "xxs",
                    "description": "",
                    "menu_order": 1,
                    "count": 1,
                    "_links": {
                    "self": [
                    {
                    "href": "https://example.com/wp-json/wc/v3/products/attributes/2/terms/23"
                    }
                    ],
                    "collection": [
                    {
                    "href": "https://example.com/wp-json/wc/v3/products/attributes/2/terms"
                    }
                    ]
                    }
                    }
     * @return mixed
     */
    public function updateGlobalAttributeTerm($attributeId, $ru);

    public function updateProductAttributeWithTerm($termId, $attributeId, $productId);

    public function getTermById($attributeId, $termId);

    public function updateProduct($product);

    /** получает все опции для данного аттрибута с сервера
     * @param $id ийди аттрибута
     * @return IOptions
     */
    public function getAttributeTerms($id):IOptions;

    public function removeProduct($id);
    public function createProduct($data);

    public function reloadAttributes():IAttributes;

    public function checkIfAttributeTermExists($id, $int);

    public function reloadAttributeTerm($id);


}