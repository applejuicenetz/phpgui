share_ids = [];

function change(id){
	var share_zeile=document.getElementById('zeile_'+id);
	var zelle=share_zeile.firstChild;
	if(share_ids[id]==1){
		share_ids[id]=0;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=false;
	}else{
		share_ids[id]=1;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='#01c0c8';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=true;
	}
}

function changeshareprio(){
	var shareline='';
	var counter=-1;
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareline+='&sharefile['+counter+']=' + i;
	}
	window.location.href='index.php?site=sharefiles&dir=" . urlencode($_GET['dir']) . "'+ shareline + '&sprio=' + document.shareprioform.shareprio.value + '&" . SID . "';
}

function reload(){
	window.location.href='index.php?site=sharefiles&dir=" . urlencode($_GET['dir']) . "&forcereload=1&" . SID . "';
}

function exportlinks(){
	var shareexpline='';
	var counter=-1;
	
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareexpline+='&shareexpfile['+counter+']=' + i;
	}

	window.location.href='?site=sharefiles&dir=" . urlencode($_GET['dir']) . "'+ shareexpline+'&" . SID . "';
}

function selectall(){
	for(var v in share_ids){
		if(share_ids[v]==0) change(v);
	}
}
	
function selectnone(){
	for(var v in share_ids){
		if(share_ids[v]==1) change(v);
	}
}