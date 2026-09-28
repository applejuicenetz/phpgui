function showAjToast(msg) {
    var t = document.getElementById('aj-js-toast');
    if (!t) {
        var wrap = document.createElement('div');
        wrap.id = 'aj-js-toast-wrap';
        wrap.style.cssText = 'position:fixed;top:120px;right:5px;z-index:300;opacity:0.9;';
        wrap.innerHTML = '<div id="aj-js-toast" class="toast align-items-center text-white bg-info border-0 fade" role="alert" aria-atomic="true">'
            + '<div class="d-flex"><div id="aj-js-toast-body" class="toast-body"></div>'
            + '<button class="btn-close btn-close-white me-2 m-auto" type="button" onclick="this.closest(\'#aj-js-toast-wrap\').style.display=\'none\'"></button>'
            + '</div></div>';
        document.body.appendChild(wrap);
        t = document.getElementById('aj-js-toast');
    }
    clearTimeout(t._hideTimer);
    document.getElementById('aj-js-toast-body').textContent = msg;
    t.parentElement.style.display = '';
    t.classList.add('show');
    t._hideTimer = setTimeout(function() {
        t.classList.remove('show');
        setTimeout(function() { t.parentElement.style.display = 'none'; }, 300);
    }, 2000);
}

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
		dl_zeile.classList.remove('dl-selected');
		document.getElementById('dlcheck_'+id).checked=false;
	}else{
		dl_ids[id]=1;
		document.dl_form.pdl.value=dl_pdl[id];
		dl_zeile.classList.add('dl-selected');
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

var DL_FILTER_KEY = 'aj_dl_name_filter';

function updateDlFilterIcon() {
    var box = document.getElementById('dl_filter_box');
    if (!box) return;
    var link = box.previousElementSibling;
    var input = document.getElementById('dl_filter');
    var active = input && input.value.trim() !== '';
    if (link) link.style.color = active ? 'var(--cui-warning, #f9b115)' : '';
}

function toggleDlFilter() {
	var box = document.getElementById('dl_filter_box');
	var input = document.getElementById('dl_filter');
	if (box.style.display === 'none') {
		box.style.display = '';
		input.focus();
	} else {
		box.style.display = 'none';
		input.value = '';
		localStorage.removeItem(DL_FILTER_KEY);
		filterDownloads('');
	}
}

function filterDownloads(val) {
	var filter = val.toLowerCase();
	if (filter !== '') {
		localStorage.setItem(DL_FILTER_KEY, val);
	} else {
		localStorage.removeItem(DL_FILTER_KEY);
	}
	for (var v in dl_names) {
		var row = document.getElementById('zeile_' + v);
		if (!row) continue;
		if (filter === '' || dl_names[v].toLowerCase().indexOf(filter) !== -1) {
			row.style.display = '';
		} else {
			row.style.display = 'none';
		}
	}
	updateDlFilterIcon();
}

function dlSelectAll(cb) {
	var checked = cb.checked ? 0 : 1;
	for (var v in dl_ids) {
		if (dl_ids[v] == checked) change(v);
	}
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

function formatBytes(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(1) + ' GB';
    if (bytes >= 1048576)    return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024)       return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
}

// Download speed limit: unit toggle
var dl_unit = 'kb';

function parseDlVal(str) {
    return parseFloat(String(str).replace(',', '.')) || 0;
}

function setDlUnit(unit) {
    var input = document.getElementById('maxdl');
    var val = parseDlVal(input.value);
    if (dl_unit === unit) return;
    if (unit === 'mb') {
        input.value = (val / 1024).toFixed(1);
    } else {
        input.value = Math.round(val * 1024);
    }
    dl_unit = unit;
    document.getElementById('maxdl_kb').classList.toggle('active', unit === 'kb');
    document.getElementById('maxdl_mb').classList.toggle('active', unit === 'mb');
    localStorage.setItem('dl_speed_unit', unit);
}

function applyMaxDl() {
    var input = document.getElementById('maxdl');
    var val = parseDlVal(input.value);
    var kb = (dl_unit === 'mb') ? Math.round(val * 1024) : Math.round(val);
    var bytes = kb * 1024;
    input.value = (dl_unit === 'mb') ? (kb / 1024).toFixed(1) : kb.toFixed(1);
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?site=api&action=set_maxdl&value=' + bytes);
    input.blur();
    xhr.onload = function() {
        if (xhr.status === 200) {
            aj_max_dl_bytes = bytes;
            var bar = document.getElementById('aj-dl-speed-bar');
            if (bar) { bar._maxRaw = bytes; bar._maxFmt = bytes > 0 ? formatBytes(bytes) : ''; }
            updateSpeedBar(null);
            showAjToast('Gespeichert!');
        }
    };
    xhr.send();
}

function updateSpeedBar(speedInfo) {
    var bar = document.getElementById('aj-dl-speed-bar');
    if (!bar) return;
    if (speedInfo !== null) {
        bar._speedRaw = speedInfo.current_speed_raw;
        bar._speedFmt = speedInfo.current_speed_formatted;
        bar._maxRaw   = speedInfo.max_speed_raw;
        bar._maxFmt   = speedInfo.max_speed_formatted;
        if (typeof aj_max_dl_bytes !== 'undefined') aj_max_dl_bytes = speedInfo.max_speed_raw;
    }
    var speedRaw = bar._speedRaw || 0;
    var speedFmt = bar._speedFmt || '0 B';
    var maxRaw   = bar._maxRaw !== undefined ? bar._maxRaw : (typeof aj_max_dl_bytes !== 'undefined' ? aj_max_dl_bytes : 0);
    var maxFmt   = bar._maxFmt || '';
    var pct = 0;
    var label;
    if (maxRaw > 0) {
        pct = Math.min(100, Math.round((speedRaw / maxRaw) * 100 * 10) / 10);
        label = speedFmt + '/s / ' + maxFmt + '/s';
    } else {
        label = speedFmt + '/s / \u221E';
    }
    bar.style.width = pct + '%';
    bar.textContent = label;
    bar.parentElement.title = label;
    bar.className = 'progress-bar bg-info';
}

// Restore saved unit preference and name filter
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('dl_speed_unit');
    if (saved === 'mb') setDlUnit('mb');

    var savedFilter = localStorage.getItem(DL_FILTER_KEY);
    if (savedFilter) {
        var input = document.getElementById('dl_filter');
        var box = document.getElementById('dl_filter_box');
        if (input && box) {
            input.value = savedFilter;
            box.style.display = '';
            filterDownloads(savedFilter);
        }
    }
});

