<?php

use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

echo '<div class="error-box">
                    <h1>404</h1>
                    <h3>' . $lang->System->error404->title . '</h3>
                    <div class="info">
                    ' . $lang->System->error404->subtitle . '
                    </div>
                    
                </div>';
