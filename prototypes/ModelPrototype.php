<?php

namespace prototypes;


class ModelPrototype
{

	function __construct()
	{

		$this->datebase = new \mysqli(DB_CONFIG['host'], DB_CONFIG['username'], DB_CONFIG['password'], DB_CONFIG['db_name']);

	}

	protected function getArrayFromSQL($result)
	{

		$result->data_seek(0);
		$array = $result->fetch_assoc();

		return $array;

	}

	function __destruct()
	{

		$this->datebase->close();

	}


}