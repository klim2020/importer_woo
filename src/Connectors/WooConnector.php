<?php

namespace App\Connectors;

use App\Components\Attributes;
use App\Components\IAttributes;
use App\Components\IOptions;
use App\Components\IProducts;
use App\Components\Options;
use App\Components\WooCommerceProducts;
use Automattic\WooCommerce\Client;

//echo __DIR__;
require_once __DIR__.'/../config.php';

class WooConnector implements IAPIConnector
{
   private $wcfg;//конфиг
   private IProducts  $products;
   private IAttributes $attributes;
   private $arraywithterms = array();


   public function __construct()
   {
       $this->initConnection(getConfig());
       $this->products = new WooCommerceProducts($this->wcfg);
       $this->attributes =new Attributes($this->wcfg->get('products/attributes'));
   }

    /**
     * @inheritDoc
     */
    public function initConnection($data)
    {
        $woocommerce = new Client($data['address'],
                                    $data['key1'],
                                    $data['key2'],
                                    $data['options']);

        $this->wcfg = $woocommerce;
    }

    /**
     * @inheritDoc
     */
    public function getAllNormalizedProducts():IProducts
    {
        $this->products = new WooCommerceProducts($this->wcfg);
        return $this->products;
    }

    /**
     * @inheritDoc
     */
    public function getProduct($sku)
    {
         return $this->products->getBySKU($sku);
    }

    /**
     * @return mixed
     */
    public function getAllAttributes():IAttributes
    {
        if (empty($this->attributes->getAllAttributes())){
            $this->reloadAttributes();
        }
        return $this->attributes;

    }

    public function reloadAttributes():IAttributes{
        $this->attributes =new Attributes($this->wcfg->get('products/attributes'));
        return $this->attributes;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAllProductAttributesById($id)
    {
        return new Attributes($this->products->getByID($id)['attributes']);
       // return $this->wcfg->get('products/attributes/'.$id);
    }

    /**
     * @param $name
     */
    public function createGlobalAttribute($data)
    {
        $data = [
            'name' => $data['name'],
            'slug' => 'pa_'.substr(preg_replace('/[^A-zА-я0-9]/m','',$this->slugify($data['name'])),0,27),
            'type' => 'select',
            'order_by' => 'menu_order',
            'visible' => true,
            'has_archives' => true
        ];

        $res = $this->wcfg->post('products/attributes', $data);
        $this->reloadAttributes();
        return new Attributes(array($res));
    }

    /**
     * @param $name
     */
    public function globalAttributeExists($name)
    {

        foreach ( $this->getAllAttributes() as $attribute){
            if (strpos($attribute->name,$name)!==false){
                return $attribute->id;
            }
        }
        return false;

    }

    public function updateGlobalAttributeTerm($attributeId, $data)
    {

          $foundorupdated = false;


        $woocommerce =  $this->wcfg;
       /* $terms=array();
        $i=1;
        do {
            $prods = $woocommerce->get('products/attributes/'.$attributeId.'/terms',['per_page'=>100,'page'=>$i]);
            $i++;
            $terms = array_merge($terms,$prods);
            //$this->skus = array_merge($this->skus,array_column($prods,'sku'));
            //$this->ids = array_merge($this->ids,array_column($prods,'id'));
        }while (count($prods)>0);


          //$terms = $this->wcfg->get('products/attributes/'.$attributeId.'/terms' ,array());
          foreach ($terms as $term){//перебор всех термов с сервера*/
          //  if (strpos(preg_replace('/\s*/', '', mb_strtolower($term->name)),preg_replace('/\s*/', '', mb_strtolower($data['name'])))!==false){
          //      if ($term->id == 0){
          //     echo "обновляем опцию аттридута с именем {$data['name']}";
         //       $out = $this->wcfg->put('products/attributes/'.$attributeId.'/terms/'.$term->id, $data);
          //      }else{$out=$term;}
          //      return $out->id;
          //  }
         // }
         //если после цикла ничего не обновлено, значит опции аттрибута не существует, добавляем
        echo "создаем опцию аттрибута {$attributeId} с именем - {$data['name']}\n";
          $out = $this->wcfg->post('products/attributes/'.$attributeId.'/terms/', $data);


        $this->reloadAttributeTerm($attributeId);
        return $out->id;
    }

    public function getProductById($id)
    {
        if (empty($this->products)){
            $this->getAllNormalizedProducts();
        }
        $f = array_search($id,$this->ids);
        return $this->products[$f];
    }

    public function getTermById($attributeId, $termId)
    {
        return $this->wcfg->get('products/attributes/'.$attributeId.'/terms/'.$termId);
    }

    public function updateProductAttributeWithTerm($termId, $attributeId, $productId)
    {
        //кешированый продукт
        $changed = false;
        $product = $this->getProductById($productId);
        $term = $this->getTermById($attributeId, $termId);
        $attribute = $this->getAttributeById($attributeId);
        foreach ($product->attributes as $item){
            $s1 = mb_strtolower($item->name);
            $s2 = mb_strtolower($attribute->name);
            if(strcmp($s2,$s1)==0){
                  $item->id = $attribute->id;
                  $changed = true;
            }
        }
        if ($changed){
            echo "данные {$product->name} бли изменены  поетому обновляем аттрибуты \n";
            $data = json_decode(json_encode($product),true);
            $this->wcfg->put('products/'.$product->id, $data);

        }
        return false;

    }

    public function updateProduct($product)
    {
        //unset($product['attributes']);
        $this->wcfg->put('products/'.$product['id'], json_decode(json_encode($product),true));
    }

    private function getAttributeById($attributeId)
    {
        return $this->wcfg->get('products/attributes/'.$attributeId);
    }


    function slugify($string) {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);
        $string = preg_replace('/[-\s]+/', '-', $string);
        return trim($string, '-');
    }

    public function getAttributeTerms($id): IOptions
    {

        if (array_key_exists($id,$this->arraywithterms)){
            return $this->arraywithterms[$id];
        }
        $terms=array();
        $i=1;
        do {
            $prods = $this->wcfg->get('products/attributes/'.$id.'/terms',['per_page'=>100,'page'=>$i]);
            $i++;
            $terms = array_merge($terms,$prods);
            //$this->skus = array_merge($this->skus,array_column($prods,'sku'));
            //$this->ids = array_merge($this->ids,array_column($prods,'id'));
        }while (count($prods)>0);
        $this->arraywithterms[$id] = new Options($terms);
       return $this->arraywithterms[$id];
    }
    public function reloadAttributeTerm($id){

        if (array_key_exists($id,$this->arraywithterms)){
            unset($this->arraywithterms[$id]);
        }
        return $this->getAttributeTerms($id);

    }

    public function removeProduct($id)
    {
        $this->wcfg->delete("products/{$id}", ['force' => true]);
    }

    public function createProduct($data)
    {
        return $this->wcfg->post('products', $data);
    }

    public function checkIfAttributeTermExists($id, $int)
    {
        $terms = $this->getAttributeTerms($id);
        foreach ($terms->getAllProducts() as $key=>$term ){
            if (strcmp($int, $term['name']) === 0){
                return true;
            }
        }
        return false;

    }
}