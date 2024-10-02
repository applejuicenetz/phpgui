<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$core = new Core();
$template = new template();
$gui = new GUI();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

//einstellungen aendern
if (!empty($_POST['change'])) {
    switch ($_POST['change']) {
        case 'standard':
            $_POST['nick'] = urlencode($_POST['nick']);
            $core->command("function", "setsettings?nick=" . $_POST['nick']);
            $template->alert("success", $lang->Settings->get_save . "!", $lang->Settings->alert_save_1);
            break;
        case 'theme':
            $data = $gui->getDeviceConfig();

            $data['GUI']['theme'] = $_POST['theme'];

            if ($gui->setDeviceConfig($data)) {
                $template->alert("success", $lang->Settings->get_save . "!", $lang->Settings->alert_save_theme);
            } else {
                echo " error"; // TODO
            }
            break;
    }
}

$settings_xml = $core->command("xml", "settings.xml");

$_SESSION['phpaj']['core_source_port'] = $settings_xml['PORT']['VALUES']['CDATA'];

//standardeinstellungen
echo '<div class="row clearfix">
		<form method="post" action="' . $_SERVER["PHP_SELF"] . '?site=user_settings&' . SID . '\" name="theme">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="fa fa-gear"></i> Darstellung</div>
                <div class="panel-body">
                	
                	<div class="form-group">
                        <label>' . $lang->Settings->theme . '</label>
                        <div class="col-sm-2">
                            <input type="radio" name="theme" id="radio_1" value="">
                            <label style="background: #009688; width:26px; height: 26px;" for="radio_1"></label>
                        </div>
                        <div class="col-sm-2">
                            <input type="radio" name="theme" id="radio_2" value="blue">
                            <label style="background: #2992A7; width:26px; height: 26px;" for="radio_1"></label>
                        </div>
                        <div class="col-sm-2">
                            <input type="radio" name="theme" id="radio_3" value="red">
                            <label style="background: #da4453; width:26px; height: 26px;" for="radio_1"></label>
                        </div>
                        <div class="col-sm-2">
                            <input type="radio" name="theme" id="radio_4" value="purple">
                            <label style="background: #a7296a; width:26px; height: 26px;" for="radio_1"></label>
                        </div>
                    </div>
                    
                    <input type="hidden" name="change" value="theme" />
            		<button type="submit" class="btn btn-lg btn-success pull-right">' . $lang->Settings->save . '</button>
                                    
           
                </div>
            </div>
        </div>
        </form>
';