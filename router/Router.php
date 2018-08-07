<?php

namespace router;
use user_spirit\User;
use view\View;

class Router
{

	static $controller; // Объявим переменную controller, в последствии сюда будет помещен
	// объект запрошенного пользователем контроллера.

	/**
	 * Метод init, вызывается первым и является точкой входа, он разбирает строку брауера,
	 * подключает запрошенные классы, вызывает экшен из контроллера.
	 */
	static public function init()
	{

		$token = null;

		if(isset($_GET['token']))// Отследим наличие переданного GET-параметра token.
		{
			$token = $_GET['token'];
		}

		$user = new User($token); // Создадим экземпляр класса User, передав ему токен,
		// если токена нет в базах или он не был передан с запросом, метод isRegistration
		// будет возвращать false.

		$breakArray = explode('/', PATH); //Разбиваем url на массив при помощи разделителя "/".

		/**
		 * Длинна разбитого массива может быть равна 3,
		 * засчет символа "/" в конце url, но при обращении без него, длинна составит 2,
		 * больше она быть не может и не должна. Поэтому сразу проверим массив на длинну,
		 * что бы отфильтровать заранее не валидные запросы.
		 */
		if(count($breakArray) <= 3)
		{

			if($breakArray[count($breakArray) - 1] === '') // Проверяем на наличие пустого элемента в конце массива.
			{

				if(count($breakArray) === 3)   // Удаляем его, если он на 3 позиции.
				{
					unset($breakArray[count($breakArray) - 1]);
				}
				// Либо вставляем дефолтное имя экшена, если он на второй позиции.
				elseif(count($breakArray) === 2)
				{
					$breakArray[count($breakArray) - 1] = DEFAULT_ACTION_NAME;
				}
				// В противном случае, обращение идет к корню и мы перенаправим пользователя на страницу приветствия.
				else
				{
					die(View::Root());
				}
			}
			elseif ($breakArray[0] !== 0 AND count($breakArray) === 1) // Так же проверим на наличие второго
				// аргумента, если есть первый, обе проверки взаимоисключаемы.
			{
				$breakArray[1] = DEFAULT_ACTION_NAME;
			}

			/**
			 * Определим имена контроллера и модели, по запросу пользователя,
			 * при этом строка с начала полностью приводится к нижнему регистру,
			 * затем первая буква делается заглавной, поскольку это имя php файла-класса,
			 * к имени добавим соответствующие постфиксы.
			 */
			$controllerName = ucfirst(mb_strtolower($breakArray[0])).'Controller';
			$modelName = ucfirst(mb_strtolower($breakArray[0])).'Model';

			/**
			 * Определяем полный путь до файлов модели и контроллера при помощи
			 * предопределенной константы SITE_ROOT. Прежде всего удобно это тем,
			 * что обращение может происходить из любой директории.
			 */
			$controllerPath = SITE_ROOT.'controllers/'.$controllerName.'.php';
			$modelPath = SITE_ROOT.'models/'.$modelName.'.php';

			// Прежде чем продолжить, проверим наличие файлов в динамически собранном пути.
			// Если одного из них нет, кинем на страницу 404.
			if(file_exists($controllerPath) AND file_exists($modelPath))
			{

				include_once $controllerPath; // Загрузим класс контроллера.

				/**
				 * Создадим экземпляр класса контроллера и определим его в статический
				 * метод controller, это позволит избежать передачи всего объекта в пределах
				 * класса в последующем.
				 */
				self::$controller = new $controllerName([$modelName, $modelPath, $user]);

				// Определяем имя экшена, который должен быть вызван.
				$actionName = 'action'.ucfirst(mb_strtolower($breakArray[1]));

				/**
				 * Определим, какой экшен нам доступен и получим его,
				 * статический метод showAvailableAction принимает на вход имя экшена
				 * и флаг(bool) - зарегестрирован-ли пользователь, он вернет публичный
				 * экшен, если такой существует, в случае если пользователь не авторизован,
				 * и вренет публичный или приватный, в случае если пользователь передал верный токен.
				 * Если есть два одинаковых публичный и приватный экшены, то для зараегестрированного
				 * пользователя будет возвращен приватный. Во всех других случаях, метод вернет false.
				 */
				$availableAction = self::showAvailableAction($actionName, $user->isRegistration());

				if($availableAction) // Проверим, если у на экшен, в противном случае - 404.
				{
					self::$controller->$availableAction(); // Если есть, вызываем его.
				}
				else die(View::notFound());

			}
			else die(View::notFound());

		}
		else die(View::notFound());

	}

	/**
	 * @param $action
	 * @param $userAuthorisation
	 * @return bool|string
	 *
	 * Возвращает публичный или приватный экшен, в зависимости от авторизованности пользователя.
	 * Если экшен не найден, вернет false.
	 */
	static public function showAvailableAction($action, $userAuthorisation)
	{

		if($userAuthorisation) // Проверим, зарегестрирован ли пользователь.
		{
			// Если пользователь авторизован, проверим, есть ли приватный экшен
			// В объекте контроллера, который сохранен в статическом свойстве controller.
			if (method_exists(self::$controller, $action))
			{

				return $action; // В случае успеха, возвращаем его.

			}

		}

		// Добавим перфикс и преведем первую букву переданного имени к верхнему регистру, для
		// поддержки CamelCase.
		$publicActionName = 'all'.ucwords($action);

		// Проверим на наличие приватного экшена в объекте контроллера.
		if (method_exists(self::$controller, $publicActionName))
		{

			return $publicActionName; // Вернем в случае успеха.

		}

		return false; // Вернем false, т.к, если не одно из условий не было соблюдено,
		// значит не приватного, не публичного экшена в объекте нет.

	}

}