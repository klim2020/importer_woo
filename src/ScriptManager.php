<?php

namespace App;

use App\Components\Attributes;
use App\Components\IProducts;
use App\Connectors\IAPIConnector;
use App\Readers\IStreamReader;


class ScriptManager
{
    private IStreamReader $datasource;
    private IAPIConnector $woocommercedata;

    public function __construct(IStreamReader $source, IAPIConnector $connector)
    {
        $this->datasource = $source;
        $this->woocommercedata = $connector;
    }


    public function updatedata(){

    }

    public function createglobalattributes(){
        echo "проверяем глобальные аттрибуты \n";
        //получаем глобальные аттрибуты с сервера
        $s_attributes = $this->woocommercedata->getAllAttributes();// все аттрибуты с сервера
        //получаем все продукты с сервера
        $s_products = $this->woocommercedata->getAllNormalizedProducts();
        echo "перебираем все продукты с сервера \n";
        foreach ($s_products->getAllProducts() as $key=>$product){
            echo "получаем аттрибуты продукта {$product['name']} \n";
            $p_attributes = $s_products->getAttributes($product['id']);
            //перебираем аттрибуты
            foreach ($p_attributes->getAllAttributes() as $vkey=>$val) {
                if ($val['id'] == 0) {
                    echo "аттрибут {$val['name']}  продукта {$product['name']} не зарегестрирован как глобальный или не обновлен айдишник\n";
                    //ищем по имени этот аттрибут в массиве с глобальными аттрибутами
                    $str = $s_attributes->getByName($val['name']);
                    if ($str !== false) {
                        //ищем по имени,  если
                        echo "в глобальном массиве был найден данный аттрибут, значит нужно просто обновить айдишник у товара\n";
                        $product['attributes'][$key]['id']=$str['id'];
                        $val['id'] = $str['id'];
                        $s_attributes->update($val);
                    } else {
                        echo " создаем этот аттрибут как глобальный и обновляем айдишник \n";
                        // но айдишника пока еще нету, так как аттрибут еще не создан на сервере
                        $val['visible']=true;
                        $res = $this->woocommercedata->createGlobalAttribute($val);
                        $res = json_decode(json_encode($res),true);
                        $s_attributes->update($res);
                        //обновляем  айди аттрибута в товаре
                        $val = $res;
                        echo "переполучаем все аттрибуты с сервера \n";
                        $s_attributes = $this->woocommercedata->getAllAttributes();
                    }
                    //TODO если не будет работать без обновления опций, нужно будет их создать
                    //После того, как аттрибуты были созданы,  пришло время для опций!, нужно убедиться в том,
                    //что данная опция у товара существует как глобальная,  тогда возможно удаление.
                }


                echo "получаем опции даннного атрибута с сервера \n";
                $s_options = $this->woocommercedata->getAttributeTerms($val['id']);
                if (count($val['options']) > 0) {//значение у товара было установлено
                    $result = $s_options->getByName($val['options'][0]);
                    if ($result === false) {// данная опция не зарегестрирована в глобальном аттрибуте
                       echo "добавляем данную опцию {$val['options'][0]} у аттрибута {$val['name']} глобально \n";

                        //$this->woocommercedata->updateGlobalAttributeTerm($val['id'], array('name' => $val['options'][0]));
                        $arrtcount = count($val['options']);
                        echo "количество опций аттрибута - {$arrtcount} \n";
                        $this->woocommercedata->updateGlobalAttributeTerm($val['id'], array('name' => $val['options'][0]));
                    }

                }else{
                    //значение у товара не установлено,  идем к источнику данных и смотрим там
                    $src_product_num = $this->datasource->getProductNum("mojki",$product['sku']);
                    $src_product = $this->datasource->getAllProducts("mojki")[$src_product_num];
                    $src_product_attributes = new Attributes($src_product['attributes']);
                    $i=1;
                    $i++;
                    echo "у источника данных";
                }
            }



            if(!$s_products->checkProductHash($product['id'],$product)) {
                echo "   обновляем продукт \n";
                $s_products->update($product);
                $this->woocommercedata->updateProduct($product);
            }
        }//все обновлено



        echo "done";
        //обновляем товар
    }



