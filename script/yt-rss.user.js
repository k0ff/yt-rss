// ==UserScript==
// @name        Youtube RSS Subscriber
// @namespace   yt-rss
// @description Youtube RSS Subscriber
// @include http*://*.youtube.com/*
// @version     0.12
// @grant       none
// ==/UserScript==

//
ytrss_HREF = 'http://yt-rss.k0ff.eu';

// 
function ytrss_append( rss_HREF, rss_TITLE )
{
	//
	link = document.createElement('link');

	//
	link.href  = rss_HREF;
	link.type  = 'application/rss+xml';
	link.title = rss_TITLE;
	link.rel   = 'alternate';

	//
	document.getElementsByTagName('head')[0].appendChild(link);

}

//
function ytrss_URI( url )
{
    var request = {};
    var pairs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        if(!pairs[i])
            continue;
        var pair = pairs[i].split('=');
        request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
     }
     return request;
}

//
function ytrss_URL( url, value )
{
	url = url.split('/');
	for( i = 0; i < url.length; i++ )
	{
		if( url[i] == value )
		{
			return url[i+1];
		}
	}
	return false;
}

//
function ytrss_find( url )
{
	if( url.indexOf( '/channel/' ) != -1 )
	{
		channel = ytrss_URL( url, 'channel' );
		if( channel )
		{
			ytrss_append( ytrss_HREF+'?channel='+channel, 'Subskrybuj kanał przez yt-rss' );
		}
	}

	if( url.indexOf( '/user/' ) != -1 )
	{
		user = ytrss_URL( url, 'user' );
		if( user )
		{
			ytrss_append( ytrss_HREF+'?user='+user, 'Subskrybuj użytkownika przez yt-rss' );
		}
	}

	if( url.indexOf( '/playlist?' ) != -1 || url.indexOf( '/watch?' ) != -1 )
	{
		list = ytrss_URI( url ).list;
		if( list )
		{
			ytrss_append( ytrss_HREF+'?playlist='+list+'&reverse', 'Subskrybuj playliste (odwrócona) przez yt-rss' );
			ytrss_append( ytrss_HREF+'?playlist='+list, 'Subskrybuj playliste przez yt-rss' );
		}

	}

	if( url.indexOf( '/results?' ) != -1 )
	{
		uri = ytrss_URI( url );
		query = uri.q || uri.search_query;
		if( query )
		{
			url = ytrss_HREF+'?search='+query;
			if( uri.sp )
			{
				url += '&sp='+uri.sp;
			}
			ytrss_append( url, 'Subskrybuj wyniki wyszukiwania przez yt-rss' );
		}
	}

}

//
{
	 if( document.querySelectorAll && location.href.indexOf( '/watch?' ) != -1 )
	{
		
		find = document.querySelectorAll('.yt-uix-sessionlink.g-hovercard.spf-link');
		if( find.length )
		{
			ytrss_find( find[0].href );
		}
	}

	ytrss_find( location.href );
}