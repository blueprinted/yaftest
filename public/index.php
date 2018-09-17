<?php

define('APP_PATH', __DIR__);

$application = new Yaf\Application(APP_PATH . "/conf/application.ini");

$application->bootstrap()->run();
