<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$core = new Core();
$share = new Share();
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

echo "<script>
function ShowFiles(dir){
	dir=encodeURIComponent(dir);
	var sharelist=window.open('index.php?site=sharefiles&dir='+dir+'&".SID."','ajsharelist',
		'width=720,height=500,left=10,top=10,dependent=yes,scrollbars=yes');
}

function do_setsubs(name, newsub){
	name=encodeURIComponent(name);
	window.location.href='?site=shares&setsubs='+name+'&newsub='+newsub;
}

function delshare(name){
	name=encodeURIComponent(name);
	window.location.href='?site=shares&".SID."&share_del='+name;
}

function newshare(){
	var name=encodeURIComponent(document.mainform.new_share.value);
	var subs=document.mainform.new_subs.checked ? 1 : 0;
	window.location.href='".$_SERVER['PHP_SELF']."?site=shares&new_share='+name+'&new_subs='+subs;
}

function share_export(){
    window.location.href = 'index.php?site=shareexport';
}

function select_dir(){
	var dirlist=window.open(
		'?site=directory&returninput=mainform.new_share.value&amp;".SID."',
		'Dirlist','width=400,height=350,left=10,top=10,dependent=yes,scrollbars=no');
	dirlist.focus();
}
</script>";

//einstellungen fuer unterverzeichnis aendern
if(!empty($_GET['setsubs'])){
	$share->changesub($_GET['setsubs'], $_GET['newsub']);
	$template->alert("info", $lang->Share->in_progress, $lang->Share->set_share);
}

//verzeichnis aus share nehmen
if(!empty($_GET['share_del'])){
	$share->del_share($_GET['share_del']);
}

//verzeichnis sharen
if(!empty($_GET['new_share'])){
	$share->add_share($_GET['new_share'], $_GET['new_subs']);
	$template->alert("info", $lang->Share->in_progress, $lang->Share->new_share);
}

echo "<form action=\"\" name=\"mainform\">";
echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                        	<div class="panel-heading bg-success"><i class="fa fa-folder"></i> '.$lang->Share->shared_directories.'</div>
                            <div class="panel-body">
                            	<div class="table-responsive">
									<table class="table table-striped">
										<thead>
                							<tr>
                    							<th></th>
                								<th width="70%">'.$lang->Share->directory_name.'</th>
                    							<th>'.$lang->Share->subs.'</th>
                    							<th width="10">'.$lang->Share->aktion.'</th>
                    						</tr>
            							</thead>
                						<tbody>';
//auch temp-verzeichnis anzeigen (für dateien die gerade geladen werden)
echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td colspan="4">
			<a href="index.php?site=sharefiles&dir='.addslashes(htmlspecialchars($share->get_temp())).'" aria-current="true">
            '.htmlspecialchars($share->get_temp()).'
            </a>
        </td>
    </tr>';
$sharedirs=$share->get_shared_dirs(1);

//freigegebene verzeichnisse anzeigen
foreach($sharedirs as $a){
	$cur_share=$share->get_shared_dir($a);
	//verzeichnisname -> link zu den einzelnen dateien
	$checked = "onclick=\"do_setsubs('".addslashes(htmlspecialchars($cur_share['NAME']))."','".(($cur_share['SHAREMODE'] == 'subdirectory') ? "0" : "1")."');\"";
	echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td>
			<a href="index.php?site=sharefiles&dir='.addslashes(htmlspecialchars($cur_share["NAME"])).'" aria-current="true">
            '.htmlspecialchars($cur_share["NAME"]).'
            </a>
        </td>
        <td><input type="checkbox" '.$checked.' value="1"
		'.$share->sharemode[$cur_share['SHAREMODE']].'/>
                                </td>
        <td><a href="javascript:delshare(\''.addslashes(htmlspecialchars($cur_share['NAME'])).'\');"><i class="fa fa-trash class="text-danger"></a></td>
    </tr>';
}
echo'
    </tbody>
    </table>
    </div></div>
          </div>
          <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                        	<div class="panel-heading bg-success"><i class="fa fa-folder"></i> '.$lang->Share->shared_directories_new.'</div>
                            <div class="panel-body">
                            	<div class="form-group">
                                        <label>'.$lang->Share->way.'</label>
                                        <input type="text" name="new_share" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                     <input type="checkbox" name="new_subs" value="1" checked/>
                                        <label for="remember_me">'.$lang->Share->with_subs.'</label>
                                       
                                    
                                    </div>
                                    <div class="form-group clearfix">
                                        <input type="button" onclick="newshare()" class="btn btn-sm btn-success pull-right">
                                    </div>
                                
							</div>
						</div>
					</div>
				</div>';
//Neues verzeichnis freigeben
//echo "\n<tr><td>NEW: <input name=\"new_share\" size=\"60\" />";
//echo " <input type=\"button\" value=\"...\" onclick=\"select_dir();\" /></td>";
//echo "<td><input type=\"checkbox\" name=\"new_subs\" value=\"1\" "
//	."checked=\"checked\" /></td><td><input type=\"button\" value=\"ADSS\" "
//	."onclick=\"newshare()\"/></td></tr>\n";
//echo "</table>";
//
//echo "<br />\n";
//echo "<div align=\"center\"><table><tr>\n";
//echo "<td><input type=\"button\" onclick=\"do_setsubs('*sharecheck',0);\" value=\""
//	.$lang->Share->check."\" /></td>";
//echo "<td><input type=\"button\" onclick=\"share_export()\" value=\""
//	.$lang->Share->exportlist."\" /></td>\n";
echo "</tr></table></div>\n";
echo "</form>\n";
