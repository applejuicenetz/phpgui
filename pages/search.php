<?php
session_start();
require_once "_classes/subs.php";
require_once "_classes/search.php";
$Search = new Search();
$lang =& $_SESSION['language']['SEARCH'];

if(empty($_GET['searchid'])) $_GET['searchid']="alles";
echo "<meta http-equiv=\"refresh\" content=\""
	.$_ENV['GUI_REFRESH_SEARCH']."; URL=".$_SERVER['PHP_SELF']."?site=search&searchid="
	.$_GET['searchid']."&amp;".SID."\" />";    //neu laden
echo "<script type=\"text/javascript\">
<!--
function dllink(ajfsplink){
	parent.oben.document.linkform.ajfsp_link.value=ajfsplink;
	parent.oben.document.linkform.showlinkpage.value=0;
	parent.oben.document.linkform.submit();
}

function toggleinfo(id,items){
	var infobox=document.getElementById('infobox_'+id);
	if(infobox.style.display=='block'){
		infobox.style.display='none';
	}else{
		if(infobox.firstChild==null){
			var zeilen=items.split('|');
			var linkinfo=zeilen.pop().split('/');
			infobox.appendChild(document.createTextNode('"
				.addslashes($lang['KNOWN_FILENAMES'])
				.":'));
			infobox.appendChild(document.createElement('br'));
			while(zeilen.length>0){
				var nameinfo=zeilen.shift().split('/');
				infobox.appendChild(
					document.createTextNode('['+nameinfo[0]+'x] '));
				var ajlink=document.createElement('a');
				ajlink.setAttribute('href',
					\"javascript:dllink('ajfsp://file|\"
					+nameinfo[1].replace(/\'/g,\"\\\\'\")+\"|\"
					+linkinfo.join('|')+\"/\\')\");
				ajlink.appendChild(document.createTextNode(nameinfo[1]));
				infobox.appendChild(ajlink);
				infobox.appendChild(document.createElement('br'));
			}
		}
		infobox.style.display='block';
	}
}
//-->
</script>";
echo '</head><body>';

$action_echo='';
//suchanfrage an core uebergeben
if(!empty($_POST['searchstring'])){
	$_POST['searchstring']=trim($_POST['searchstring']);
	$Search->start($_POST['searchstring']);
}

//suche abbrechen
if(!empty($_GET['cancelid'])){
	$Search->cancel($_GET['cancelid']);
}
echo'<div class="card">
            <div class="card-body">
              <h5 class="card-title">'; 
              
echo "<form action=\"".$_SERVER['PHP_SELF']."?site=search&".SID."\" method=\"post\">";
echo "<input size=\"50\" name=\"searchstring\" /> ";
echo "<input type=\"submit\" value='"
	.$lang['SEARCH']."' />";
echo "</form>\n";

echo'</h5>
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#alles" type="button" role="tab" aria-controls="home" aria-selected="true">
                  '.$lang['ALL_RESULTS'].'('.$Search->cache['SEARCHENTRY_count'].')
                  <a href="'.$_SERVER['PHP_SELF'].'?site=search&deleteall=1&'.SID.'"><span class="badge bg-danger"><i class="bi bi-x-square-fill"></i></span></a>
                  </button>
                </li>
               ';


$Search->refresh_cache();

//suche loeschen
if(!empty($_GET['deleteid'])){
	$action_echo .= $Search->delete($_GET['deleteid']);
}

//alle ergebnisse loeschen
if(!empty($_GET['deleteall'])){
	$action_echo .= $Search->delete_all();
}


$Search->process_results();

if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $b){
echo'<li class="nav-item" role="presentation">
                  <button class="nav-link" id="'.$b.'-tab" data-bs-toggle="tab" data-bs-target="#'.$b.'" type="button" role="tab" aria-controls="profile" aria-selected="false">
                  '.$Search->cache["SEARCH"][$b]["SEARCHTEXT"].'('.$Search->cache["SEARCH"][$b]["phpaj_FOUNDFILES"].')
                  
       ';
			//icon zum abbrechen/loeschen der suche
		if($Search->cache['SEARCH'][$b]['RUNNING']==="true"){
			
			echo'<a href="'.$_SERVER['PHP_SELF'].'?site=search&cancleid='.$b.'"><span class="badge bg-danger"><i class="bi bi-circle-fill"></i></span></a>
                  
                  </button>
                </li>';
				}else{
			echo'<a href="'.$_SERVER['PHP_SELF'].'?site=search&deleteid='.$b.'"><span class="badge bg-danger"><i class="bi bi-x-square-fill"></i></span></a>
                  
                  </button>
                </li>';
			}
		
	}echo'</ul>';
}
echo'  <div class="tab-content pt-2" id="myTabContent">';

if(!empty($Search->cache['SEARCH'])){
	foreach(array_keys($Search->cache['SEARCH']) as $b){
echo'<div class="tab-pane fade" id="'.$b.'-tab" role="tabpanel" aria-labelledby="'.$b.'-tab">
                  Nesciunt totam et. Consequuntur magnam aliquid eos nulla dolor iure eos quia. Accusantium distinctio omnis et atque fugiat. Itaque doloremque aliquid sint quasi quia distinctio similique. Voluptate nihil recusandae mollitia dolores. Ut laboriosam voluptatum dicta.
                </div>
                ';
	}}
echo"</div></div></div>2";