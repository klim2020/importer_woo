<?php

namespace Apps\Importers;


use App\Classes\IOptions;
use Apps\Components\IAttribute;
use Apps\Components\IAttributeOption;
use Apps\Components\IProduct;
use Apps\TransportProvider\ITransportProvider;

/**
 * инкапсулирует работу с выходными данными,
 * задача менеджерить товары, аттрибуты,  опции с сервера
 *
 */
interface IImporter
{

    /**
     *
     * Создаем Импортера товаров
     * получаем список товаров, аттрибутов, опций с сервера
     * @param ITransportProvider $provider
     */
    public function __construct(ITransportProvider $provider);

    /**
     * Добавляет продукт к списку  продуктов
     *
     * @param IProduct $product
     * @return mixed
     */
    public function addProduct(IProduct $product);

    /**
     * Добавляет аттрибут к продукту,
     * !проверки на соответствие глобальным аттрибутам не происходит
     *
     * @param IProduct $product
     * @param IAttribute $attribute
     * @return mixed
     */
    public function addAttribute(IProduct $product, IAttribute $attribute);

    /**
     * * Добавляет опцию  к  аттрибуту продукта,
     * !проверки на соответствие глобальным опциям не происходит
     *
     * @param IProduct $product
     * @param IAttribute $attribute
     * @param IAttributeOption $option
     * @return mixed IMessageHandler
     */
    public function addOption(IProduct $product, IAttribute $attribute, IAttributeOption $option);

    /**
     *
     * Добавляет глобальный аттрибут
     * @param IAttribute $attr
     * @return mixed IMessageHandler
     */
    public function addGlobalAttribute(IAttribute $attr);

    /**
     * Добавляет глобальную опцию к аттрибуту.
     * @param IAttribute $attr
     * @param IAttributeOption $option
     * @return mixed IMessageHandler
     */
    public function addGlobalOption(IAttribute $attr, IAttributeOption $option);

    /**
     * @return mixed
     */
    public function getAllGlobalAttributes();






}