<?php

spl_autoload_register(function ($file)
{

	include SITE_ROOT.str_replace('\\', '/', $file).".php";

});