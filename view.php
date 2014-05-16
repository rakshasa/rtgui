<?php
//
// rtGui - Copyright Simon Hall 20007
//
// http://rtgui.googlecode.com/
//
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="favicon.ico" />
<title>rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="submodal/subModal.css" />
<script type="text/javascript" src="submodal/common.js"></script>
<script type="text/javascript" src="submodal/subModal.js"></script>
</head>
<body>
<?php
// Get torrent info...  (get all downloads, then filter out just this one by the hash)
$alltorrents=get_full_list("main");
$thistorrent=array();
foreach($alltorrents as $torrent) {
   if ($r_hash==$torrent['hash']) $thistorrent=$torrent;
}
// Controls (stop/start/hash check etc)...
echo "<table align=center class='maintable' border=0 cellspacing=0 cellpadding=5>\n";
echo "<tr>\n";
echo "<td>";
if ($thistorrent['is_active']==1) {
   echo "<a class='controls' href='control.php?hash=".$thistorrent['hash']."&cmd=stop'><img align=top alt='Stop torrent' border=0 src='images/stop.gif' width=16 height=16> Stop</a>";
} else {
   echo "<a class='controls'  href='control.php?hash=".$thistorrent['hash']."&cmd=start'><img align=top alt='Start torrent' border=0 src='images/start.gif' width=16 height=16> Start</a>";
}
echo "</td>";
echo "<td>&nbsp;</td>";
echo "<td><a class='controls' target='_top' href='control.php?hash=".$thistorrent['hash']."&cmd=delete' onClick='return confirm(\"Delete torrent - are you sure? (This will not delete data from disk)\");'><img align=top alt='Delete torrent' border=0 src='images/delete.gif' width=16 height=16> Delete</a></td>";
echo "<td>&nbsp;</td>";
echo "<td><a class='controls'  href='control.php?hash=".$thistorrent['hash']."&cmd=hashcheck'><img align=top alt='Start hash check' border=0 src='images/hashcheck.gif' width=16 height=16> Hash check</a></td>\n";
echo "<td>&nbsp;</td>";
echo "<td><a class='controls'  href='view.php?select=$r_select&hash=".$thistorrent['hash']."'><img align=top alt='Refresh' border=0 src='images/refresh.gif' width=16 height=16> Refresh</a></td>\n";
echo "</tr></table>\n";
echo "<hr size=1>";

// Select view...
echo "<table cellspacing=0 cellpadding=3>\n";
echo "<tr><td>&nbsp;</td>\n";
echo "<td class='".($r_select=="files" ? "viewselon" : "viewseloff")."'><a href='?select=files&hash=$r_hash'>Files</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($r_select=="tracker" ? "viewselon" : "viewseloff")."'><a href='?select=tracker&hash=$r_hash'>Tracker</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($r_select=="torrent" ? "viewselon" : "viewseloff")."'><a href='?select=torrent&hash=$r_hash'>Torrent</a></td>\n";
echo "</tr></table>\n";

// Main outer table...
echo "<table class='maintable' width='100%'>\n";
echo "<tr><td>\n";

