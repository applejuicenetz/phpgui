<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\Kernel;

$gui = new GUI();
$gui::refresh();

//Templatedaten lesen
$template= new template();
$subs = new subs();
//Core Settings auslesen
$core = new Core();
$settings_xml=$core->command("xml","settings.xml");

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

?>
<!-- Modals -->
<div class="modal fade" id="search" tabindex="-1" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLiveLabel">Search</h5>
                                <button class="btn-close" type="button" data-coreui-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                            	<form method="post" action="" name="linkform">
									<div class="mb-3">
										<label for="exampleFormControlTextarea1" class="form-label">AJ-Links</label>
										<textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="ajfsp_link" id="link"></textarea>
									</div>
                                <?php echo'
<input name="showlinkpage" type="hidden" value="1" />
<input name="'.session_name().'" type="hidden" value="'.session_id().'" />'; ?></div>
                              <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-coreui-dismiss="modal">Close</button>
                                <button class="btn btn-primary" type="submit">Dateien Downloaden</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
<div class="modal fade" id="coreexit" tabindex="-1" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header bg-danger">
                            <h4 class="modal-title" id="defaultModalLabel">Kill Core</h4>
                        </div>
                        <div class="modal-body">
                        M&ouml;chtest du wirklich den Core beenden?
                        S&auml;mtliche Punkte gehen verloren!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href ='index.php?site=kickcore'">Ja Core beenden</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">CLOSE</button>
                        </div>
                            </div>
                          </div>
                        </div>


</div>
<div class="d-block d-sm-none">
	<footer class="header fixed-bottom pt-3">
	
	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link<?php template::active("start"); ?>" href="index.php?site=start">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
            </svg>
           </a>
         </li></div>
  	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link<?php template::active("downloads"); ?>" href="index.php?site=downloads">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
            </svg>
           </a>
         </li></div>
  	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link<?php template::active("uploads"); ?> position-relativ" href="index.php?site=uploads">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
            </svg><?php template::upload_sm(); ?>
            
           </a>
         </li>
         </div>
  	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link<?php template::active("search"); ?>" href="index.php?site=search">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-search"></use>
            </svg>
           </a>
         </li>
         </div>
  	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link<?php template::active("shares"); ?>" href="index.php?site=shares">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-share-alt"></use>
            </svg>
           </a>
         </li>
         </div>
  	<div class="d-flex justify-content-between"><li class="nav-item d-flex justify-content-between">
          <a class="nav-link <?php template::active("server"); ?>" href="index.php?site=server">
            <svg class="icon icon-xl">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg>
           </a>
         </li></div>
      	
         
         
            
        </ul>
		
    </footer>
</div>
      <footer class="footer px-4">
        <div>create with <i class="col-danger fa fa-heart"></i> by <b>red171</b> & <b>kddk22</b>
                    </div>
        <div class="ms-auto"><b>v<?php echo PHP_GUI_VERSION; ?></b></div>
      </footer>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="themes/CoreUI/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="themes/CoreUI/vendors/simplebar/js/simplebar.min.js"></script>
    <!-- Plugins and scripts required by this view-->
    <script>
    </script>

  </body>
</html>

        <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <div class="modal-dialog bg-danger">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title" id="defaultModalLabel">Kill Core</h4>
                        </div>
                        <div class="modal-body">
                        M&ouml;chtest du wirklich den Core beenden?
                        S&auml;mtliche Punkte gehen verloren!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" onclick="window.location.href ='index.php?site=kickcore'">Ja Core beenden</button>
                            <button type="button" class="btn btn-link" data-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
