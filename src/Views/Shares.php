<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\UI\Language;

$core = new Core();
$share = new Share();
$template = new template();

//Language
$language = Language::getLanguage();

//einstellungen fuer unterverzeichnis aendern
if(!empty($_GET['setsubs'])){
	$share->changesub($_GET['setsubs'], $_GET['newsub']);
	$template->alert("info", $language->translate('Share.inprogress'), $language->translate('Share.set_share'));
	echo'';
}

//verzeichnis aus share nehmen
if(!empty($_GET['share_del'])){
	$share->del_share($_GET['share_del']);
}

//verzeichnis sharen
if(!empty($_GET['new_share'])){
	$share->add_share($_GET['new_share'], $_GET['new_subs']);
	$template->alert("info", $language->translate('Share.inprogress'), $language->translate('Share.newshare'));
	
}

echo "<form action=\"\" name=\"mainform\">";
echo'<div class="row clearfix">
                    <div class="col-sm-12 mb-4">
                        <div class="card">
                        	<div class="card-body">
                        	<div class="mb-2">
                        	<a href="?site=Shares&setsubs=*sharecheck&newsub=0" class="btn btn-primary"><i class="fa fa-refresh"></i> ' . $language->translate('Share.check') . '</a></div>
                            	<div class="table-responsive">
									<table class="table table-striped">
										<thead>
                							<tr>
                    							<th></th>
                								<th width="70%">'.$language->translate('Share.directory_name').'</th>
                    							<th nowrap>'.$language->translate('Share.subs').'</th>
                    							<th width="10">'.$language->translate('Share.aktion').'</th>
                    						</tr>
            							</thead>
                						<tbody>';
//auch temp-verzeichnis anzeigen (für dateien die gerade geladen werden)
echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td colspan="4">
			<a href="index.php?site=Sharefiles&dir='.addslashes(htmlspecialchars($share->get_temp())).'" aria-current="true">
            '.htmlspecialchars($share->get_temp()).'
            </a>
        </td>
    </tr>';
$sharedirs=$share->get_shared_dirs(1);

//freigegebene verzeichnisse anzeigen
foreach($sharedirs as $a){
	$cur_share=$share->get_shared_dir($a);
	//verzeichnisname -> link zu den einzelnen dateien
	$checked = "onclick=\"location.href='?site=shares&setsubs=" . htmlspecialchars($cur_share['NAME']) . "&newsub=" . (($cur_share['SHAREMODE'] == 'subdirectory') ? "0" : "1") . "'\"";
	echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td>
			<a href="index.php?site=sharefiles&dir='.addslashes(htmlspecialchars($cur_share["NAME"])).'" aria-current="true">
            '.htmlspecialchars($cur_share["NAME"]).'
            </a>
        </td>
        <td><input type="checkbox" '.$checked.' value="1" '.$share->sharemode[$cur_share['SHAREMODE']].'/>
                                </td>
        <td><a href="javascript:delshare(\''.addslashes(htmlspecialchars($cur_share['NAME'])).'\');" class="btn btn-sm btn-danger"><i class="fa fa-trash class="text-danger"></a></td>
    </tr>';
}
echo'<tr>
		<td colspan="4">' . $language->translate('Share.shared_directories_new') . '</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="text" name="new_share" class="form-control input-sm" placeholder="' . $language->translate('Share.way') . '"/></td>
		<td><input type="checkbox" name="new_subs" value="1" checked/></td>
		<td><button type="button" onclick="newshare()" class="btn btn-primary btn-sm pull-middle"><i class="fa fa-save"></i></button></td>
	</tr>';
echo'
    </tbody>
    </table>
    </div></div>
          </div>
          ';

//echo "<br />\n";
//echo "<div align=\"center\"><table><tr>\n";
//echo "<td><input type=\"button\" onclick=\"do_setsubs('*sharecheck',0);\" value=\""
//	.$language->translate('Share->check."\" /></td>";
//echo "<td><input type=\"button\" onclick=\"share_export()\" value=\""
//	.$language->translate('Share->exportlist."\" /></td>\n";
echo "</tr></table></div>\n";
echo "</form>\n";
