<?php
class language {

   public $data;

   function __construct($lang) {
      $data = file_get_contents("_lang/".$lang . ".json");
      $this->data = json_decode($data);
   }

   function translate() {
        return $this->data;
   }
}
?>