<?php

namespace App\Classes;

interface IOptions extends IProducts
{
    public  function checkOptionHash($id, $data);

    public function getByName($name);
}