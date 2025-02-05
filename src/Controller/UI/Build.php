<?php

namespace appleJuiceNETZ\UI;

use appleJuiceNETZ\UI\View;
use appleJuiceNETZ\UI\Router;


class Build
{
    public $view;
    public $router;

    public function __construct()
    {
        $this->view = new View();
        $this->router = new Router();
    }

    public function App():void
    {
        $this->view->Template("Header");
        
        $this->router->handle();

        $this->view->Template("Footer");
        
    }
    public function AppLoaded($page):void
    {
        
        $this->router->ContentLoaded($page);
        
    }
}	
