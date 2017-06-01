<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');


#
include_once( 'class.rss.php' );
include_once( 'ytrss.functions.php' );
include_once( 'ytrss.itemlist.php' );

#
class ytrss_feedlist extends ytrss_itemlist
{
	//
	function load( $url, $limit = false )
	{
		$file = ytrss_load( $url );

		//
		$stream = new DOMDocument();
		$stream->loadHTML( $file );

		//
		$items = $stream->getElementsByTagName('entry');

		//
		$stream = new DomXPath( $stream );

		//
		$output = array(
				'items' => array()
			);

		//
		$title 	= $stream->query( '//title[1]' );
		$author = $stream->query( '//author/name' );

		//
		if( $title->length )
		{
			$this->title = $title[0]->textContent;
		}

		//
		if( $author->length )
		{
			$this->author = $author[0]->textContent;
		}

		//
		$no = -1;

	// 	//
	// 	$this->read( $items, $limit );
	// }

	// //
	// function read( &$items, &$limit, &$no = -1 )
	// {
		//
		foreach( $items as $item )
		{
			//
			$path = $item->getNodePath();

			//
			$videoid = $stream->query( $path.'/videoid' );

			//
			if( $videoid->length == 0 )
			{
				// error;
				continue;
			}
			else
			{
				$no++;
				$videoid = $videoid[0]->textContent;
			}

			//
			$elements = array(
					$stream->query( $path.'/title' ),
					$stream->query( $path.'/channelid' ),
					$stream->query( $path.'/author/name' ),
					$stream->query( $path.'/author/uri' ),
					$stream->query( $path.'//published' ),
					$stream->query( $path.'//description')
				);

			//
			$record = array(
					'videoid'	=>	$videoid,
					'link'		=> 'https://www.youtube.com/watch?v='.$videoid
				);

			//
			if( $elements[0]->length )
			{
				$record['title'] = $elements[0][0]->textContent;
			}

			//
			if( $elements[1]->length )
			{
				$record['channelid'] = $elements[1][0]->textContent;
			}

			//
			if( $elements[2]->length && $elements[3]->length )
			{
				$record['author'] = array(
						'uri'	=>	$elements[3][0]->textContent,
						'name'	=>	$elements[2][0]->textContent
					);
			}

			//
			if( $elements[4]->length )
			{
				$record['published'] = $elements[4][0]->textContent;
			}

			//
			if( $elements[5]->length )
			{
				$record['description'] = $elements[5][0]->textContent;
			}

			//
			$this->items[$videoid] = $record;
			$this->queue[$no] = $videoid;



			//
			if( $limit && $no == $limit )
			{
				return;	
			}
			

		}

		//
		// return $output;

	}

}
?>