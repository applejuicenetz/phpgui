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

//Variablen kuerzen
$tempdir = htmlspecialchars($settings_xml["TEMPORARYDIRECTORY"]["VALUES"]["CDATA"]);
$incdir = htmlspecialchars($settings_xml["INCOMINGDIRECTORY"]["VALUES"]["CDATA"]);
$port = $settings_xml['PORT']['VALUES']['CDATA'];
$xml_port = $settings_xml['XMLPORT']['VALUES']['CDATA'];
$nick = htmlspecialchars($settings_xml["NICK"]["VALUES"]["CDATA"]);

$maxcon = $settings_xml["MAXCONNECTIONS"]["VALUES"]["CDATA"];
$maxul = $settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"] / 1024;
$uls = $settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"];
$maxdl = $settings_xml["MAXDOWNLOAD"]["VALUES"]["CDATA"] / 1024;
$conturn = $settings_xml["MAXNEWCONNECTIONSPERTURN"]["VALUES"]["CDATA"];
$maxdlsrc = $settings_xml["MAXSOURCESPERFILE"]["VALUES"]["CDATA"];

if($settings_xml['AUTOCONNECT']['VALUES']['CDATA']=='true') $checked = "checked";

//standardeinstellungen
echo'<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">
		<form method="post" action="'.$_SERVER["PHP_SELF"].'?site=settings&'.SID.'\" name="standard"  class="form-floating">
			<div class="card mb-4">
				<div class="card-header">
				' . $lang->Settings->head_all . '
				</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="tempdir" name="tempdir" value="' . $tempdir . '">
						<label for="floatingInput">' . $lang->Settings->tempdir . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="incdir" name="incdir" value="' . $incdir . '">
						<label for="floatingInput">' . $lang->Settings->incomingdir . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="c_port" name="c_port" value="' . $port . '">
						<label for="floatingInput">' . $lang->Settings->port . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="c_xml_port" name="c_xml_port" value="' . $xml_port . '">
						<label for="floatingInput">' . $lang->Settings->xml_port . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="nick" name="nick" value="' . $nick . '">
						<label for="floatingInput">' . $lang->Settings->nick . '</label>
					</div>
					
				</div>
				<div class="card-footer">
					<input type="hidden" name="change" value="standard" />
            		<button type="submit" class="btn btn-sm btn-primary pull-right">'.$lang->Settings->save.'</button>
                </div>
			</div>
		</div>
        </form>
        <div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">
		<form method="post" action="'.$_SERVER["PHP_SELF"].'?site=settings&'.SID.'\" name="connection"  class="form-floating">
			<div class="card mb-4">
				<div class="card-header">
				' . $lang->Settings->head_con . '
				</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxcon" name="maxcon" value="' . $maxcon . '">
						<label for="floatingInput">' . $lang->Settings->max_connections . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxul" name="maxul" value="' . $maxul . '">
						<label for="floatingInput">' . $lang->Settings->max_ul . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="uls" name="uls" value="' . $uls . '">
						<label for="floatingInput">' . $lang->Settings->speed_per_slot . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxdl" name="maxdl" value="' . $maxdl . '">
						<label for="floatingInput">' . $lang->Settings->max_dl . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="conturn" name="conturn" value="' . $conturn . '">
						<label for="floatingInput">' . $lang->Settings->max_connections_per_turn . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxdlsrc" name="maxdlsrc" value="' . $maxdlsrc . '">
						<label for="floatingInput">' . $lang->Settings->max_dl_src . '</label>
					</div>
					<div class="form mb-3">
							<input type="checkbox" class="switch" id="aconnect" name="autoconnect" value="true" '.$checked.' />
							<label class="align-middle">' . $lang->Settings->autoconnect . '</label>
						
					</div>
				</div>
				<div class="card-footer">
					<input type="hidden" name="change" value="connection" />
            		<button type="submit" class="btn btn-sm btn-primary pull-right">'.$lang->Settings->save.'</button>
                </div>
			</div>
        </form>
    </div>
</div>';

