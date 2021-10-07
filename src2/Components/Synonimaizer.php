<?php

namespace Apps\Components;

/**
 *
 */
class Synonimaizer
{
    /** Nahodit synonim dlya polya name
     * alhoritm dlya konkretnoho polya opredelyaetsya v potomke
     * @param $name imya polya, obezatelno s malenkoj bukvy, v klasse potomke eta funkcija budet nazyvatsya synonimize + name(s bolshoj bukvy)
     * @param $value
     * @return libo samo znachenie libo obrabotannoje
     */
    public function synonimize($name, $value){
      $fname = "synonimize".ucfirst($name);
      if (method_exists($this,$fname)){
          return $this->$fname($value);
      }
      return $value;
    }
}