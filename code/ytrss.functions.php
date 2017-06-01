<?php

if(!defined('YT_RSS__INIT'))
    include('redirect.php');


//
error_reporting(E_ERROR | E_PARSE);

//
function ytrss_load( $url )
{
    $ch = curl_init();

    //
	curl_setopt( $ch, 	CURLOPT_HEADER, 		0 );
    curl_setopt( $ch, 	CURLOPT_URL, 			$url );
    curl_setopt( $ch, 	CURLOPT_USERAGENT, 		"Mozilla/5.0" );
	curl_setopt( $ch, 	CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt( $ch, 	CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, 	CURLOPT_TIMEOUT, 		100 );
 
    //
    $data = curl_exec( $ch );
    
    //
    curl_close( $ch );

    //
    return $data;
}

function ytrss_error( $message )
{
    die( $message );
    exit();
}

//
function ytrss_description( &$record, &$videoid )
{
    //
    $output =   '<a href="'.$record['link'].
                '" target="_blank"><img src="http://img.youtube.com/vi/'.$videoid.
                '/mqdefault.jpg" /></a>';

    //
    if( $record['author'] || $record['duration'] )
    {
        //
        $output .= '<p><i>';

        //
        if( $record['author'] )
        {
            $output .= 'Uploads by <a href="'.$record['author']['uri'].'">'.$record['author']['name'].'</a>';
        }

        if( $record['duration'] )
        {
            if( $record['author'] )
            {
                $output .= ', ';
            }
            $output .= 'Duration: '.$record['duration'];
        }

        //
        $output .= '</i></p>';

    }

    //
    if( $record['description'] )
    {
        $output .=  '<p>'.nl2br(preg_replace(
                    '@(https?://([-\w\.]+)+(:\d+)?(/(.[\w/_\.%-=#]*(\?\S+)?)?)?)@',
                    '<a href="$1">$1</a>',
                    $record['description']
                )).'</p>';
    }

    //
    return $output;
}

?>