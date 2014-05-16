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

$execstart=$start=microtime(true);
include "functions.php";
include "config.php";
import_request_variables("gp","r_");

if (!isset($r_select)) {
   $r_select="files";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="favicon.ico" />
<title>rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class='modal'>
<?php
// Get torrent info...  (get all downloads, then filter out just this one by the hash)
$alltorrents=get_full_list("main");
$thistorrent=array();
foreach($alltorrents as $torrent) {
   if ($r_hash==$torrent['hash']) $thistorrent=$torrent;
}
if ($thistorrent['complete']==1) { $statusstyle="complete"; } else { $statusstyle="incomplete"; }
if ($thistorrent['is_active']==1) { $statusstyle.="active"; } else { $statusstyle.="inactive"; }

echo "<h3 class='".$statusstyle."' align='center'>".mb_wordwrap($thistorrent['name'],52,"<br/>\n",TRUE)."</h3>\n";

// Controls (stop/start/hash check etc)...
echo "<div class='controlcontainer'>\n";
if ($thistorrent['is_active']==1) {
   echo "<input type=button value='Stop' class='buttonstop' onClick='window.location=\"control.php?hash=".$thistorrent['hash']."&amp;cmd=stop\"' />\n";
} else {
   echo "<input type=button value='Start' class='buttonstart' onClick='window.location=\"control.php?hash=".$thistorrent['hash']."&amp;cmd=start\"' />\n";
}
echo "<input type=button value='Delete' class='buttondel' onClick='if (confirm(\"Delete torrent - are you sure? (This will not delete data from disk)\")) parent.location=\"control.php?hash=".$thistorrent['hash']."&amp;cmd=delete\"' />\n";
echo "<input type=button value='Hash check' class='buttonhashcheck' onClick='window.location=\"control.php?hash=".$thistorrent['hash']."&amp;cmd=hashcheck\"' />\n";
echo "<input type=button value='Refresh' class='buttonrefresh' onClick='window.location=\"view.php?select=$r_select&amp;hash=".$thistorrent['hash']."\"' />\n";

echo "</div>\n"; // end of controlcontainer div
echo "<p>&nbsp;</p>";

// Select view...
echo "<div id='navcontainer'  style='width:552px'>\n";
echo "<ul id='navlist'>\n";
echo "<li><a ".($r_select=="files" ? "id='current'" : "")." href='?select=files&amp;hash=$r_hash'>Files</a></li>\n";
echo "<li><a ".($r_select=="tracker" ? "id='current'" : "")." href='?select=tracker&amp;hash=$r_hash'>Tracker</a></li>\n";
echo "<li><a ".($r_select=="peers" ? "id='current'" : "")." href='?select=peers&amp;hash=$r_hash'>Peers</a></li>\n";
echo "<li><a ".($r_select=="torrent" ? "id='current'" : "")." href='?select=torrent&amp;hash=$r_hash'>Torrent</a></li>\n";
echo "<li><a ".($r_select=="storage" ? "id='current'" : "")." href='?select=storage&amp;hash=$r_hash'>Storage</a></li>\n";
if ($debugtab) {
   echo "<li><a ".($r_select=="debug" ? "id='current'" : "")." href='?select=debug&amp;hash=$r_hash'>Debug</a></li>\n";
}
echo "</ul>\n";
echo "</div>\n";

// Display file info...
if ($r_select=="files") {
   $data=get_file_list($r_hash);
   echo "<div class='container' style='width:550px'>\n";
   echo "<div class='headcol' style='width:190px;'>Filename</div>\n";
   echo "<div class='headcol' style='width:90px;'>Size</div>\n";
   echo "<div class='headcol' style='width:90px;'>Done</div>\n";
   echo "<div class='headcol' style='width:90px;'>Chunks</div>\n";
   echo "<div class='headcol' style='width:90px;'>Priority</div>\n";
   $thisrow="row1";
   $index=0;
   echo "<form action='control.php' method=post>\n";
   foreach($data AS $item) {
      echo "<div class='$thisrow'>\n";
      echo "<div class='namecol'>\n";
      echo mb_wordwrap($item['get_path'],90,"<br/>\n",TRUE);
      echo "</div>\n";
      echo "<div class='floatright'>";
      echo "<div class='datacol smalltext' style='width:90px;'>".format_bytes($item['get_size_bytes'])."</div>\n";
      echo "<div class='datacol smalltext' style='width:90px;'>";
      echo @round(($item['get_completed_chunks']/$item['get_size_chunks'])*100)." %<br/>\n";
      echo percentbar(@round((($item['get_completed_chunks']/$item['get_size_chunks'])*100)/2));
      echo "</div>\n";
      echo "<div class='datacol smalltext' style='width:90px;'>".$item['get_completed_chunks']." / ".$item['get_size_chunks']."</div>\n";
      echo "<div class='datacollast smalltext' style='width:90px;'>";
      echo "<select name='set_fpriority[$index]' class='smalltext'>\n";
      echo "<option value='0' ".($item['get_priority']==0 ? "selected" : "").">Off</option>\n";
      echo "<option value='1' ".($item['get_priority']==1 ? "selected" : "").">Normal</option>\n";
      echo "<option value='2' ".($item['get_priority']==2 ? "selected" : "").">High</option>\n";
      echo "</select>\n";
      echo "<input type='hidden' name='hash' value='$r_hash' />\n";
      echo "</div>\n";
      echo "</div>\n";  // end floatright div
      echo "<div class='spacer'>&nbsp;</div>\n";
      echo "</div>\n";  // end of $thisrow div
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
      $index++;
   }

   echo "<div align='right' class='bottomtab'>\n";
   echo "<input type='submit' value='Save' />";
   echo "</div>\n";
   echo "</form>\n";
   
   echo "</div>\n";  // end container div
}

// tracker info...
if ($r_select=="tracker") {
   $data=get_tracker_list($r_hash);
   echo "<div class='container' style='width:550px'>\n";
   echo "<div class='headcol' style='width:156px;'>URL</div>\n";
   echo "<div class='headcol' style='width:124px;'>Last</div>\n";
   echo "<div class='headcol' style='width:90px;'>Interval</div>\n";
   echo "<div class='headcol' style='width:90px;'>Seed/Leech</div>\n";
   echo "<div class='headcol' style='width:90px;'>Enabled</div>\n";
   $thisrow="row1";
   foreach($data AS $item) {
      echo "<div class='$thisrow'>\n";
      echo "<div class='namecol'>\n";
      echo mb_wordwrap($item['get_url'],90,"<br/>\n",TRUE);
      echo "</div>\n";
      echo "<div class='floatright'>";
      echo "<div class='datacol smalltext'  style='width:124px;'>".($item['get_scrape_time_last']>0 ? date("Y-m-d
H:i",$item['get_scrape_time_last']) : "never")."</div>\n";
      echo "<div class='datacol smalltext' style='width:90px;'>".@round($item['get_normal_interval']/60)."</div>\n";
      echo "<div class='datacol smalltext' style='width:90px;'>".$item['get_scrape_complete']." / ".$item['get_scrape_incomplete']."</div>\n";
      echo "<div class='datacollast smalltext' style='width:90px;'>".($item['is_enabled']==1 ? "Yes" : "No")."</div>\n";
      echo "</div>\n";  // end floatright div
      echo "<div class='spacer'>&nbsp;</div>\n";
      echo "</div>\n";  // end of $thisrow div      
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
   }
   echo "<div class='bottomthin'> </div>\n";
   echo "</div>\n";  // end container div
}

// Peers info...
if ($r_select=="peers") {
   $data=get_peer_list($r_hash);
   echo "<div class='container' style='width:550px'>\n";
   echo "<div class='headcol' style='width:190px;'>Address</div>\n";
   echo "<div class='headcol' style='width:90px;'>Complete</div>\n";
   echo "<div class='headcol' style='width:90px;'>Download</div>\n";
   echo "<div class='headcol' style='width:90px;'>Upload</div>\n";
   echo "<div class='headcol' style='width:90px;'>Peer</div>\n";
   $thisrow="row1";
   foreach($data AS $item) {
      echo "<div class='$thisrow'>\n";
      echo "<div class='namecol smalltext'>\n";
      echo "<a href='http://www.who.is/whois-ip/ip-address/".$item['get_address']."/' target='_blank'>".gethostbyaddr($item['get_address'])."</a>";
      echo ":".$item['get_port']."&nbsp;&nbsp;<i>".$item['get_client_version']."</i>";
      $flags=($item['is_encrypted'] ? "enc " : "").($item['is_incoming'] ? "inc " : "").($item['is_obfuscated'] ? "obs " : "").($item['is_snubbed'] ? "snb " : "");
      echo ($flags!="" ? "&nbsp;&nbsp;Flags: ".$flags : "");      
      echo "</div>\n";
      echo "<div class='floatright'>";        
      echo "<div class='datacol smalltext' style='width:90px;'>&nbsp;".$item['get_completed_percent']. "%<br/>".percentbar(@round($item['get_completed_percent'])/2)."</div>\n";
      echo "<div class='datacol smalltext download' style='width:90px;'>&nbsp;".($item['get_down_rate']>0 ? format_bytes($item['get_down_rate'])."/sec<br/>" : "").format_bytes($item['get_down_total'])."</div>\n";
      echo "<div class='datacol smalltext upload' style='width:90px;'>&nbsp;".($item['get_up_rate']>0 ? format_bytes($item['get_up_rate'])."/sec<br/>" : "").format_bytes($item['get_up_total'])."</div>\n";
      echo "<div class='datacollast smalltext' style='width:90px;'>&nbsp;".($item['get_peer_rate']>0 ? format_bytes($item['get_peer_rate'])."ps<br/>" : "").format_bytes($item['get_peer_total'])."</div>\n";
      echo "</div>\n";  // end floatright div
      echo "<div class='spacer'>&nbsp;</div>\n";
      echo "</div>\n";  // end of $thisrow div 
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
   }
   echo "<div class='bottomthin'> </div>\n";
   echo "</div>\n";  // end container div
}

// Display torrent info...
if ($r_select=="torrent") {
   if ($thistorrent['complete']) { $statusflags="Complete "; } else { $statusflags="Incomplete ";}
   if ($thistorrent['is_hash_checked']) $statusflags.="&middot; Hash Checked ";
   if ($thistorrent['is_hash_checking']) $statusflags.="&middot; Hash Checking ";
   if ($thistorrent['is_multi_file']) $statusflags.="&middot; Multi-file ";
   if ($thistorrent['is_open']) $statusflags.="&middot; Open ";
   if ($thistorrent['is_private']) $statusflags.="&middot; Private ";
   if ($thistorrent['complete']==1) {
      $statusstyle="complete";
   } else {
      $statusstyle="incomplete";
   }
   if ($thistorrent['is_active']==1) {
      $statusstyle.="active";
   } else {
      $statusstyle.="inactive";
   }
   echo "<div class='container' style='width:550px'>\n";
   echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable' width='100%'>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Name</b></td><td><span class='torrenttitle $statusstyle'>".mb_wordwrap($thistorrent['name'],60,"<br/>\n",TRUE)."</span></td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Status</b></td><td><img src='images/".$statusstyle.".gif' width=10 height=9 alt='Status' /> ".$thistorrent['status_string']."</td></tr>\n";

   echo "<tr class='row2'><td class='datacol' align=right><b>Priority</b></td><td>";
   echo "<form action='control.php' method='post'>";
   echo "<input type='hidden' name='hash' value='".$thistorrent['hash']."' />";
   echo "<select name='set_tpriority'>\n";
   echo "<option value='0' ".($thistorrent['priority']==0 ? "selected" : "").">Off </option>\n";
   echo "<option value='1' ".($thistorrent['priority']==1 ? "selected" : "").">Low </option>\n";
   echo "<option value='2' ".($thistorrent['priority']==2 ? "selected" : "").">Normal </option>\n";
   echo "<option value='3' ".($thistorrent['priority']==3 ? "selected" : "").">High </option>\n";
   echo "</select>\n";
   echo "<input type='submit' value='Set' />\n";
   echo "</form>\n";

   echo "<tr class='row1'><td class='datacol' align=right><b>Status Flags</b></td><td>".$statusflags."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Message</b></td><td>".$thistorrent['message']."</td>";
   echo "<tr class='row1'><td class='datacol' align=right><b>Completed Bytes</b></td><td>".format_bytes($thistorrent['completed_bytes'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Size</b></td><td>".format_bytes($thistorrent['size_bytes'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Complete</b></td><td><div class='datacollast'>".$thistorrent['percent_complete']." %<br/>";
   echo percentbar(@round(($thistorrent['percent_complete']/2)))."</div>";
   echo "<tr class='row2'><td class='datacol' align=right><b>Down Rate</b></td><td>".format_bytes($thistorrent['down_rate'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Down Total</b></td><td>".format_bytes($thistorrent['down_total'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Up Rate</b></td><td>".format_bytes($thistorrent['up_rate'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Up Total</b></td><td>".format_bytes($thistorrent['up_total'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Peers connected</b></td><td>".$thistorrent['peers_connected']."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Peers not connected</b></td><td>".$thistorrent['peers_not_connected']."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Peers complete</b></td><td>".$thistorrent['peers_complete']."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Ratio</b></td><td>".@round(($thistorrent['ratio']/1000),2)." %</td></tr>\n";
   echo "</table>\n";
   echo "</div>\n";
   echo "<div class='bottomthin' style='width:552px;'> </div>\n";

}

// Storage info...
if ($r_select=="storage") {
   echo "<div class='container' style='width:550px'>\n";
   echo "<fieldset ><legend>Current Directory</legend>\n";
   $seldir=$thistorrent['directory'];

   $torrentdir="";
   if ($thistorrent['is_multi_file']==1) {
      $seldir=substr($thistorrent['directory'],0,strrpos($thistorrent['directory'],"/"));
      $torrentdir=substr($thistorrent['directory'],strrpos($thistorrent['directory'],"/"));
   }
   $torrentdir=htmlentities($torrentdir,ENT_QUOTES,"UTF-8");
   if (isset($r_dir)) $seldir=$r_dir;
   
   echo "<p style='background-color:#ddd;padding:3px;'><span id='seldir'>".$seldir."</span><span class='gray'>".$torrentdir."</span></p>\n";

   echo "<form action='control.php' method='post' name='directory' onSubmit=\"document.directory.newdir.value=document.getElementById('seldir').innerHTML;\">\n";
   if ($thistorrent['is_active']==1) {
      echo "<p><input type='submit' name='setdir'  value='Set directory' disabled=1>&nbsp;<i>Torrent must be stopped before changing directory.</i></p>\n";
   } else {
      echo "<input type='hidden' name='hash' value='".$thistorrent['hash']."'>\n";
      echo "<input type='hidden' name='newdir' value=''>\n";
      echo "<input type='submit' name='setdir'  value='Set directory'>\n";
   }
   echo "</fieldset>\n";
   echo "</form>\n";  
   
   echo "<iframe frameborder=0 src='dirbrowser.php?dir=".urlencode($seldir)."&amp;hilitedir=".urlencode($torrentdir)."' width=100% height=300px>iFrame</iframe>";

   echo "<br>&nbsp;</div>"; // end container div
   echo "<div class='bottomthin' style='width:552px;'> </div>\n";
}

// Debug info
if ($r_select=="debug") {
   echo "<pre class='medtext'>";
   echo "<h2>Torrent</h2>";
   echo nl2br(print_r($thistorrent));
   echo "<h2>Files</h2>";
   echo nl2br(print_r(get_file_list($r_hash)));
   echo "<h2>Peers</h2>";
   echo nl2br(print_r(get_peer_list($r_hash)));
   echo "<h2>Tracker</h2>";
   echo nl2br(print_r(get_tracker_list($r_hash)));
   echo "</pre>";
}

?>
</div>
</body>
</html>
