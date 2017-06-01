<?php

if(!defined('YT_RSS__INIT'))
	include('redirect.php');

class rss
{
	//
	var $title = 'rss:title';
	var $description = 'rss:description';
	var $link = 'rss:link';

	//
	var $cache = '';


	//
	function push( $title, $link, $description, $time = false )
	{
		$output .=  "\t<item>\n".
					"\t\t<title>".$title."</title>\n".
                    "\t\t<link>".htmlspecialchars($link)."</link>\n".
                    "\t\t<guid>".htmlspecialchars($link)."</guid>\n";

        //
		if( $time )
		{        
        	$output .= "\t\t<pubDate>".date(DATE_RSS, strtotime( $time ))."</pubDate>\n";
        }
    
       	//
        $output .=  "\t\t<description><![CDATA[".
        			$description.
        			"]]></description>\n\t</item>\n";
        //
        $this->cache .= $output;
	}

	//
	function show()
	{
		//
		return 	"<?xml version=\"1.0\" ?>\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n<channel>\n".
        			"\t<atom:link href=\"http://".htmlspecialchars($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).
                	"\" rel=\"self\" type=\"application/rss+xml\" />\n\t<link>".($this->link).
                    "</link>\n\t<pubDate>".date(DATE_RSS).
                    "</pubDate>\n\t<title>".($this->title).
                    "</title>\n\t<description>".($this->description).
                    "</description>\n".($this->cache).
                    "</channel>\n</rss>";
	}
}

//
?>