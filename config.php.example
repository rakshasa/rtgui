<?php
//
//  This file is part of rtGui.  http://rtgui.googlecode.com/
//  Copyright (C) 2007-2011 Simon Hall.
//
//  rtGui is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  rtGui is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with rtGui.  If not, see <http://www.gnu.org/licenses/>.

// Connect string for your local RPC/rTorrent connection:
$rpc_connect="http://localhost/RPC2";

// rtorrent 'watch' directory (used for upload torrent)
$watchdir="/Torrents/TorrentFiles/Auto/";

// Path to report disk usage
$downloaddir="/Torrents";

// Threshold for disk usage alert (%)
$alertthresh=15;

// Time between ajax calls - default 5000 (5 secs).   Disable with 0
$defaultrefresh=5000;  

// Display tracker URL for each torrent on main page - you might want to disable this if you run lots (ie 30+ ?) 
// torrents - To get the tracker URL requires another RPC call for every torrent displayed.  
// If it's disabled, it only requires one RPC call to list all the torrents.
$displaytrackerurl=TRUE;

// URL to your rtGui installation (used in RSS feed).  Include trailing slash.
$rtguiurl="http://192.168.0.1/rtgui/";

// Speeds for the download cap settings dialog.
$defspeeds=array(5,10,15,20,30,40,50,60,70,80,90,100,125,150,200,250,300,400,500,600,700,800,900,1000,1500,2000,5000,10000);

// Start download immediately after loading torrent
$load_start=FALSE;

// Enable debug tabs
$debugtab=FALSE;

// Tracker colour hilighting...
// Format is array(hexcolour, URL, URL, ...) The URL is a string to match identifiy tracker URL
// Add as many arrays as needed.
$tracker_hilite_default="#900";   // Default colour
$tracker_hilite[]=array("#990000","ibiblio.org","etree.org");
$tracker_hilite[]=array("#006699","another.com","tracker.mytracker.net","mytracker.com");
$tracker_hilite[]=array("#996600","moretrackers.com");


// Define your RSS feeds here - you can have as many as you like.   Used in the feedreader
// Feed name, feed URL, Direct download links? (0/1)
$feeds[]=array("ibiblio.org","http://torrent.ibiblio.org/feed.php?blockid=3",0);
$feeds[]=array("etree","http://bt.etree.org/rss/bt_etree_org.rdf",0);
$feeds[]=array("Utwente","http://borft.student.utwente.nl/%7Emike/oo/bt.rss",1);

?>
