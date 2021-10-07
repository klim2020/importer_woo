<?php
namespace  Apps;
use Apps\Exporters\ExporterSandi;
use Apps\Importers\ImporterWoocommerce;
use Apps\Managers\Sandi2WoocommerceManager;
use Apps\TransportProvider\TransportProviderWoocommerce;
use Apps\TransportProvider\TransportProviderSandi;

require_once 'vendor/autoload.php';
require_once 'src2/config.php';


//echo ":";

  $manager_sandi2woo =  new Sandi2WoocommerceManager();

  $importer_woocommerce = new ImporterWoocommerce(new TransportProviderWoocommerce( $config['woocommerce'] ));
  $exporter_sandi = new ExporterSandi(new TransportProviderSandi($config['sandi-mojki']));

  $manager_sandi2woo->addImporter($importer_woocommerce);
  $manager_sandi2woo->addExporter($exporter_sandi);

 // запускаем процесс обработки

  $manager_sandi2woo->runMaintanceProcedure();
 /*
 * if ($manager_sandi2woo->getResult()->success()){
 *    echo "обновленные продукты = {$manager_sandi2woo->getResult()->getUpdatedProductCount()}";
 *    echo "добавленные продукты = {$manager_sandi2woo->getResult()->getAddedProductCount()}";
 *    echo "удаленные продукты = {$manager_sandi2woo->getResult()->getDeletedProductCount()}";
 * // процедура обновления завершилась удачно
 * }else{
 *    echo "при обновлении произошла ошибка $manager_sandi2woo->getResult()->getLastError()";
 *
 * //процедура обновления завершилась неудачно
 * }
 *
 * */
