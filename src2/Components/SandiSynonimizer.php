<?php

namespace Apps\Components;





class SandiSynonimizer extends Synonimaizer
{
    public function synonimizeName($val){
        $re = '/\s*\(.*\)$/m';
        return preg_replace($re,'',$val);
        //return $val;
    }
    public function synonimizeDescription($val)
    {
        $replaces = array(
                '/Кухонная мойка/m'=>'Мойка для кухни',
                '/поэтому/m'=>'а значит',
                '/со смесителем/m'=>'вместе с рукомойником',
                //'//m'=>'',
                '/изготовлена/m'=>'произведена'
            );
        return preg_replace(array_keys($replaces),array_values($replaces),$val);
     }

     public function convertimage(){
         $watermark = new Watermark('/home/klim/Загрузки/123.jpg');
         //Ajaxarra
         $watermark->withText('ajaxray.com')
             ->setFontSize(48)
             ->setRotate(30)
             ->setOpacity(.4)
             ->write('/home/klim/Загрузки/321.jpg');

     }

}