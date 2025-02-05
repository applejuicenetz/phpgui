<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

$template = new template();

?>
<!DOCTYPE html><!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.0.0
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2024 creativeLabs ukasz Holeczek
* Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
-->
<html lang="de">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - appleJuice phpGUI</title>
    <link rel="icon" type="image/png" sizes="16x16" href="favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="themes/CoreUI/vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="themes/CoreUI/css/vendors/simplebar.css">
    <!-- Main styles for this application-->
    <link href="themes/CoreUI/css/style.css" rel="stylesheet">
   <script src="themes/CoreUI/js/config.js"></script>
    <script src="themes/CoreUI/js/color-modes.js"></script>
  </head>
  <body>
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card-group d-block d-md-flex row">
              <div class="card col-md-7 p-4 mb-0">
                <div class="card-body">
                  <h1>Login</h1>
                  <p class="text-body-secondary"><?php echo $lang->Login->headline; ?></p>
                  <?php
                $core = new Core();
				if(!empty($_SESSION['login']['host'])){
					$template->alert("danger","Warnung!","Kann zum Core nicht verbinden.");
					$_SESSION['login']['host'] ="";
				}
				if(!empty($_SESSION['login']['wrong_pass'])){
					$template->alert("danger","Warnung!","Falsches Passwort");
					$_SESSION['login']['wrong_pass'] = "";
				}
				echo '
                <form name="core" action="index.php?login=1" method="post" autocomplete="off">
    				<div class="form-floating mb-3">
                        <input type="url" class="form-control" placeholder="Core-URL" name="host" id="chost" value="'.($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']).':'.$_ENV["CORE_PORT"].'" required/>
                        <label for="floatingInput">Core-URL</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="cpass" id="cpass"
                               required/>
                               <label for="floatingInput">'.$lang->Login->password.'</label>
                    </div>
                    <div class="row">
                        <div class="col-">
                            <div class="checkbox icheck m-l--20">
                                <label><input type="checkbox"> '.$lang->Login->remember.'</label>
                            </div>
                        </div>
                        
                            <div class="row">
                    <div class="col-6">
                      <button class="btn btn-primary px-4" type="submit">'.$lang->Login->login.'</button>
                        </div>
                    
                </form>'; 
            	?>
                  
                    </div>
                    <div class="col-6 text-end">
                      </div>
                  </div>
                </div>
              </div>
              <div class="card col-md-5 text-white bg-danger py-5" style="background-image: url(themes/CoreUI/assets/img/signin-bg.jpeg); backgound-position: center;">
                <div class="card-body text-center">
                  <div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>
    <script>
      const header = document.querySelector('header.header');

      document.addEventListener('scroll', () => {
        if (header) {
          header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
        }
      });
    </script>
    <script>
    </script>

  </body>
</html>