<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');


class ytrss_itemlist
{
	//
	function __construct()
	{
		$this->rss = new rss();
	}

	//
	var $title;
	var $author;

	//
	var $reverse = false;

	//
	var $rss;

	//
	var $items;
	var $queue;

	//
	function data( $itemlist, $limit = false )
	{
		//
		if( $limit === false )
		{
			$limit = count( $itemlist->queue );
		}
		
		//
		for( $i = 0; $i < $limit; $i++ )
		{
			if( $this->items[ $itemlist->queue[$i] ] )
			{
				$this->items[ $itemlist->queue[$i] ]['description'] = $itemlist->items[ $itemlist->queue[$i] ]['description'];
			}
		}
	}

	//
	function show( $limit = false )
	{
		//
		if( $this->reverse )
		{
			$queue = array_reverse( $this->queue );
		}
		else
		{
			$queue = &$this->queue;
		}

		//
		// var_dump( $queue );

		//
		if( $limit === false )
		{
			$limit = count( $queue );
		}
		
		//
		for( $i = 0; $i < $limit; $i++ )
		{
			$this->rss->push( $this->items[ $queue[$i] ]['title'],
						$this->items[ $queue[$i] ]['link'],
						ytrss_description( $this->items[ $queue[$i] ], $queue[$i] ),
						$this->items[ $queue[$i] ]['published']
					);
		}

		//
		// var_dump( $this->rss->show() );

		//
		return $this->rss->show();
	}

}

?>