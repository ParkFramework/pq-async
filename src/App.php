<?php

class App
{
	public static $db;
	public static $loop;
	public static $coroutine;

	public function run($coroutine)
	{
		self::$coroutine = $coroutine;

		try
		{
			$coroutine->rewind();
		}
		catch(Throwable $e)
		{
			print_r($e);
		}

		return $this;
	}

	public function loop()
	{
		self::$loop->run();
	}

	public static function start()
	{
		self::$loop = EvLoop::defaultLoop();
		self::$db   = new App\DB;

		return new self;
	}
}