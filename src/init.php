<?php
use App\ScriptManager;
use App\Connectors;
use App\Readers;







$mgr = new ScriptManager(
               new App\Readers\SandiStreamReader(
                   array(
                       'mojki'=>"https://b2b-sandi.com.ua/export/view/36ce20096675493583a131edbb836408-8675-1629136354/json"
                   )),
                new App\Connectors\WooConnector()
);
//$mgr->test();
$mgr->uploadproducts();

$mgr->uploadOrUpdateData();
//$mgr->createglobalattributes();