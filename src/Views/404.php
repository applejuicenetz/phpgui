<?php

use appleJuiceNETZ\UI\Language;

$language = Language::getLanguage();


echo'<div class="row justify-content-center">
          <div class="col-md-6 col-sm-12">
            <div class="clearfix">
              <h1 class="float-start display-3 me-4">404</h1>
              <h4 class="pt-3">' . $language->translate('System.error404.title') . '</h4>
              <p class="text-body-secondary">' . $language->translate('System.error404.subtitle') . '</p>
              <br><br><br><br><br><br>
            </div>
            </div>
        </div>
      
   ';
