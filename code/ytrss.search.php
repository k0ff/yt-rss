<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');


#
include_once( 'class.rss.php' );
include_once( 'ytrss.functions.php' );
include_once( 'ytrss.itemlist.php' );

#
class ytrss_search extends ytrss_itemlist
{
	//
	function load( $url )
	{
		//
		$file = ytrss_load( $url );

		//
		// $file = substr_replace( $file, '<meta charset="UTF-8">', strpos( $file, '<style'), 0);  

		//
		$stream = new DOMDocument('5.0','UTF-8');
		$stream->loadHTML( $file );
		$stream = new DomXPath( $stream );

		//
		$this->read( $stream );
	}

	//
	function read( &$stream )
	{
		//
		$no = 0;

		//
		$items = $stream->query('//*[@id=\'results\']/ol/li/ol/li/div/div');

		//
		foreach( $items as $item )
		{
			//
			$path = $item->getNodePath();

			//
			$elements = array(
				$stream->query( $path.'//div[1]/a/span' ),
				$stream->query( $path.'//div[2]/h3/a' ),
				$stream->query( $path.'//div[2]/div[1]/a' ),
				$stream->query( $path.'//div[2]/div[3]' ),
				$stream->query( $path.'//div[1]/button' )
			);

			//
			$record = $stream->query( $path.'//div[1]/button' );

			//
			if( $record->length != 0 )
			{
				//
				$record = array( 'videoid' => $record[0]->getAttribute('data-video-ids') );

				//
				if( $elements[0]->length != 0 )
				{
					$record['duration'] = $elements[0][0]->textContent;
				}

				if( $elements[1]->length != 0 )
				{
					$record['title'] = utf8_decode( $elements[1][0]->textContent );
				}

				if( $elements[2]->length != 0 )
				{
					$record['author'] = array(
							'name' 	=> 	utf8_decode( $elements[2][0]->textContent ),
							'uri' 	=> 	utf8_decode( 'http://www.youtube.com'.$elements[2][0]->getAttribute('href') )
						);
				}

				if( $elements[3]->length != 0 )
				{
					$record['description'] = utf8_decode( $elements[3][0]->textContent ); 
				}

				//
				$record['link'] = 'https://www.youtube.com/watch?v='.$record['videoid'];

				//
				$this->items[$record['videoid']] = $record;
				$this->queue[$no] = $record['videoid'];

				//
				$no++;
			}
			
		
		}

	}
}

?>