<?php

namespace appleJuiceNETZ\Template;

use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\Kernel;

class Downloadpage
{
    static function list_once($status, $title, $balken, $sources, $size, $parts, $rest, $speed, $time, $restzeit, $a, $b, $c, $subdircounter, $pdl)
    {
        if($balken != "100")
        {
            $balken_color = "info";
        }else{
            $balken_color = "success";
        }
     
        echo'    
        <tr class="align-middle"  id="zeile_' . $a . '"  onclick="change(' . $a . ');">
                          <td>
                        	<input class="form-check-input" type="checkbox" id="dlcheck_' . $a . '">
                          </td>
                          <td>
                            <div class="text-nowrap" id="nametd_' . $a . '>
                            	<a onclick="javascript:rename(' . $a . ')" title="Umbenennen">
                            		' . $title . '
                            	</a>
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span><a onclick="location.href=\'\'" title="Mehr Info">
					                    ' . $sources . '</a></span> | ' . $size . ' | ' . $parts . '</div>
                          </td>
                          <td class="text-center">
                            ' . $status . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">';
                            if($time != "")
                            {
                              $time = (int)floor($restzeit / 3600); // Stunden
                              $minutes = (int)floor(($restzeit % 3600) / 60); // Minuten
                              $seconds = (int)floor($restzeit % 60); // Sekunden
                              if($time<24)
                              {
                                printf("%02d:%02d:%02d", $time, $minutes, $seconds);
                                echo" - ";
                              }
                              else
                              {
		                            printf("%.1f Tage ",$time/24);
                                echo" - ";
                                
		                          }
                            }
                            else
                            {
                              echo"";
                            }
                            echo $speed . $rest . '</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-' . $balken_color .'" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </td>
                          <td class="text-center">
                            ' . $pdl . '
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="icon">
                                  <use xlink:href="' . WEBUI_THEME . 'vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                                </svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Info</a><a class="dropdown-item" href="#">Edit</a><a class="dropdown-item text-danger" href="#">Delete</a></div>
                            </div>
                          </td>
                        </tr>';
    }
    static function list_group($status, $title, $balken, $sources, $size, $parts, $rest, $speed, $time, $restzeit, $a, $b, $c, $subdircounter, $pdl)
    {
        if($balken != "100")
        {
            $balken_color = "info";
        }else{
            $balken_color = "success";
        }
        echo "<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($b)."';\n"
				."dl_pdl[$a]=".((($c)+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
        echo'    
        <tr class="align-middle"  id="zeile_' . $a . '" onclick="change(' . $a . ');">
                          <td>
                        	<input class="form-check-input refreshCheckbox" type="checkbox" onclick="change(' . $a . ');" id="dlcheck_' . $a . '">
                          </td>
                          <td>
                            <div class="text-nowrap" id="nametd_' . $a . '>
                            	<a onclick="javascript:rename(' . $a . ')" title="Umbenennen">
                            		' . $title . '
                            	</a>
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span><a onclick="location.href=\'\'" title="Mehr Info">
					                    ' . $sources . '</a></span> | ' . $size . ' | ' . $parts . '</div>
                          </td>
                          <td class="text-center">
                            ' . $status . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">';
                            if($time != "")
                            {
                              $time = (int)($restzeit / 3600); // Stunden
                              $minutes = (int)(($restzeit % 3600) / 60); // Minuten
                              $seconds = (int)($restzeit % 60); // Sekunden
                              if($time<24)
                              {
                                printf("%02d:%02d:%02d", $time, $minutes, $seconds);
                                echo" - ";
                              }
                              else
                              {
		                            printf("%.1f Tage ",$time/24);
                                echo" - ";
                                
		                          }
                            }
                            else
                            {
                              echo"";
                            }
                            echo $speed . $rest . '</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-' . $balken_color .'" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </td>
                          <td class="text-center">
                            ' . $pdl . '
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="icon">
                                  <use xlink:href="' . WEBUI_THEME . 'vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                                </svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Info</a><a class="dropdown-item" href="#">Edit</a><a class="dropdown-item text-danger" href="#">Delete</a></div>
                            </div>
                          </td>
                        </tr>';
    }
}