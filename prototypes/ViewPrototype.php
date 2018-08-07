<?php

namespace prototypes;


class ViewPrototype
{

	public static function Root()
	{

		return '<b>Добро пожаловать в наш API</b>';

	}

	public static function notFound()
	{

		return '<ppan style="color: #8b0008">Ошибка 404, страница не найдена.</ppan>';

	}

}