<?php

namespace prototypes;
use view\View;

class ControllerPrototype
{
	protected $view;
	protected $model;
	protected $user;

	function __construct($objectArray)
	{

		include_once $objectArray[1];

		$this->view = new View();
		$this->model = new $objectArray[0]();
		$this->user = $objectArray[2];

	}

}