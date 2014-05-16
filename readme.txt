= rtGui - the Web front end for rTorrent =

rtGui is a web based front end for rTorrent - the Linux command line BitTorrent client.  It's written in PHP and uses XML-RPC to communicate with the rTorrent client.

For more information on rTorrent, see the home page: http://libtorrent.rakshasa.no/

== Features ==
  * List all torrent downloads or by started/stopped/complete/incomplete/seeding status.
  * Sort view by any of the displayed columns.
  * View detailed torrent information.
  * Stop/start/hash-check torrent.
  * Set upload/download speed cap.
  * Add torrents by URL.
  * Does not require mySQL or any other database.
  * Set priority per torrent or file.

== Requirements ==
  * rTorrent complied with XML-RPC library support - see http://libtorrent.rakshasa.no/ 
  * XML-RPC library - see http://xmlrpc-c.sourceforge.net/
  * Apache webserver configured with XML-RPC - see http://libtorrent.rakshasa.no/wiki/RTorrentXMLRPCGuide
  * PHP 5 with XML-RPC module - see http://www.php.net/
  * A web browser - see http://www.mozilla.com/


== Installation ==
  * Change to your webserver root directory, eg:
   cd /srv/www/htdocs
  * Extract the files from the archive you downloaded:
   tar xvzf rtgui-0.1.tgz
  * Modify config.php, if required:
   vi rtgui/config.php
  * Point your web browser to the directory, eg:
   http://localhost/rtgui
  * Enjoy :)

== Security considerations ==
Absolutely no thought whatsoever has been given to security in rtGui - do not run this on a publicly available website.  rtGui is intended for 'home' use where users can be considered as trusted.  A basic authentication mechanism is planned for future releases.  At the very least, you should password protect your webserver using .htaccess or similar (see http://en.wikipedia.org/wiki/Htaccess for more info).
