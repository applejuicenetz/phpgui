<?php

use appleJuiceNETZ\GUI\template;

?>
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
      <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
          <svg class="sidebar-brand-full" width="88" height="32" alt="CoreUI Logo">
            <use xlink:href="<?php echo WEBUI_THEME; ?>assets/brand/coreui.svg#full"></use>
          </svg>
          <svg class="sidebar-brand-narrow" width="32" height="32" alt="CoreUI Logo">
            <use xlink:href="<?php echo WEBUI_THEME; ?>assets/brand/coreui.svg#signet"></use>
          </svg>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
      </div>
      <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
          <a class="nav-link <?php template::active("Dashboard"); ?>" href="index.php?site=Dashboard">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
            </svg> Dashboard           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Downloads"); ?>" href="index.php?site=Downloads">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
            </svg> Downloads           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Uploads"); ?>" href="index.php?site=Uploads">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
            </svg> Uploads <span class="badge badge-sm bg-info ms-auto" style="display: none;" id="newUploadBadge">0</span></a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Search"); ?>" href="index.php?site=Search">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-search"></use>
            </svg> Suche           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Shares"); ?>" href="index.php?site=Shares">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-share-alt"></use>
            </svg> geteilte Ordner           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Server"); ?>" href="index.php?site=Server">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg> Serverliste           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Settings"); ?>" href="index.php?site=Settings">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
            </svg> Einstellungen           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("Settings"); ?>" href="index.php?site=Extras">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
            </svg> Plugins</a>
         </li>
        
      <div class="sidebar-footer border-top d-none d-md-flex">
      </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
      <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
          <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
            <svg class="icon icon-lg">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
          </button>
          <ul class="header-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#">
                <svg class="icon icon-lg">
                  <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                </svg></a></li>
            <li class="nav-item"><a class="nav-link" href="#">
                <svg class="icon icon-lg">
                  <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-list-rich"></use>
                </svg></a></li>
            <li class="nav-item"><a class="nav-link" href="#">
                <svg class="icon icon-lg">
                  <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                </svg></a></li>
          </ul>
          <ul class="header-nav">
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown">
              <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
                <svg class="icon icon-lg theme-icon-active">
                  <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                </svg>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-sun"></use>
                    </svg>Light
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-moon"></use>
                    </svg>Dark
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                    </svg>Auto
                  </button>
                </li>
              </ul>
            </li>
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-md"><img class="avatar-img" src="<?php echo WEBUI_THEME; ?>assets/img/avatars/8.jpg" alt="user@email.com"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-end pt-0">
                <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Core beenden</div><a class="dropdown-item" href="?serv=Kick-Core">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                  </svg> Updates<span class="badge badge-sm bg-info ms-2">42</span></a><a class="dropdown-item" href="?serv=Logout">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#log_out"></use>
                  </svg> Logout</span></a>
            </li>
          </ul>
        </div>
        <div class="container-fluid px-4">
          <?php template::bread($_GET['site'],$_GET['site']);  ?>
        </div>
      </header>