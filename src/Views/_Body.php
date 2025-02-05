<?php

use appleJuiceNETZ\UI\View;

// Überprüfe, ob der 'site' Parameter leer ist oder nicht existiert
$site = isset($_GET['site']) && !empty($_GET['site']) ? $_GET['site'] : 'Dashboard';


View::Template("Header");
View::Template("Navigation");
View::Template("ContentLoader");
View::Template("Footer");