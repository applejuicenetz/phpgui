function ShowFiles(dir){
	dir=encodeURIComponent(dir);
	var sharelist=window.open('index.php?site=sharefiles&dir='+dir+'&".SID."','ajsharelist',
		'width=720,height=500,left=10,top=10,dependent=yes,scrollbars=yes');
}

function do_setsubs(name, newsub){
	name=encodeURIComponent(name);
	window.location.href='index.php?site=shares&setsubs='+name+'&newsub='+newsub;
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