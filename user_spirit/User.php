<?php

namespace user_spirit;
use prototypes\ModelPrototype;

class User extends ModelPrototype
{

	private $user;

	function __construct($token)
	{

		parent::__construct();

		if($token)
		{

			if($result = $this->datebase->query("SELECT * FROM `".USER_CONFIG['table_name']."` WHERE `".USER_CONFIG['token_select']."` = '$token'"))
			{

				$this->user = $this->getArrayFromSQL($result);

			}
			else die("Ошибка запроса к базе данных при попытке получить данные пользователя в файле: <b>".__FILE__."</b>, в строке: ".getLine(__LINE__, 6).".\n");

		}
		else
		{

			$this->user = [];

		}

	}

	public function isRegistration()
	{

		if(isset($this->user['id']))
		{

			return true;

		}

		return false;

	}

	public function getUserData()
	{

		return $this->user;

	}

}