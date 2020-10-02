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

// By default, only completed torrents returned.
// To change, use:
//   rssfeed.php?view=xxx
// Where xxx=
//   main  (all torrents)
//   started
//   stopped
//   complete
//   incomplete
//   seeding

include "config.php";
include "functions.php";
extract($_REQUEST, EXTR_PREFIX_ALL|EXTR_REFS, 'r');

if (!isset($r_view)) {
   $r_view="complete";
}

// header:
header('Content-Type: text/xml');
echo "<?xml version=\"1.0\"?>\n";
echo "<rss version=\"2.0\">\n";
echo "<channel>\n";
echo "<title>rtGui rss feed</title>\n";
echo "<description>Latest info from your rTorrent/rtGui system</description>\n";
echo "<generator>rtGui - http://rtgui.googlecode.com/ </generator>\n";
echo "<link>".$rtguiurl."</link>";
echo "<lastBuildDate>".date("r")."</lastBuildDate>\n";

$data=get_full_list($r_view);

if (is_array($data)) {
   $sortkey="state_changed";
   usort($data,'sort_matches_desc');

   foreach($data AS $item) {
      echo "<item>\n";
      echo "<title>".($item['complete']==1 ? "[Complete] " : "[Incomplete] ").htmlspecialchars($item['name'])."</title>\n";
      echo "<description>\n";
      echo htmlspecialchars($item['tied_to_file'])." (".format_bytes($item['size_bytes']).")";
      echo "</description>\n";
      echo "<pubDate>".date("r",$item['state_changed'])."</pubDate>\n";
      echo "<guid>".$rtguiurl."view.php?hash=".$item['hash']."</guid>\n";
      echo "</item>\n";
   }
}
echo "</channel>\n";
echo "</rss>\n";
?>
