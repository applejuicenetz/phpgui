<?php

namespace appleJuiceNETZ\UI;

use appleJuiceNETZ\UI\View;
use appleJuiceNETZ\UI\Router;
use appleJuiceNETZ\UI\Subs;


class Build
{
    public $view;
    public $router;
    public $subs;

    public function __construct()
    {
        $this->view = new View();
        $this->router = new Router();
        $this->subs = new Subs();
    }

    public function App():void
    {
        $this->view->Template("Header");
        
        $this->router->handle();

        $this->view->Template("Footer");
        
    }
    public function AppLoaded($page):void
    {
        $this->subs->refresh_cache();
        
        $this->router->ContentLoaded($page);
        
    }
}	
