<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$core = new Core();
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

//einstellungen aendern
if(!empty($_POST['change'])){
	switch($_POST['change']){
		case 'standard':
			$_POST['incdir']=urlencode($_POST['incdir']);
			$_POST['tempdir']=urlencode($_POST['tempdir']);
			$_POST['nick'] = urlencode($_POST['nick']);
            
			$core->command("function","setsettings?Incomingdirectory=".$_POST['incdir']."&Temporarydirectory=".$_POST['tempdir']
				."&Port=".$_POST['c_port']."&XMLPort=".$_POST['c_xml_port']."&Nickname=".$_POST['nick']);
            $template->alert("success", $lang->Settings->get_save."!", $lang->Settings->alert_save_1);
        break;
		case 'connection':
			if(empty($_POST['autoconnect'])) $_POST['autoconnect']='false';
			$_POST['maxul']=floor($_POST['maxul']*1024);	//von kb in bytes umrechnen
			$_POST['maxdl']=floor($_POST['maxdl']*1024);	//von kb in bytes umrechnen
			$core->command("function","setsettings?MaxConnections="
				.$_POST['maxcon']."&MaxUpload=".$_POST['maxul']
					."&Speedperslot=".$_POST['uls']."&MaxDownload="
					.$_POST['maxdl']."&MaxNewConnectionsPerTurn="
					.$_POST['conturn']."&AutoConnect=".$_POST['autoconnect']
					."&MaxSourcesPerFile=".$_POST['maxdlsrc']);
            $template->alert("success", $lang->Settings->get_save."!", $lang->Settings->alert_save_2);
            break;
	}
}

$settings_xml=$core->command("xml","settings.xml");

$_SESSION['phpaj']['core_source_port']=$settings_xml['PORT']['VALUES']['CDATA'];

//standardeinstellungen
echo'<div class="row clearfix">
		<form method="post" action="'.$_SERVER["PHP_SELF"].'?site=settings&'.SID.'\" name="standard">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="fa fa-gear"></i> Standart Einstellungen</div>
                <div class="panel-body">
                	<div class="form-group">
                        <label>'.$lang->Settings->tempdir.'</label>
                        <input type="text" class="form-control" id="tempdir" name="tempdir" value="'.htmlspecialchars($settings_xml["TEMPORARYDIRECTORY"]["VALUES"]["CDATA"]).'" />
                    </div>
                    <div class="form-group">
                        <label>'.$lang->Settings->incomingdir.'</label>
                        <input type="text" class="form-control" id="incdir" name="incdir" value="'.htmlspecialchars($settings_xml["INCOMINGDIRECTORY"]["VALUES"]["CDATA"]).'" />
                    </div>
                    <div class="form-group">
                        <label>'.$lang->Settings->port.'</label>
                        <input type="number" class="form-control" id="c_port" name="c_port" value="'.$settings_xml['PORT']['VALUES']['CDATA'].'" disabled />
                    </div>
                    <div class="form-group">
                        <label>'.$lang->Settings->xml_port.'</label>
                        <input type="number" class="form-control" id="c_xml_port" name="c_xml_port" value="'.$settings_xml['XMLPORT']['VALUES']['CDATA'].'" disabled />
                    </div>
                    <div class="form-group">
                        <label>' . $lang->Settings->nick . '</label>
                        <input type="text" class="form-control" id="nick" name="nick" value="' . htmlspecialchars($settings_xml['NICK']['VALUES']['CDATA']) . '" />
                    </div>
                    
                    <input type="hidden" name="change" value="standard" />
            		<button type="submit" class="btn btn-lg btn-success pull-right">'.$lang->Settings->save.'</button>
                                    
           
                </div>
            </div>
        </div>
        </form>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="fa fa-gear"></i> Verbindungs Einstellungen</div>
                <div class="panel-body">
                	<form method="post" action="'.$_SERVER["PHP_SELF"].'?site=settings&'.SID.'\" name="connection">
                		<div class="form-group">
                			<label>'.$lang->Settings->max_connections.'</label>
                			<input type="text" class="form-control" id="maxcon" name="maxcon" value="'.$settings_xml["MAXCONNECTIONS"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang->Settings->max_ul.'</label>
                			<input type="text" class="form-control" id="maxul" name="maxul" value="'.($settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"] / 1024).'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang->Settings->speed_per_slot.'</label>
                			<input type="text" class="form-control" id="uls" name="uls" value="'.$settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang->Settings->max_dl.'</label>
                			<input type="text" class="form-control" id="maxdl" name="maxdl" value="'.($settings_xml["MAXDOWNLOAD"]["VALUES"]["CDATA"] / 1024).'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang->Settings->max_connections_per_turn.'</label>
                			<input type="text" class="form-control" id="conturn" name="conturn" value="'.$settings_xml["MAXNEWCONNECTIONSPERTURN"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang->Settings->max_dl_src.'</label>
                			<input type="text" class="form-control" id="maxdlsrc" name="maxdlsrc" value="'.$settings_xml["MAXSOURCESPERFILE"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">';
                		
                		if($settings_xml['AUTOCONNECT']['VALUES']['CDATA']=='true') $checked = "checked";

                           echo'<input type="checkbox" class="js-switch" id="aconnect" name="autoconnect" value="true" '.$checked.' />
                           '.$lang->Settings->autoconnect.'
                        </div>
                        <input type="hidden" name="change" value="connection" />
                		<button type="submit" class="btn btn-lg btn-success pull-right">'.$lang->Settings->save.'</button>
                    
                </div>
            </div>
        </div>
        </form>
	</div>';

