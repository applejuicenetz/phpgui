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
        echo "<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($b)."';\n"
				."dl_pdl[$a]=".((($c)+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
        echo'    <div class="col-12 col-lg-12 col-xl-12 col-xxl-12 mb-1" id="zeile_' . $a . '" onclick="change(' . $a . ');">
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                            <div class=" col-6">
                              <input class="form-check-input" type="checkbox" onclick="change(' . $a . ');" id="dlcheck_' . $a . '">
                            </div>
                            ' . $status . '
                            </div>
                            <div class="fs-4 fw-semibold input-group" id="nametd_' . $a . '">
                                <a onclick="javascript:rename(' . $a . ')">' . $title . '</a>
                            </div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . $sources . ' | ' . $size . ' | PDL: ' . $pdl . ' | ' . $parts . '</div>
                            <div class="text-end mt-3">';
                            if($time != "")
                            {
                              if($time<24)
                              {
		                            
                                printf("%02d:%02d:%02d",$time,($restzeit%3600)/60,$restzeit%60);
                                
		                          }
                              else
                              {
		                            printf("%.1f Days",$time/24);
                                
		                          }
                            }
                            else
                            {
                              echo"";
                            }
        echo $restzeit. $speed . $rest . $balken . '%</div>
                            <div class="progress progress-thin mt-0 mb-0">
                            
                              <div class="progress-bar bg-' . $balken_color .'" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                      </div>';
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
        echo'    <div class="col-12 col-lg-12 col-xl-12 col-xxl-12 mb-0" id="zeile_' . $a . '">
                        <div class="card-body">
                          
                            <div class="row">
                            <div class=" col-6">
                              <input class="form-check-input" type="checkbox"  onclick="change(' . $a . ');"id="dlcheck_' . $a . '">
                            </div>
                            ' . $status . '
                            </div>
                            <div class="fs-4 fw-semibold">
                              <div class="input-group mb-3">
                                ' . $title . '
                              </div>
                            </div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . $sources . ' | ' . $size . ' | PDL: ' . $pdl . ' | ' . $parts . '</div>
                            <div class="text-end mt-3">';
                            if($time != "00:00:00")
                            {
                              if($time<24)
                              {
		                            printf("%02d:%02d:%02d",$time,($restzeit%3600)/60,$restzeit%60);
		                          }
                              else
                              {
		                            printf("%.1fd",$time/24);
		                          }
                            }
                            else
                            {
                              echo"";
                            }
        echo $time. $speed . $rest . $balken . '%</div>
                            <div class="progress progress-thin mt-0 mb-0">
                            
                              <div class="progress-bar bg-' . $balken_color .'" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div></div>';
    }
}