<?php
use appleJuiceNETZ\UI\Language;

$language = Language::getLanguage();

?>
<div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card-group d-block d-md-flex row">
              <div class="card col-md-7 p-4 mb-0">
                <div class="card-body">
                  <h1>Login</h1>
                  <p class="text-body-secondary"><?php echo $language->translate('Login.headline'); ?></p>
                  <?php
               
				if(!empty(['core_host'])){
					
					$_SESSION['host'] ="";
          echo"HOst";
				}
				if(!empty($_SESSION['wrong_pass'])){
					
					$_SESSION['wrong_pass'] = "";
          echo"pass";
				}
				echo '
                <form name="core" action="" method="post" autocomplete="off">
    				<div class="form-floating mb-3">
                        <input type="url" class="form-control" placeholder="Core-URL" name="host" id="chost" value="'.($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']).'" required/>
                        <label for="floatingInput">Core-URL</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="cpass" id="cpass"
                               required/>
                               <label for="floatingInput">'.$language->translate('Login.password') . '</label>
                    </div>
                    <div class="row">
                        <div class="col-">
                            <div class="checkbox icheck m-l--20">
                                <label><input type="checkbox"> '.$language->translate('Login.remember') .'</label>
                            </div>
                        </div>
                        
                            <div class="row">
                    <div class="col-6">
                      <button class="btn btn-primary px-4" type="submit">'.$language->translate('Login.login').'</button>
                        </div>
                    
                </form>'; 
            	?>
                    </div>
                    <div class="col-6 text-end">
                      
                    </div>
                  </div>
                </div>
              </div>
              <div class="card col-md-5 text-white bg-primary py-5">
                <div class="card-body text-center">
                  <div>
                  
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