    public  function UpdateProducts(){
        //получаем массив с даными от источников
        //получаем массив с товарами с сервера
        //перебираем все источники
          //перебираем все продукты в каждом источнике
            //проверяем есть ли товар по sku в базе товаров с сервера
                //да-обновляем
                //нет - создаем

    }
    public function  uploadproducts(){

        echo "начинаем загрузку товаров \n";

        $src_products = $this->datasource->getAllProducts("mojki");

        $s_products = $this->woocommercedata->getAllNormalizedProducts();
        foreach ($src_products as $key=>$product){
            //проверить существует ли продукт

            $s_product_num = $s_products->getBySKU($product['sku']);

            if ($s_product_num !==false){
                echo "продукт {$product['name']}существует, обновляем данные\n";
                //
            //$this->woocommercedata->removeProduct($s_products->getAllProducts()[$s_product_num]['id']);
                $product['id']=$s_products->getAllProducts()[$s_product_num]['id'];
                $s_product = $s_products->getAllProducts()[$s_product_num];

            }else{
                echo "продукта {$product['name']} не существует, создаем\n";
                $product['id'] = '';
                //$s_product = $s_products->getAllProducts()[$s_product_num];
                $out = $this->woocommercedata->createProduct($product);
                $out = json_decode(json_encode($out),true);
                $s_products->update($out);
                $s_product = $out;
            }

            $s_product = json_decode(json_encode($s_product),true);
            //получаем список аттрибутов у продукта
            $product_attributes = new Attributes($product['attributes']);
            $s_attributes = $this->woocommercedata->getAllAttributes();
            $needUpdate = false;
            foreach ($product_attributes->getAllAttributes() as $key=>$attribute){
                $out = $s_attributes->getByName($attribute['name']);//массив от сервера с данным аттрибутом илои фалс
                if ($out ===false)
                //аттрибут не зарегестрирован как глобальный
                {
                   /*IAttribute*/
                    $attribute['visible'] = true;
                   $out = $this->woocommercedata->createGlobalAttribute($attribute)->getAllAttributes()[0];

                }
                //массив имеется,  проверяем опцию
                //ПРИСВОИТЬ АЙДИ АТТРИБУТА($out[id]) ПРОДУКТУ С СЕРВЕРА$s_product[attributes][порядковый_номер_аттрибута]
                $s_product_attributes = new Attributes($s_product['attributes']);
                $s_product_attribute_num = $s_product_attributes->getNumBySKU($out['name']);

                if ($s_product_attribute_num !== false){

                    $s_product['attributes'][$s_product_attribute_num]= $out;
                    $s_product['attributes'][$s_product_attribute_num]['options'][0]=$attribute['options'][0];
                }
                //$s_products->getAllProducts()[$s_product_num]['attributes'][$s_product_attribute_num]['id']

                if($this->woocommercedata->checkIfAttributeTermExists($out['id'],$attribute['options'][0])){
                    //опция Аттрибута   существует в глобальном массиве

                }else{//опции Аттрибута не существует
                    $arrtcount = count($attribute['options']);
                    echo "количество опций аттрибута - {$arrtcount} \n";
                    $this->woocommercedata->updateGlobalAttributeTerm($out['id'],array('name'=>$attribute['options'][0]));

                }
                $j=1;

            }
            $i = 1;
            foreach ($s_product['attributes'] as $key=>$val){
                $s_product['attributes'][$key]['visible']=true;
            }
            $this->woocommercedata->updateProduct($s_product);
            //TODO остановился тут нужно отладить с 155  до конца
        }

    }
    public function test(){
        //каждому товару присвоить глобальный аттрибут,  если его не существует то создать.
        //$this->datasource->getAllProductsNormalized();
        $attributes = $this->woocommercedata->getAllAttributes();// все аттрибуты с сервера
        $dataproducts = $this->datasource->getAllProducts('mojki');//продукты с импорта в нормальном виде
        //$dataattributes = $this->datasource->getAllAttributes();
        foreach ($dataproducts as $product){//начинаем перебор всех продуктов из файла

            if ($this->woocommercedata->getProduct($product['sku'])){//продукт добавлен на сайт
                $id = $this->woocommercedata->getProduct($product['sku'])->id;//получаем id с сайта
                $dataproductattributes = $product['attributes'];//все аттрибуты продукта файла

                foreach ($dataproductattributes as $key=>$attribute){//loop trough all atribute in datasource of a product
                    $arrtcount = count($attribute['options']);
                    echo "количество опций аттрибута - {$arrtcount} \n";
                    $name = $attribute['name'];
                    $attributeId = $this->woocommercedata->globalAttributeExists($name);
                    if($attributeId){//если этот аттрибут существует на сервере и он глобальный
                       // $attributeId = $this->woocommercedata->getAllAttributeId($name);//получаем id аттрибута
                        echo "обновляем глобальный аттрибут {$attribute['name']}, пробуем добавить значение {$attribute['options'][0]}\n";
                        $arrtcount = count($attribute['options']);
                        echo "количество опций аттрибута - {$arrtcount} \n";
                        $termId = $this->woocommercedata->updateGlobalAttributeTerm($attributeId, array('name'=>$attribute['options'][0]));//обновляем аттрибут
                    }else{//аттрибута не существует
                        echo "создаем  аттрибут {$attribute['name']}-> {$attribute['options'][0]}\n";
                        $name['visible']=true;
                         $attributeId= $this->woocommercedata->createGlobalAttribute($name)->id;//создаем глобальный аттрибут

                        $arrtcount = count($attribute['options']);
                        echo "количество опций аттрибута - {$arrtcount} \n";
                         $termId = $this->woocommercedata->updateGlobalAttributeTerm($attributeId, array('name'=>$attribute['options'][0]));

                    }
                    sleep(2);
                    echo "пробуем обновить опцию продукта  {$id} с аттрибутом  {$attribute['name']}-> {$attribute['options'][0]}\n";
                    $this->woocommercedata->updateProductAttributeWithTerm($termId,$attributeId,$id);
                }

                $i = 1;
            }else{
                $ii =0;
            }//продукта нет на сайте
        }

        $i=1;
    }

