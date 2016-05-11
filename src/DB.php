<?php

namespace App;

use Ev;
use App;

use Throwable;
use Exception;

use pq\Result;
use pq\Connection;

class DB
{
	public $pq;
	public $loop;

	public function __construct()
	{
		$pq = $this->pq = new Connection('dbname=test user=test');

		$this->loop = App::$loop->io($pq->socket, Ev::READ, function($w) use($pq)
		{
			$pq->poll();

			if(null === $r = $pq->getResult())
			{
				return;
			}

			if($r->status === Result::TUPLES_OK)
			{
				App::$coroutine->send($r);
			}
			else if($r->status === Result::FATAL_ERROR)
			{
				App::$coroutine->throw(new Exception($r->errorMessage, $r->status));
			}
		});
	}

	public function listen($name, $call)
	{
		$pq = $this->pq;

		if($pq->busy)
		{
			$pq->getResult();
		}

		$pq->listenAsync($name, $call);
	}

	public function exec($sql)
	{
		$pq = $this->pq;

		if($pq->busy)
		{
			$pq->getResult();
		}

		$pq->execAsync($sql);
	}
}