<?php
class template{
	function bread($a){
		if(empty($a)){echo "";
		}else{
			$sub = substr($a, 0, strpos($a, '/'));
    		return '<li class="breadcrumb-item active">'.ucfirst($sub).'</li>';
		}
	}
	function bread2($a){
        $tab = ucfirst($a);
        foreach($_GET as $key => $value){
         	$sub = substr($_GET["$key"], 0, strpos($_GET["$key"], '/'));
            return '<li class="breadcrumb-item active">'.ucfirst($sub).'</li>';
        }
	}
	function alert($alert, $strong, $text){
		// $alert = success, warning, danger, info
		echo' <div class="alert alert-'.$alert.'" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <i class="fa fa-fw fa-check-circle"></i>
                                    <strong>'.$strong.'</strong> '.$text.'
                                </div>
                               ';
	}

}