var dl_ids = [];		//download ausgewaehlt?
var dl_names = [];		//download namen
var dl_pdl = [];		//momentaner pdl-wert
var dl_subdirs = [];	//unterverzeichnissnummern

var renameopen = 0;
var renamelink;

function rename(id){
	if(renameopen!=0){
		var zelle_alt=document.getElementById('nametd_'+renameopen);
		while(zelle_alt.firstChild!=null){
			zelle_alt.removeChild(zelle_alt.firstChild);
		}
		zelle_alt.appendChild(renamelink);
	}
	var zelle=document.getElementById('nametd_'+id);
	renamelink=zelle.firstChild.cloneNode(true);
	var nameinput=document.createElement('input');
		nameinput.setAttribute('id', 'newname_'+id);
		nameinput.setAttribute('value', dl_names[id]);
		nameinput.setAttribute('size', dl_names[id].length);
	zelle.replaceChild(nameinput, zelle.firstChild);
	var okbutton=document.createElement('input');
		okbutton.setAttribute('type', 'button');
		okbutton.setAttribute('value', 'OK');
		okbutton.onclick=new Function('dorename('+id+');'); //scheiss ie
	zelle.appendChild(okbutton);
	renameopen=id;
}

function dorename(id){
	var newname=encodeURIComponent(
		eval('document.dl_form.newname_'+id+'.value'));
	window.location.href='/index.php?site=downloads&action=renamedownload&dl_id[0]='+
		id+'&action_value=' + newname + '&';
}

function dlparts(id){
	var ajpartinfo=window.open('/index.php?site=dl_parts&dl_id='+id+'','ajdlparts',
		'width=540,height=300,left=10,top=10,dependent=yes,scrollbars=no');
	ajpartinfo.focus();
}

function dlusers(id){
	var ajdlinfo=window.open('index.php?site=dl_users&dl_id='+id+'','ajdlinfo',
		'width=1000,height=600,left=10,top=10,dependent=yes,scrollbars=yes');
	ajdlinfo.focus();
}

function inc_pdl(){
	if(document.dl_form.pdl.value==1){
		document.dl_form.pdl.value='2.2';
	}else if(document.dl_form.pdl.value<=49.9
			&& document.dl_form.pdl.value>1){
			var neuer_pdlwert=(document.dl_form.pdl.value*1)+0.1;
			document.dl_form.pdl.value=neuer_pdlwert.toFixed(1);
	}else{
			document.dl_form.pdl.value='1.0';
	}
}

function dec_pdl(){
	if(document.dl_form.pdl.value<2.3){
		document.dl_form.pdl.value='1.0';
	}else if(document.dl_form.pdl.value>50){
			document.dl_form.pdl.value='50.0';
	}else{
			var neuer_pdlwert=(document.dl_form.pdl.value*1)-0.1;
			document.dl_form.pdl.value=neuer_pdlwert.toFixed(1);
	}
}

function change(id){
	var dl_zeile=document.getElementById('zeile_'+id);
	var zelle=dl_zeile.firstChild;
	if(dl_ids[id]==1){
		dl_ids[id]=0;
		document.dl_form.pdl.value='1.0';
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='';
			zelle=zelle.nextSibling;
		}
		document.getElementById('dlcheck_'+id).checked=false;
	}else{
		dl_ids[id]=1;
		document.dl_form.pdl.value=dl_pdl[id];
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='#CEE3F6';
			zelle=zelle.nextSibling;
		}
		document.getElementById('dlcheck_'+id).checked=true;
	}
}

function dlaction(action){
	var dlline='?site=downloads&action='+action;
	var counter=-1;
	var fragetext="cancel?\n";
	for (var v in dl_ids){
		if(dl_ids[v]==0) continue;
		counter++;
		dlline+='&dl_id['+counter+']=' + v;
		fragetext+="\n"+dl_names[v];
	}
	if(action=='settargetdir'){
		var newname=prompt('targetdir:','');
		if(newname==null) return;
		dlline+='&action_value='+encodeURIComponent(newname);
	}
	if(action=='setpowerdownload')
		dlline+='&action_value='+document.dl_form.pdl.value;
	if(action=='canceldownload' && !confirm(fragetext))
		return;
	window.location.href='' + dlline+'&';
}

function select_all(moep){
	for(var v in dl_ids){
		if(dl_ids[v]==moep) change(v);
	}
}

function select_sub(subid, moep){
	for(var v in dl_ids){
		if(dl_subdirs[v]==subid && dl_ids[v]==moep) change(v);
	}
}

function togglesubdir(dircounter){
	var bild=document.getElementById('img_'+dircounter);
	var zeilen=new Array();
	for (var v in dl_subdirs){
		if(dl_subdirs[v] != dircounter) continue;
		var dl_zeile=document.getElementById('zeile_'+v);
		zeilen.push(dl_zeile);
	}
	var z=zeilen.shift();
	if(z.style.display != 'none'){
		while(z!=null){
			z.style.display='none';
			z=zeilen.shift();}
		bild.setAttribute('src','');
	}else{
		while(z!=null){
			z.style.display='';
			z=zeilen.shift();}
		bild.setAttribute('src','');
	}
}