// Display file info...
if ($r_select=="files") {
   $data=get_file_list($r_hash);
   echo "<form action='control.php' action=post>";
   echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable' width='100%'>";
   echo "<tr class='tablehead'>";
   echo "<td>Filename</td>";
   echo "<td align=center>Size</td>";
   echo "<td align=center>Done</td>";
   echo "<td align=center>Chunks</td>";
   echo "<td align=center>Priority</td>";
   echo "</tr>\n";
   $thisrow="row1";
   $index=0;
   foreach($data AS $item) {
      echo "<tr class='$thisrow'>";
      echo "<td colspan=5>".wordwrap($item['get_path'],90,"<br>\n",TRUE)."</td>";
      echo "</tr><tr class='$thisrow'>\n";
      echo "<td class='datacol'>&nbsp;</td>";
      echo "<td class='datacol' align=center nowrap>".format_bytes($item['get_size_bytes'])."</td>";
      echo "<td class='datacol' align=center nowrap>".@round(($item['get_completed_chunks']/$item['get_size_chunks'])*100)." %<br>\n";
      echo percentbar(@round((($item['get_completed_chunks']/$item['get_size_chunks'])*100)/2));
      echo "</td>\n";
      echo "<td class='datacol' align=center nowrap>".$item['get_completed_chunks']." / ".$item['get_size_chunks']."</td>\n";
      echo "<td align=center>\n";
      echo "<select name='set_fpriority[$index]'>\n";
      echo "<option value='0' ".($item['get_priority']==0 ? "selected" : "").">Off</option>\n";
      echo "<option value='1' ".($item['get_priority']==1 ? "selected" : "").">Normal</option>\n";
      echo "<option value='2' ".($item['get_priority']==2 ? "selected" : "").">High</option>\n";
      echo "</select>\n";
      echo "<input type='hidden' name='hash' value='$r_hash'>\n";
      echo "</td>\n";
      echo "</tr>\n";
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
      $index++;
   }
   echo "</table>\n";
   echo "<div align=right>";
   echo "<input type='submit' value='Save'>";
   echo "</div>\n";
   echo "</form>";
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
   echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable' width='100%'>";
   echo "<tr class='row2'><td class='datacol' align=right><b>Name</b></td><td><span class='torrenttitle $statusstyle'>".wordwrap($thistorrent['name'],65,"<br>\n",TRUE)."</span></td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Status</b></td><td><img src='images/".$statusstyle.".gif' width=10 height=9 alt='Status'> ".$thistorrent['status_string']."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Status Flags</b></td><td>".$statusflags."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Message</b></td><td>".$thistorrent['message']."</td>";
   echo "<tr class='row2'><td class='datacol' align=right><b>Completed Bytes</td><td>".format_bytes($thistorrent['completed_bytes'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Size</b></td><td>".format_bytes($thistorrent['size_bytes'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Complete</b></td><td>".$thistorrent['percent_complete']." %&nbsp;&nbsp;";
   echo percentbar(@round(($thistorrent['percent_complete']/2)));
   echo "<tr class='row1'><td class='datacol' align=right><b>Down Rate</b></td><td>".format_bytes($thistorrent['down_rate'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Down Total</b></td><td>".format_bytes($thistorrent['down_total'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Up Rate</b></td><td>".format_bytes($thistorrent['up_rate'])."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Up Total</b></td><td>".format_bytes($thistorrent['up_total'])."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Peers connected</b></td><td>".$thistorrent['peers_connected']."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Peers not connected</b></td><td>".$thistorrent['peers_not_connected']."</td></tr>\n";
   echo "<tr class='row1'><td class='datacol' align=right><b>Peers complete</b></td><td>".$thistorrent['peers_complete']."</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Ratio</b></td><td>".@round(($thistorrent['ratio']/1000),2)." %</td></tr>\n";
   echo "<tr class='row2'><td class='datacol' align=right><b>Priority</b></td><td>".$thistorrent['priority_str']."</td></tr>\n";

   echo "</table>\n<br>\n";
}

// tracker info...
if ($r_select=="tracker") {
   $data=get_tracker_list($r_hash);
   echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable' width='100%'>";
   echo "<tr class='tablehead'>";
   echo "<td>URL</td>";
   echo "<td align=center>Last</td>";
   echo "<td align=center>Interval</td>";
   echo "<td align=center>Scrapes</td>";
   echo "<td align=center>Enabled</td>";
   echo "</tr>\n";
   $thisrow="row1";
   foreach($data AS $item) {
      echo "<tr class='$thisrow'>";
      echo "<td colspan=5>".wordwrap($item['get_url'],90,"<br>\n",TRUE)."</td>";
      echo "</tr><tr class='$thisrow'>\n";
      echo "<td class='datacol' width='80%'>&nbsp;</td>";
      echo "<td class='datacol' align=center nowrap>".($item['get_scrape_time_last']>0 ? date("Y-m-d H:i",@round($item['get_scrape_time_last']/1000000)) : "never")."</td>";
      echo "<td class='datacol' align=center nowrap>".@round($item['get_normal_interval']/60)." mins</td>";
      echo "<td class='datacol' align=center nowrap>".$item['get_scrape_complete']."</td>";
      echo "<td align=center nowrap>".($item['is_enabled']==1 ? "Yes" : "No")."</td>";
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
   }
   echo "</table>\n<br>\n";
}

echo "</tr></td></table>\n";

?>
</body>
</html>