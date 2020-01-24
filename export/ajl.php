<?php
/*
Diese Funktion wird für jeden link in der Exportliste ein Mal aufgerufen.
Die Ausgabe wird in das Textfeld geschrieben.
Vorhandene Variablen:
	$share_ex_link		-ajfsp link
	$share_ex_name		-Dateiname
	$share_ex_hash		-md5 Hash der Datei
	$share_ex_bytesize	-Dateigroesse in Bytes
	$share_ex_size		-Dateigroesse Formatiert
*/
echo "\r\nDu benoetigst ein appleJuice-GUI, um diese Datei zu oeffnen. "
    . "http://www.applejuicenet.de\r\n\r\n";
echo "Diese Datei darf nicht modifiziert werden!\r\n-----\r\n100\r\n";

function write_linkexport($share_ex_link, $share_ex_name, $share_ex_hash, $share_ex_bytesize, $share_ex_size)
{


    echo $share_ex_name . PHP_EOL . $share_ex_hash . PHP_EOL . $share_ex_bytesize . PHP_EOL;
}
