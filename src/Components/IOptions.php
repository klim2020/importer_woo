<?php

namespace App\Components;

interface IOptions extends IProducts
{
    public  function checkOptionHash($id, $data);

    public function getByName($name);


}