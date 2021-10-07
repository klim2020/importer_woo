<?php

namespace Apps\Managers;

use Apps\Exporters\IExporter;
use Apps\Importers\IImporter;
use Apps\MessageHandlers\IMessageHandler;

/**
 * Коммутирует связь между импортером и експортером
 */
interface IManager
{


    /**
     * добавляет импортера товаров в менеджер
     * @param IImporter $importer
     * @return mixed
     */
    public function addImporter(IImporter $importer);

    /**
     * Добавляем экспортера товаров в менеджер
     * @param IExporter $exporter
     * @return mixed
     * @throws в случае 
     */
    public function addExporter(IExporter $exporter);

    /**
     * Запуск процедуры перевода
     * @return mixed
     */
    public function runMaintanceProcedure();

    /**
     * сообщения после работы
     * @return IMessageHandler
     */
    public function getResult():IMessageHandler;


}