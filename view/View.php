<?php

namespace view;
use prototypes\ViewPrototype;

class View extends ViewPrototype
{

	public function reply($answer)
	{

		$JSON_string = json_encode($answer);

		if (getallheaders()['Accept'] === '*/*')
		{
			echo $JSON_string;
		}
		else echo "<pre style='word-wrap: break-word;white-space: pre-wrap;'>".$JSON_string."</pre>";

	}

}