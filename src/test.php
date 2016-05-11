#!/usr/bin/php
<?php

include __DIR__.'/DB.php';
include __DIR__.'/App.php';

function testSubQuery()
{
	$res = yield App::$db->exec('SELECT 1');

	$res->fetchCol($one);

	echo "Result: $one\r\n";

	try
	{
		yield App::$db->exec('SELECT throw');
	}
	catch(Throwable $e)
	{
		echo 'Catch error: '.$e->getCode()."\r\n";
	}

	$res = yield App::$db->exec('SELECT 2');

	$res->fetchCol($two);

	echo "Result: $two\r\n";

	return $one + $two;
}

function testQuery()
{
	App::$db->listen('name', function($name, $message)
	{
		echo "NOTIFY: $message\r\n";
	});

	$ret = yield from testSubQuery();

	echo "Return: $ret\r\n";

	App::$db->exec("NOTIFY name, 'Test'");
}

App::start()->run(testQuery())->loop();