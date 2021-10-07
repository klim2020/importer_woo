<?php
//конфигурационные файлы для установления подключения
 $config = array(
        "woocommerce" => array(
          'address' => 'https://mojki.com.ua/',
          'key1' => 'ck_eb30cb1a5576274a5919cd91876f102b71ddfedc',
          'key2' => 'cs_5f7870f341a0f7c5810a96529e9545f02a87212f',
          'options' => [
              'wp_api' => true,
              'version' => 'wc/v3',
              'query_string_auth' => false,
          ]
        ),
        "sandi-mojki"=>array(
            "url"=>'https://b2b-sandi.com.ua/export/view/36ce20096675493583a131edbb836408-8675-1629136354/json'
        )
    );

 ?>
