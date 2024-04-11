<?php
session_start();
require_once "_classes/subs.php";
require_once "_classes/core.php";
$core = new Core();
$template = new template();
$lang =& $_SESSION['language']['SETTINGS'];

//einstellungen aendern
if(!empty($_POST['change'])){
	switch($_POST['change']){
		case 'standard':
			$_POST['incdir']=urlencode($_POST['incdir']);
			$_POST['tempdir']=urlencode($_POST['tempdir']);
			$_POST['nick']=urlencode($_POST['nick']);
			$core->command("function","setsettings?Incomingdirectory="
				.$_POST['incdir']."&Temporarydirectory=".$_POST['tempdir']
				."&Port=".$_POST['c_port']."&XMLPort=".$_POST['c_xml_port']
				."&Nickname=".$_POST['nick']);
            $template->alert("success", "Gepeichert", "Einstellungen aktualliesiert!");
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
            $template->alert("success", "Gepeichert", "Verbindungseinstellungen aktualliesiert!");
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
                        <label>'.$lang["TEMPDIR"].'</label>
                        <input type="text" class="form-control" id="tempdir" name="tempdir" value="'.htmlspecialchars($settings_xml["TEMPORARYDIRECTORY"]["VALUES"]["CDATA"]).'" />
                    </div>
                    <div class="form-group">
                        <label>'.$lang["INCOMINGDIR"].'</label>
                        <input type="text" class="form-control" id="incdir" name="incdir" value="'.htmlspecialchars($settings_xml["INCOMINGDIRECTORY"]["VALUES"]["CDATA"]).'" />
                    </div>
                    <div class="form-group">
                        <label>'.$lang["PORT"].'</label>
                        <input type="number" class="form-control" id="c_port" name="c_port" value="'.$settings_xml['PORT']['VALUES']['CDATA'].'" disabled />
                    </div>
                    <div class="form-group">
                        <label>'.$lang["XML_PORT"].'</label>
                        <input type="number" class="form-control" id="c_xml_port" name="c_xml_port" value="'.$settings_xml['XMLPORT']['VALUES']['CDATA'].'" disabled />
                    </div>
                    <div class="form-group">
                        <label>'.$lang["NICK"].'</label>
                        <input type="text" class="form-control" id="nick" name="nick" value="'.htmlspecialchars($settings_xml['NICK']['VALUES']['CDATA']).'" />
                    </div>
                    <input type="hidden" name="change" value="standard" />
            		<button type="submit" class="btn btn-lg btn-success pull-right">Speichern</button>
                                    
           
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
                			<label>'.$lang["MAXCONNECTIONS"].'</label>
                			<input type="text" class="form-control" id="maxcon" name="maxcon" value="'.$settings_xml["MAXCONNECTIONS"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang["MAXUL"].'</label>
                			<input type="text" class="form-control" id="maxul" name="maxul" value="'.($settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"] / 1024).'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang["SPEEDPERSLOT"].'</label>
                			<input type="text" class="form-control" id="uls" name="uls" value="'.$settings_xml["MAXUPLOAD"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang["MAXDL"].'</label>
                			<input type="text" class="form-control" id="maxdl" name="maxdl" value="'.($settings_xml["MAXDOWNLOAD"]["VALUES"]["CDATA"] / 1024).'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang["MAXCONNECTIONSPERTURN"].'</label>
                			<input type="text" class="form-control" id="conturn" name="conturn" value="'.$settings_xml["MAXNEWCONNECTIONSPERTURN"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">
                			<label>'.$lang["MAXDLSRC"].'</label>
                			<input type="text" class="form-control" id="maxdlsrc" name="maxdlsrc" value="'.$settings_xml["MAXSOURCESPERFILE"]["VALUES"]["CDATA"].'">
                		</div>
                		<div class="form-group">';
                		
                		if($settings_xml['AUTOCONNECT']['VALUES']['CDATA']=='true') $checked = " checked=\"checked\"";

                           echo'<input type="checkbox" class="js-switch" id="aconnect" name="autoconnect" value="true" '.$checked.' />
                           Automatisch verbinden
                        </div>
                        <input type="hidden" name="change" value="connection" />
                		<button type="submit" class="btn btn-lg btn-success pull-right">Speichern</button>
                    
                </div>
            </div>
        </div>
        </form>
	</div>';

