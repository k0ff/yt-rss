<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');


#
include_once( 'class.rss.php' );
include_once( 'ytrss.functions.php' );
include_once( 'ytrss.itemlist.php' );

#
class ytrss_playlist extends ytrss_itemlist
{
	//
	function load( $url, $limit = false )
	{
		//
		$file = ytrss_load( $url );
		$file = substr_replace( $file, '<meta charset="UTF-8">', strpos( $file, '<style'), 0);  

		// echo($file);
		// exit;

		//
		$stream = new DOMDocument('5.0','UTF-8');
		$stream->loadHTML( $file );
		$stream = new DomXPath( $stream );

		//
		$title 	= $stream->query('.//*[@id=\'pl-header\']/div[2]/h1');
		$author	= $stream->query('.//*[@id=\'pl-header\']/div[2]/ul/li[1]/a');

		//
		if( $title->length )
		{
			$this->title = utf8_decode( trim( $title[0]->textContent ) );
		}

		//
		if( $author->length )
		{
			$this->author = utf8_decode( trim( $author[0]->textContent ) );
		}

		//
		$this->read( $stream, $limit );
	}

	function read( &$stream, &$limit = false, &$no = -1 )
	{
		//
		$items = $stream->query('.//tr[contains(@class, \'pl-video\')]');
		
		//
		foreach( $items as $item )
		{
			//
			$path = $item->getNodePath();

			//
			$elements = array(
					$stream->query( $path.'//td[4]/div/a' ),
					$stream->query( $path.'//td[7]/div/div[1]/span' ),
					$stream->query( $path.'//td[4]/a' )
				);

			//
			$record = array(
					'videoid'	=>	$item->getAttribute('data-video-id'),
					'title'		=>	utf8_decode( $item->getAttribute('data-title') )
					// $elements[0][0]->getAttribute('href'),
					// $elements[0][0]->textContent,
					// $elements[1][0]->textContent,
				);

			//
			if( $record['videoid'] && $record['title'] )
			{
				$no++;
			}
			else
			{
				continue;
			}

			//
			if( $elements[0]->length != 0 )
			{
				$record['author'] = array(
						'name'	=>	utf8_decode( $elements[0][0]->textContent ),
						'uri'	=>	utf8_decode( 'http://www.youtube.com'.$elements[0][0]->getAttribute('href') )
					);
			}

			//
			if( $elements[1]->length != 0 )
			{
				$record['duration'] = $elements[1][0]->textContent;
			}

			//
			if( $elements[2]->length != 0 )
			{
				$record['link'] = 'http://www.youtube.com'.$elements[2][0]->getAttribute('href');
			}
			else
			{
				$record['link'] = 'https://www.youtube.com/watch?v='.$record['videoid'];
			}

			//
			$this->items[$record['videoid']] = $record;
			$this->queue[$no] = $record['videoid'];

			//
			if( $limit && $no == $limit )
			{
				return;	
			}

		}

		//
		$next = $stream->query('.//button[@data-uix-load-more-href]');

		//
		if( $next->length )
		{
			//
			$json = json_decode( ytrss_load('https://www.youtube.com'.$next[0]->getAttribute('data-uix-load-more-href')) );

			//
			$next = new DOMDocument();
			$next->loadHTML( '<html><head><meta charset="UTF-8"></head><body><table>'.$json->content_html.'</table><hr>'.$json->load_more_widget_html.'</body></html>' );
			$next = new DomXPath( $next );

			//
			$this->read( $next, $limit, $no );

		}
	}

}
?>