// Live update: downloads
AjPolling.register('downloads', function(data) {
    var items = data.items || data;
    var totalSpeedRaw = 0;
    for (var id in items) {
        var row = document.getElementById('zeile_' + id);
        if (!row) continue;
        var dl = items[id];
        totalSpeedRaw += dl.speed_raw || 0;
        var el;
        el = row.querySelector('[data-aj="status"]');
        if (el) el.innerHTML = dl.status;
        el = row.querySelector('[data-aj="done-pct"]');
        if (el) el.textContent = dl.done_percent + '%';
        el = row.querySelector('[data-aj="rest"]');
        if (el) el.textContent = dl.rest + '- ' + dl.eta;
        el = row.querySelector('[data-aj="progress-bar"]');
        if (el) el.style.width = dl.done_percent + '%';
        el = row.querySelector('[data-aj="speed"]');
        if (el) el.textContent = dl.speed;
        el = row.querySelector('[data-aj="pdl"]');
        if (el) el.textContent = dl.pdl;
        el = row.querySelector('[data-aj="sources"]');
        if (el) {
            var link = el.querySelector('a');
            if (link) link.textContent = (dl.sources_queue + dl.sources_active) + '/' + dl.sources_total;
        }
    }
    // Update speed bar from summed individual speeds (always in sync)
    if (data.max_speed_raw !== undefined) {
        var bar = document.getElementById('aj-dl-speed-bar');
        if (bar) {
            bar._maxRaw = data.max_speed_raw;
            bar._maxFmt = data.max_speed_formatted;
            aj_max_dl_bytes = data.max_speed_raw;
        }
    }
    var bar2 = document.getElementById('aj-dl-speed-bar');
    if (bar2) {
        bar2._speedRaw = totalSpeedRaw;
        bar2._speedFmt = formatBytes(totalSpeedRaw);
    }
    updateSpeedBar(null);
});
document.addEventListener('DOMContentLoaded', function() {
    AjPolling.start(['header', 'downloads']);
});

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

