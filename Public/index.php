<?php
chdir(dirname(__DIR__));
require 'Library/Simplezend/Loader/Autoload.php';
Library\Loader\Autoload::factory(array(
    'Library\Loader\StandardAutoloader'=>array(
        'autoregister_frame'=>true
    )
));
Library\Application\Application::init(require 'Config/application.php')->run();

