<?php

function loadClass($class)
{
	if(file_exists(LIB . $class . '.php')) {
		require(LIB . $class . '.php');
	}
}

spl_autoload_register('loadClass');