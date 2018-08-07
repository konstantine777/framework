<?php

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
define('PATH', substr(parse_url($_SERVER['REQUEST_URI'])['path'], '1'));