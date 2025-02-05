<?php

namespace appleJuiceNETZ\Template;

use appleJuiceNETZ\Kernel;

class Data
{
    public static function header($title)
    {
        echo'    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="themes/' . GUI_THEME . '/font/iconsmind-s/css/iconsminds.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/font/simple-line-icons/css/simple-line-icons.css" />

    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/bootstrap.rtl.only.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/fullcalendar.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/datatables.responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/select2.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/perfect-scrollbar.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/glide.core.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/bootstrap-stars.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/nouislider.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/bootstrap-datepicker3.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/vendor/component-custom-switch.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/dore.dark.blueyale.min.css" />
    <link rel="stylesheet" href="themes/' . GUI_THEME . '/css/main.css" />';
    }
    
    public static function js_Scripte()
    {
        echo'<script src="themes/' . GUI_THEME . '/js/vendor/jquery-3.3.1.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/bootstrap.bundle.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/Chart.bundle.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/chartjs-plugin-datalabels.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/moment.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/fullcalendar.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/datatables.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/perfect-scrollbar.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/progressbar.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/jquery.barrating.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/select2.full.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/nouislider.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/bootstrap-datepicker.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/Sortable.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/mousetrap.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/vendor/glide.min.js"></script>
        <script src="themes/' . GUI_THEME . '/js/dore.script.js"></script>
        <script src="themes/' . GUI_THEME . '/js/scripts.js"></script>';
    }
    
}
