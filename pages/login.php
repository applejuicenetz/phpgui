<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

$template = new template();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - appleJuice phpGUI</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet"/>

    <!-- Bootstrap Core Css -->
    <link href="/themes/BsbAdmin/assets/plugins/bootstrap/dist/css/bootstrap.css" rel="stylesheet"/>

    <!-- Font Awesome Css -->
    <link href="/themes/BsbAdmin/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>

    <!-- iCheck Css -->
    <link href="/themes/BsbAdmin/assets/plugins/iCheck/skins/square/_all.css" rel="stylesheet"/>

    <!-- Custom Css -->
    <link href="/themes/BsbAdmin/assets/css/style.css" rel="stylesheet"/>
</head>
<body class="sign-in-page">
    <div class="signin-form-area">
        <h1><b>appleJuice</b> - phpGUI</h1>
        <div class="signin-top-info"><?php echo $lang->Login->headline; ?></div>
        <div class="row padding-15">
            <div class="col-sm-2 col-md-2 col-lg-4"></div>
            <div class="col-sm-8 col-md-8 col-lg-4">
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
    				<div class="form-group has-feedback">
                        <input type="url" class="form-control" placeholder="Core-URL" name="chost" id="chost" value="'.($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']).':'.$_ENV["CORE_PORT"].'" required/>
                        <span class="glyphicon glyphicon-globe form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" placeholder="'.$lang->Login->password.'" name="cpass" id="cpass"
                               required/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck m-l--20">
                                <label><input type="checkbox"> '.$lang->Login->remember.'</label>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-success btn-block btn-flat">'.$lang->Login->login.'</button>
                        </div>
                    </div>
                </form>'; 
            	?>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-4"></div>
        </div>
    </div>
    <div class="signin-right-image">
        <div class="background-layer"></div>
        <div class="copyright-info">
            <p><b>v<?php echo PHP_GUI_VERSION; ?></p>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="/themes/BsbAdmin/assets/plugins/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/themes/BsbAdmin/assets/plugins/bootstrap/dist/js/bootstrap.js"></script>

    <!-- iCheck Js -->
    <script src="/themes/BsbAdmin/assets/plugins/iCheck/icheck.js"></script>

    <!-- Jquery Validation Js -->
    <script src="/themes/BsbAdmin/assets/plugins/jquery-validation/dist/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="/themes/BsbAdmin/assets/js/pages/examples/signin.js"></script>
</body>
</html>