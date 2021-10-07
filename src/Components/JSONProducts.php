<?php

namespace App\Components;


class JSONProducts extends Products implements IProducts
{
    private  $rawData = array();//сырые данные,   может когда нить пригодятся
    function slugify($string) {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);
        $string = preg_replace('/[-\s]+/', '-', $string);
        return trim($string, '-');
    }
    public function __construct($path)
    {

        $json = json_decode(file_get_contents($path), true);
        $products = $json;
        $this->rawData = $json;
        foreach ($products['products'] as $product) {
            /*Chec sku before create the product */
            //$productExist = checkProductBySku($product['sku']);


            $imagesFormated = array();
            /*Main information */
            $name = $product['main']['name']['ru'];
            //$slug = $product['url'];
            $sku = $product['main']['sku'];
            $description = $product['main']['description']['ru'];
            $images = array($product['images']['main']);
            foreach ($product['images']['additional'] as $val) {
                $images[] = $val;
            }
            //$articulos = $product['articulos'];
            $cat_id = $product['main']['category'];
            $categories = array(array('name' => $products['categories'][$cat_id]['name']['ru']));
            $categoriesIds = array();
            foreach ($images as $image) {
                $imagesFormated[] = [
                    'src' => $image,
                    'position' => 0
                ]; /* TODO: FIX POSITON */

            }


            /* Prepare categories */
            foreach ($categories as $category) {
                // $categoriesIds[] = ['id' => getCategoryIdByName($category)];
            }
            $finalProduct = [
                'name' => $name,
                'slug' => '',
                'sku' => $sku,
                'id'=>$sku,
                'description' => $description,
                'images' => $imagesFormated,
                'categories' => $categories,
                'price' => $product['main']['prices']['retail']['current'],
                'regular_price' => $product['main']['prices']['retail']['current'],


                //'attributes' => getproductAtributesNames($articulos)
                ''

            ];
            $attributes = array();
            foreach ($product['attributes'] as $key => $val) {
                $attributes[] = array(
                    'name' => $products['attributes'][$key]['ru'],
                    'slug'=>"pa_".$this->slugify($products['attributes'][$key]['ru']),
                    'variation' => false,
                    'visible' => true,
                    'options' => array(
                        $val['ru']
                    ),

                );
            }

            $finalProduct['attributes'] = $attributes;

            $this->ids[] = $sku;
            $this->skus[] = $sku;
            $this->data[] = $finalProduct;
            $this->hashes[] = $this->hash($finalProduct);

            echo $this->checkProductHash($sku,$finalProduct);
        }


        //return $json;
    }

    public function update($data)
    {
        // TODO: Implement update() method.
    }

    public function checkProductHash($id, $data)
    {
        // TODO: Implement checkProduct() method.
    }



}