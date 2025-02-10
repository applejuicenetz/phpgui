<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\UI\Language;
use appleJuiceNETZ\Kernel;

$core = new Core();
$template = new template();

//Language
$language = Language::getLanguage();

//einstellungen aendern
if (!empty($_POST['change'])) {
    // Helper function to clean POST data
    function sanitize_input($data) {
        return urlencode(trim($data));
    }

    // Collect all POST data based on the form submitted
    $incomingDirectory = sanitize_input($_POST['incdir']);
    $temporaryDirectory = sanitize_input($_POST['tempdir']);
    $nickname = sanitize_input($_POST['nick']);
    $port = $_POST['c_port'];
    $xmlPort = $_POST['c_xml_port'];

    // Default values for 'connection' form
    $maxConnections = $_POST['maxcon'];
    $maxUpload = floor($_POST['maxul'] * 1024);  // KB to bytes
    $maxDownload = floor($_POST['maxdl'] * 1024);  // KB to bytes
    $speedPerSlot = $_POST['uls'];
    $newConnectionsPerTurn = $_POST['conturn'];
    $maxSourcesPerFile = $_POST['maxdlsrc'];
    $autoConnect = empty($_POST['autoconnect']) ? 'false' : $_POST['autoconnect'];

    // Handling the 'standard' form submission
    if ($_POST['change'] === 'standard') {
        $params = [
            'Incomingdirectory' => $incomingDirectory,
            'Temporarydirectory' => $temporaryDirectory,
            'Port' => $port,
            'XMLPort' => $xmlPort,
            'Nickname' => $nickname
        ];

        // Prepare the query string
        $queryString = http_build_query($params);
        $core->command("function", "setsettings?" . $queryString);
        $template->alert("success", $language->translate('Settings.get_save') . "!", $language->translate('Settings.alert_save_1'));
    }
    // Handling the 'connection' form submission
    elseif ($_POST['change'] === 'connection') {
        $params = [
            'MaxConnections' => $maxConnections,
            'MaxUpload' => $maxUpload,
            'Speedperslot' => $speedPerSlot,
            'MaxDownload' => $maxDownload,
            'MaxNewConnectionsPerTurn' => $newConnectionsPerTurn,
            'AutoConnect' => $autoConnect,
            'MaxSourcesPerFile' => $maxSourcesPerFile
        ];

        // Prepare the query string
        $queryString = http_build_query($params);
        $core->command("function", "setsettings?" . $queryString);
        $template->alert("success", $language->translate('Settings.get_save') . "!", $language->translate('Settings.alert_save_2'));
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
		<form method="post" action="?site=Settings" name="standard"  class="form-floating">
			<div class="card mb-4">
				<div class="card-header">
				' . $language->translate('Settings.head_all') . '
				</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="tempdir" name="tempdir" value="' . $tempdir . '">
						<label for="floatingInput">' . $language->translate('Settings.tempdir') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="incdir" name="incdir" value="' . $incdir . '">
						<label for="floatingInput">' . $language->translate('Settings.incomingdir') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="c_port" name="c_port" value="' . $port . '">
						<label for="floatingInput">' . $language->translate('Settings.port') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="c_xml_port" name="c_xml_port" value="' . $xml_port . '">
						<label for="floatingInput">' . $language->translate('Settings.xml_port') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="text" class="form-control" id="nick" name="nick" value="' . $nick . '">
						<label for="floatingInput">' . $language->translate('Settings.nick') . '</label>
					</div>
					
				</div>
				<div class="card-footer">
					<input type="hidden" name="change" value="standard" />
            		<button type="submit" class="btn btn-sm btn-primary pull-right">'.$language->translate('Settings.save').'</button>
                </div>
			</div>
		</div>
        </form>
        <div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">
		<form method="post" action="?site=Settings" name="connection"  class="form-floating">
			<div class="card mb-4">
				<div class="card-header">
				' . $language->translate('Settings.head_con') . '
				</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxcon" name="maxcon" value="' . $maxcon . '">
						<label for="floatingInput">' . $language->translate('Settings.max_connections') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxul" name="maxul" value="' . $maxul . '">
						<label for="floatingInput">' . $language->translate('Settings.max_ul') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="uls" name="uls" value="' . $uls . '">
						<label for="floatingInput">' . $language->translate('Settings.speed_per_slot') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxdl" name="maxdl" value="' . $maxdl . '">
						<label for="floatingInput">' . $language->translate('Settings.max_dl') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="conturn" name="conturn" value="' . $conturn . '">
						<label for="floatingInput">' . $language->translate('Setting.max_connections_per_turn') . '</label>
					</div>
					<div class="form-floating mb-3">
						<input type="number" class="form-control" id="maxdlsrc" name="maxdlsrc" value="' . $maxdlsrc . '">
						<label for="floatingInput">' . $language->translate('Settings.max_dl_src') . '</label>
					</div>
					<div class="form mb-3">
							<input type="checkbox" class="switch" id="aconnect" name="autoconnect" value="true" '.$checked.' />
							<label class="align-middle">' . $language->translate('Settings.autoconnect') . '</label>
						
					</div>
				</div>
				<div class="card-footer">
					<input type="hidden" name="change" value="connection" />
            		<button type="submit" class="btn btn-sm btn-primary pull-right">'.$language->translate('Settings.save').'</button>
                </div>
			</div>
        </form>
    </div>
</div>';

