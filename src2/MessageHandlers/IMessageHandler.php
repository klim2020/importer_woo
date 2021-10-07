<?php

namespace Apps\MessageHandlers;

interface IMessageHandler
{

    /**
     * установка статуса процесса
     * @param $statusname - success, fail, warning, error
     * @return mixed void
     */
    public function setStatus($statusname);


    /**
     * установка этапа процесса
     * @param $statusname - имя этапа
     * @return mixed void
     */
    public function setState($statename);
}
