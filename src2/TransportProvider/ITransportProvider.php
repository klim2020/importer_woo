<?php

namespace Apps\TransportProvider;

interface ITransportProvider
{
    /** возвращает соединение в том или ином виде
     * @return mixed
     */
    public function getConnection();
}