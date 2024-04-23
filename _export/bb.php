<?php
/*
Diese Funktion wird f�r jeden link in der Exportliste ein Mal aufgerufen.
Die Ausgabe wird in das Textfeld geschrieben.
Vorhandene Variablen:
	$share_ex_link		-ajfsp link
	$share_ex_name		-Dateiname
	$share_ex_hash		-md5 Hash der Datei
	$share_ex_bytesize	-Dateigroesse in Bytes
	$share_ex_size		-Dateigroesse Formatiert
*/
function write_linkexport($share_ex_link,$share_ex_name,$share_ex_hash,
		$share_ex_bytesize,$share_ex_size){
	echo "[URL=\"".$share_ex_link."\"]".$share_ex_name." ("
		.$share_ex_size.")[/URL]\n";
}
