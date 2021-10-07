<?php

namespace Apps\Managers;

use Apps\Components\ISynonimaizer;
use Apps\Components\SandiSynonimizer;
use Apps\Components\Synonimaizer;
use Apps\Exporters\IExporter;
use Apps\Importers\IImporter;
use Apps\MessageHandlers\IMessageHandler;
use Apps\MessageHandlers\MessageHandlerStandard;
use Aj

class Sandi2WoocommerceManager implements IManager
{
    private IImporter $importer;
    private IExporter $exporter;
    private Synonimaizer $synonimizer;

    /**
     * @param ISynonimaizer $synonimizer
     */
    public function setSynonimizer(Synonimaizer $synonimizer): void
    {
        $this->synonimizer = $synonimizer;
    }
    private IMessageHandler $msg;

    public function __construct()
    {
        $msg = new MessageHandlerStandard();
        $msg->setState('created');//устанавливаем  этап - создание
    }

    /**
     * @inheritDoc
     */
    public function addImporter(IImporter $importer)
    {
        $this->importer = $importer;
        //$importer->CheckConnection();
    }

    /**
     * @inheritDoc
     */
    public function addExporter(IExporter $exporter)
    {
        $this->exporter = $exporter;
        //$exporter->CheckConnection();
    }

    /**
     * @inheritDoc
     */
    public function runMaintanceProcedure()
    {
        $syn = new SandiSynonimizer();
        $out = $syn->synonimize("description",'Кухонная мойка Lidz (BLA-03) D510/200 изготовлена из качественного искусственного камня, имеет черную матовую поверхность, поэтому будет хорошо смотреться со смесителем того же цвета. Отменное качество и дизайн мойки позволят удовлетворить самые высокие требования. Она выполнена с рабочей чашей с отверстием слив-перелив и отверстием под смеситель. Мойка монтируется врезным способом в отверстие в столешнице.');
        $syn->convertimage();
        echo $out;
        // TODO: Implement runMaintanceProcedure() method.
    }

    /**
     * @inheritDoc
     */
    public function getResult(): IMessageHandler
    {
        return $this->msg;

    }
}