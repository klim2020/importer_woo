<?php
namespace App\Importer;
set_time_limit(0); // this way
ini_set('max_execution_time', 0); // or this way


use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use DOMDocument;


class WooImporter {
    private $wcfg;//конфиг
    private array $rawdataJSON;
    private array $rawdataXML;
    private array  $products;//все товары с сервера
    private array $importproducts;
    private array $readers;



    public function __construct()
    {

        $this->rawdataJSON = array();
        $this->rawdataXML = array();
        $this->wcfg = $this->getWoocommerceConfig();
    }

    /**
     *  Reads Json data from file/stream
     *  return переведенные данные,  также добавляет данные в риватную переменную
     */
    function readJson(string $path, $key){
        $json = json_decode(file_get_contents($path), true);
        $this->rawdataJSON[$key] = $json;
        return $json;
    }

    function readXML(string $path, $key){
        $doc = new DOMDocument();
        $doc->loadHTML(file_get_contents($path));
       // $json = simplexml_load_file($path);
        $this->rawdataXML[$key] = $doc;


        return $doc;
    }

    function getJson($key){
        return $this->rawdataJSON[$key];
    }

    function getWoocommerceConfig()
    {


        $woocommerce = new Client(
            'https://lighthouse-kr.com.ua/',
            'ck_e327099e37187732497b314ac605e1ef257c9d87',
            'cs_1e2d304caefb14284464789bc0fbd64dd9de55af',
            [
                'wp_api' => true,
                'version' => 'wc/v2',
                'query_string_auth' => false,
            ]
        );

        return $woocommerce;
    }

    function checkProductBySku($skuCode)
    {


       $products = $this->getAllProducts();
        foreach ($products as $product) {

            $currentSku = strtolower($product->sku);
            $skuCode = strtolower($skuCode);
            if ($currentSku === $skuCode) {
                return ['exist' => true, 'idProduct' => $product->id];
            }
        }
        return ['exist' => false, 'idProduct' => null];
    }

    function createProducts()
    {

        $data2 = [
            'options'=> array(
                'czcvx'
            ),

        ];

        $this->wcfg->put('products/attributes/2', $data2);
        $qq24 = $this->wcfg->get('products/attributes/1/terms');



        $woocommerce = $this->wcfg;
        $products = $this->getJson("mojkiJSON");

        $imgCounter = 0;
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
            foreach ($product['images']['additional'] as $val){
                $images[] = $val;
            }
            //$articulos = $product['articulos'];
            $cat_id = $product['main']['category'];
            $categories = array(array('name' =>$products['categories'][$cat_id]['name']['ru']));
            $categoriesIds = array();
            foreach ($images as $image) {
                $imagesFormated[] = [
                    'src' => $image,
                    'position' => 0
                ]; /* TODO: FIX POSITON */
                $imgCounter++;
            }


            /* Prepare categories */
            foreach ($categories as $category) {
               // $categoriesIds[] = ['id' => getCategoryIdByName($category)];
            }
            $finalProduct = [
                'name' => $name,
                'slug' => '',
                'sku' => $sku,
                'description' => $description,
                'images' => $imagesFormated,
                'categories' => $categories,
                'price'=>$product['main']['prices']['retail']['current'],
                'regular_price'=>$product['main']['prices']['retail']['current'],


                //'attributes' => getproductAtributesNames($articulos)
                ''

            ];
            $attributes = array();
            foreach ($product['attributes'] as $key=>$val){

                $attributes[] = array(
                    'name'=>$products['attributes'][$key]['ru'],
                    'variation'=>false,
                    'visible'=>true,
                    'options'=> array(
                       $val['ru']
                    ),

                );
            }
            $finalProduct['attributes']= $attributes;
            echo "продукт ". $finalProduct['name']. '...';
            $productExist = $this->checkProductBySku($sku);
            if (!$productExist['exist']) {
                echo "не присутствует в базе,   \e[7m пробуем добавить \e[7m ....";
                $productResult = $woocommerce->post('products', $finalProduct);
                echo "успех!!!";
                //echo $productResult;
            } else {
                /*Update product information */
                echo "присутствует в базе,  \e[5m пробуем обновить \e[5m....";
                $idProduct = $productExist['idProduct'];
                $productResult = $woocommerce->put('products/' . $idProduct, $finalProduct);
                echo "успех!!!";
            }
            echo "\n";
            //$productResult;
        }
    }

    function getCategoryIdByName($categoryName)
    {
        $woocommerce = $this->getWoocommerceConfig();
        $categories = $woocommerce->get('products/categories');
        foreach ($categories as $category) {
            if ($category['name'] == $categoryName) {
                return $category['id'];
            }
        }
    }

    public function getAllProducts(){
        if (empty($this->products)){
            $woocommerce =  $this->getWoocommerceConfig();
            $this->products=array();
            $i=1;
            do {
                $prods = $woocommerce->get('products',['per_page'=>100,'page'=>$i]);
                $i++;
                $this->products = array_merge($this->products,$prods);
            }while (count($prods)>0);


        }
        return $this->products;
    }

}











function createCategories()
{
    $categoryValues = getCategories();
    $woocommerce = getWoocommerceConfig();

    foreach ($categoryValues as $value) {
        if (!checkCategoryByname($value)) {
            $data = [
                'name' => $value
            ];
            $woocommerce->post('products/categories', $data);
        }
    }
}

function checkCategoryByName($categoryName)
{
    $woocommerce = getWoocommerceConfig();
    $categories = $woocommerce->get('products/categories');
    foreach ($categories as $category) {
        if ($category['name'] === $categoryName) {
            return true;
        }
    }
    return false;
}

/** CATEGORIES  **/
function getCategories()
{
    $products = getJsonFromFile();
    $categories = array_column($products, 'categorias');

    foreach ($categories as $categoryItems) {
        foreach ($categoryItems as $categoryValue) {
            $categoryPlainValues[] = $categoryValue;
        }
    }
    $categoryList = array_unique($categoryPlainValues);
    return $categoryList;
}




function getproductAtributesNames($articulos)
{
    $keys = array();
    foreach ($articulos as $articulo) {
        $terms = $articulo['config'];
        foreach ($terms as $key => $term) {
            array_push($keys, $key);
        }
    }
    /* remove repeted keys*/
    $keys = array_unique($keys);
    $configlist = array_column($articulos, 'config');
    $options = array();
    foreach ($keys as $key) {
        $attributes = array(
            array(
                'name' => $key,
                'slug' => 'attr_' . $key,
                'visible' => true,
                'variation' => true,
                'options' => getTermsByKeyName($key, $configlist)
            )
        );
    }
    return $attributes;
}

function getTermsByKeyName($keyName, $configList)
{
    //var_dump($configList);
    $options = array();
    foreach ($configList as $config) {
        foreach ($config as $key => $term) {
            if ($key == $keyName) {
                array_push($options, $term);
            }
        }
    }
    return $options;
}

function prepareInitialConfig()
{
    echo ('Importing data, wait...')."\n";
    createCategories();
    createProducts();
    echo ('Done!')."\n";
}

//prepareInitialConfig();


?>


