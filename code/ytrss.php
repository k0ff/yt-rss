<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');


#
if(!defined('YT_RSS__INIT'))
	goto quit;

#
$feedfile = YT_RSS__CACHE.'/'.(floor(time()/300));

#
switch( true )
{
	case (isset( $_GET['user'] )): 
		$feedfile .= '__user_'.$_GET['user'];
		break;

	case (isset( $_GET['playlist'] )):
		$feedfile .= '__playlist_'.$_GET['playlist'];
		break;

	case (isset( $_GET['channel'] )): 
		$feedfile .= '__channel_'.$_GET['channel'];
		break;

	case (isset( $_GET['search'] )): 
		$feedfile .= '__search_'.$_GET['search'];
		if( isset( $_GET['sp'] ) )
		{
			$feedfile .= '_sp_'.$_GET['sp'];
		}
		break;

}

#
if( isset( $_GET['reverse'] ) )
{
	$feedfile .= '_reverse';
}

#
$feedfile .= '.xml';

#
if( file_exists( $feedfile ) )
{
	$feed = file_get_contents( $feedfile );
	goto send;
}


#
include_once( 'ytrss.search.php' );

#
include_once( 'ytrss.playlist.php' );
include_once( 'ytrss.feedlist.php' );

#
if( isset( $_GET['user'] ) )
{
	//
	$feedlist = new ytrss_feedlist();

	//
	$feedlist->load( 'http://www.youtube.com/feeds/videos.xml?user='.$_GET['user'] );

	//
	if( count( $feedlist->queue ) == 0 )
	{
		ytrss_error( 	'Not found data stream on <a href="'.
						'http://www.youtube.com/feeds/videos.xml?user='.$_GET['user'].
						'">'.'http://www.youtube.com/feeds/videos.xml?user='.$_GET['user'].'</a>' );
	}

	//
	$feedlist->rss->title = &$feedlist->title;
	$feedlist->rss->link = htmlspecialchars( 'https://www.youtube.com/user/'.$_GET['user'] );
	$feedlist->rss->description = &$feedlist->author;

	//
	goto feed;
}

#
if( isset( $_GET['playlist'] ) )
{
	//
	$feedlist = new ytrss_feedlist();
	$playlist = new ytrss_playlist();

	//
	$feedlist->load( 'https://www.youtube.com/feeds/videos.xml?playlist_id='.$_GET['playlist'] );

	//
	//
	if( count( $feedlist->queue ) == 0 )
	{
		ytrss_error( 	'Not found data stream on <a href="'.
						'https://www.youtube.com/feeds/videos.xml?playlist_id='.$_GET['playlist'].
						'">'.'https://www.youtube.com/feeds/videos.xml?playlist_id='.$_GET['playlist'].'</a>' );
	}

	//
	$playlist->load( 'https://www.youtube.com/playlist?list='.$_GET['playlist'] );

	// echo '<pre>';
	// var_dump( $playlist );
	// echo '</pre>';



	//
	$playlist->data( $feedlist );
	$feedlist = $playlist;

	//
	$feedlist->rss->title = &$feedlist->title;
	$feedlist->rss->link = htmlspecialchars( 'https://www.youtube.com/playlist?list='.$_GET['playlist'] );
	$feedlist->rss->description = &$feedlist->author;

	//
	goto feed;
}

#
if( isset( $_GET['channel'] ) )
{
	//
	$feedlist = new ytrss_feedlist();

	//
	$feedlist->load( 'http://www.youtube.com/feeds/videos.xml?channel_id='.$_GET['channel'] );	

	//
	//
	if( count( $feedlist->queue ) == 0 )
	{
		ytrss_error( 	'Not found data stream on <a href="'.
						'http://www.youtube.com/feeds/videos.xml?channel_id='.$_GET['channel'].
						'">'.'http://www.youtube.com/feeds/videos.xml?channel_id='.$_GET['channel'].'</a>' );
	}

	//
	$feedlist->rss->title = &$feedlist->title;
	$feedlist->rss->link = htmlspecialchars( 'https://www.youtube.com/channel/'.$_GET['channel'] );
	$feedlist->rss->description = &$feedlist->author;

	//
	goto feed;
}

#
if( isset( $_GET['search'] ) )
{
	//
	$feedlist = new ytrss_search();

	//
	$url = 'https://www.youtube.com/results?q='.urlencode($_GET['search']);

	//
	if( isset( $_GET['sp'] ) )
	{
		$url .= '&sp='.$_GET['sp'];
	}

	$feedlist->load( $url );

	//
	$feedlist->title = $_GET['search'];
	$feedlist->author = 'Wyniki wyszukiwania';

	//
	$feedlist->rss->title = &$feedlist->title;
	$feedlist->rss->link = htmlspecialchars( $url );
	$feedlist->rss->description = &$feedlist->author;

	//
	goto feed;
}

#
quit:
echo ';)';
exit;



#
feed:

#
if( isset( $_GET['reverse'] ) )
{
	$feedlist->rss->description .= ' (reversed)';		
	$feedlist->reverse = true;
}

#
$feed = $feedlist->show();
file_put_contents( $feedfile, $feed );

#
send:
header('Content-Type:application/rss+xml');
echo $feed;

?>