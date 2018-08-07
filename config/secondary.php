<?php

function getLine($line, $bias)
{

	return $line - $bias;

}

function debug($structure, $caption = '')
{

	echo "<pre style='background-color: #dfe3e3;padding: 30px;box-sizing: border-box; color: #3f5999'>";
	echo "<span style='color: #2b8b26'>$caption\n</span>";
	print_r($structure);
	echo "</pre>";

}