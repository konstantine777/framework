<?php

include_once 'config/userConfiguration.php';
include_once 'config/configurationConstants.php';
include_once 'config/autoload.php';
include_once 'config/secondary.php';

use router\Router as Router;

Router::init();
