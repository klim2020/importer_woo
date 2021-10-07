<?php

namespace Apps\MessageHandlers;

class MessageHandlerStandard implements IMessageHandler
{

    public function setStatus($statusname)
    {
        $this->data["status"] = $statusname;
    }

    public function setState($statename)
    {
        $this->data["state"] = $statename;
    }
}