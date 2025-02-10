<style type="text/css">


table {
	width: 730px;
	border: 1px solid #ccc;
	background: #fff;
	padding: 1px;
    margin-left: auto;
  margin-right: auto;
    
}

td, th {
	border: 1px solid #FFF;
	font-size: 12px;
	padding:4px 8px;
    margin: 25px auto 5px auto;
}

h1 {

	font-size: 24px;
    text-align: center;
}

h2.phpinfo {

	font-size: 22px;
	color: #0B5FB4;
	text-align: left;
	margin: 25px auto 5px auto;
	width: 724px;
    
}

hr {
	background-color: #A9A9A9;
	color: #A9A9A9;
    margin: 25px auto 5px auto;
}

.e, .v, .vr {
	color: #333;
}
.e {
	background-color: #eee;
}
.h {
	background-color: #0B5FB4;
	color: #fff;
}
.v {
	background-color: #F1F1F1;
	-ms-word-break: break-all;
	word-break: break-all;
	word-break: break-word;
	-webkit-hyphens: auto;
	-moz-hyphens: auto;
	hyphens: auto;
}
img {
	display:none;
}
</style>
  

<?php
ob_start () ;
phpinfo () ;
$pinfo = ob_get_contents () ;
ob_end_clean () ;
$pinfo = preg_replace ( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );
$pinfo = str_replace ( "module_Zend Optimizer", "module_Zend_Optimizer", $pinfo );
$pinfo = str_replace ( "<h2>", "<h2 class='phpinfo'>", $pinfo );

echo'<div class="card">
<div class="card-body">
  <h2>' . $pinfo . '</h2>

</div></div>
';
?>