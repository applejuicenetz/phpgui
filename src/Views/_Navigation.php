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
          <a class="nav-link active" href="index.php?site=Dashboard">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
            </svg> Dashboard           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="index.php?site=Downloads">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
            </svg> Downloads           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="index.php?site=Uploads">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
            </svg> Uploads           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="index.php?site=Search">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-search"></use>
            </svg> Suche           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="index.php?site=Shares">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-share-alt"></use>
            </svg> geteilte Ordner           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link " href="index.php?site=Server">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg> Serverliste           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link" href="index.php?site=Settings">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
            </svg> Einstellungen           </a>
         </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
            </svg> Plugins</a>
          <ul class="nav-group-items compact">
         <li class="nav-item">
            		<a class="nav-link" href="index.php?site=extras&show=ajl/ajl.php">
            		<span class="nav-icon"><span class="nav-icon-bullet"></span></span> 
            		Link Import</a></li>
            <li class="nav-item">
            		<a class="nav-link" href="index.php?site=extras&show=phpinfo/phpinfo.php">
            		<span class="nav-icon"><span class="nav-icon-bullet"></span></span> 
            		phpinfo</a></li>
            <li class="nav-item">
            		<a class="nav-link" href="index.php?site=extras&show=sharestats/sharestats.php">
            		<span class="nav-icon"><span class="nav-icon-bullet"></span></span> 
            		Share Stats</a></li>
                     
          </ul>
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
                <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Account</div><a class="dropdown-item" href="#">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                  </svg> Updates<span class="badge badge-sm bg-info ms-2">42</span></a><a class="dropdown-item" href="#">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                  </svg> Messages<span class="badge badge-sm bg-success ms-2">42</span></a><a class="dropdown-item" href="#">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-task"></use>
                  </svg> Tasks<span class="badge badge-sm bg-danger ms-2">42</span></a><a class="dropdown-item" href="#">
                  <svg class="icon me-2">
                    <use xlink:href="<?php echo WEBUI_THEME; ?>vendors/@coreui/icons/svg/free.svg#cil-comment-square"></use>
                  </svg> Comments<span class="badge badge-sm bg-warning ms-2">42</span></a>
            </li>
          </ul>
        </div>
        <div class="container-fluid px-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0">
              <li class="breadcrumb-item"><a href="#">Home</a>
              </li>
              <li class="breadcrumb-item active"><span>Dashboard</span>
              </li>
            </ol>
          </nav>
        </div>
      </header>