    public function uploadOrUpdateData()
    {
        /*
         * получаем все данные с источника
         * sourcedataprovider, serverdataprovider - IDataProvider
         * $src_products = $this->sourcedataprovider->getRawProducts();//IProducts
         * $server_products = $this->serverconnector->getRawProducts();//IProducts
         * foreach($src_products->iterator() as $key=>$src_product){//IProduct
         *      //if ($server_products->searchBySKU($src_product->getSKU())!==false){//продукт есть на сервере проверяем аттрибуты
         *          $src_attributes = $value->getAttributes() //IAttributes
         *          foreach($src_attributes->iterator() as $attr_key=>$attr_value){$attr_value - IAttribute
         *          //перебираем каждый аттрибут
         *
         *          //проверяем зарегестрирован ли данный аттрибут  как глобальный
         *          $isGlobalAttribute = $this->serverdataprovider->compareAttribute($attr_value->getName())
         *          if ($isGlobalAttribute){//аттрибут зарегестрирован как глобальный
         *             $srv_attr = $this->serverdataprovider->getAttribute();
         *          }else{//аттрибут не зарегестрирован как глобальный
         *             $srv_attr = $this->serverdataprovider->registerGlobalAttribute($attr_value);//регестрируем
         *          }
         *          //$srv_attr  - IAttribute аттрибут с сервера, с правильным полем айди, как на сервере, но при етом в переменной
         *          //$attr_value  хранится тоже самое только с скрипта.
         *         // $server_products->getBySKU($src_product['sku'])->
         * }
         *      }
         *      //$value - IProduct
         *      $server_products->
         * }
         *
         *
         * */
    }

}