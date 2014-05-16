<?php
//
// rtGui - Copyright Simon Hall 20007
//
// http://rtgui.googlecode.com/
//
$execstart=$start=microtime(true);
session_start();
include "config.php";
include "functions.php";
import_request_variables("gp","r_");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="submodal/subModal.css" />
<script type="text/javascript" src="submodal/common.js"></script>
<script type="text/javascript" src="submodal/subModal.js"></script>
<title>rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
// Sort out the session variables for sort order, sort key and current view...
if (!isset($_SESSION['sortkey'])) $_SESSION['sortkey']="name";
if (isset($r_setsortkey)) $_SESSION['sortkey']=$r_setsortkey;

if (!isset($_SESSION['sortord'])) $_SESSION['sortord']="asc";
if (isset($r_setsortord)) $_SESSION['sortord']=$r_setsortord;

if (!isset($_SESSION['view'])) $_SESSION['view']="main";
if (isset($r_setview)) $_SESSION['view']=$r_setview;

$globalstats=get_global_stats();

// Title Block...
echo "<table width=100% border=0 cellpadding=5 cellspacing=0>\n";
echo "<tr><td><a href='index.php'><h1>rtGui</h1></a>";
echo "<i class='smalltext'>The rTorrent Graphical User Interface</i><br>\n";

echo "</td>\n";
echo "<td align=right class='mediumtext'>";
echo "<form method='post' action='control.php'>";
echo "Max Up: ";
echo "<select name='setmaxup'>";
foreach (array(0,5,10,15,20,30,40,50,75,100,150,200,250,300,400,500,750,1000) AS $i) {
   $x=($i*1024);
   echo "<option value='$x' ";
   if ($x==$globalstats['upload_cap']) echo "selected";
   echo ">".format_bytes($x)."</option>";
}
echo "</select>";
echo "&nbsp;&nbsp;Down: ";
echo "<select name='setmaxdown'>";
foreach (array(0,5,10,15,20,30,40,50,75,100,150,200,250,300,400,500,750,1000) AS $i) {
   $x=($i*1024);
   echo "<option value='$x' ";
   if ($x==$globalstats['download_cap']) echo "selected";
   echo ">".format_bytes($x)."</option>";
}
echo "</select>";
echo "&nbsp;<input type='submit' name='submit' value='Set'>";
echo "</form><br>\n";
echo "<form method='post' action='control.php'>";
echo "Add torrent URL: <input type=text cols=20 name='addurl' size=38 maxlength=500>";
echo "&nbsp;<input type='submit' name='submit' value='Add'>";
echo "</form>";
   
echo "</tr></td>\n</table>\n<br>\n";
// ..end of title block

// Get the list of torrents downloading
$data=get_full_list($_SESSION['view']);

// Sort the list
if (is_array($data)) {
   if (strtolower($_SESSION['sortord']=="asc")) {
      $sortkey=$_SESSION['sortkey'];
      usort($data,'sort_matches_asc');
   } else {
      $sortkey=$_SESSION['sortkey'];
      usort($data,'sort_matches_desc');
   }
} else {
   $data=array();
}

// View selection...
echo "<table cellspacing=0 cellpadding=3>\n";
echo "<tr><td>&nbsp;</td>\n";
echo "<td class='".($_SESSION['view']=="main" ? "viewselon" : "viewseloff")."'><a href='?setview=main'>All</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($_SESSION['view']=="started" ? "viewselon" : "viewseloff")."'><a href='?setview=started'>Started</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($_SESSION['view']=="stopped" ? "viewselon" : "viewseloff")."'><a href='?setview=stopped'>Stopped</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($_SESSION['view']=="complete" ? "viewselon" : "viewseloff")."'><a href='?setview=complete'>Complete</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($_SESSION['view']=="incomplete" ? "viewselon" : "viewseloff")."'><a href='?setview=incomplete'>Incomplete</a></td>\n";
echo "<td>&nbsp</td>\n";
echo "<td class='".($_SESSION['view']=="seeding" ? "viewselon" : "viewseloff")."'><a href='?setview=seeding'>Seeding</a></td>\n";
echo "</tr></table>\n";

// Main table 
echo "<form action='control.php' method='post'>";
echo "<table class='maintable' border=0 cellspacing=0 cellpadding=5 width='100%'>\n";
// The headings, with sort links...
echo "<tr class='tablehead'>\n";
$uparr="<img src='images/uparrow.gif' height=8 width=5 alt='Descending'>";
$downarr="<img src='images/downarrow.gif' height=8 width=5 alt='Ascending'>";
echo "<td nowrap width='20%'><a href='?setsortkey=name&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Name</a> ".($_SESSION['sortkey']=="name" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=status_string&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Status</a> ".($_SESSION['sortkey']=="status_string" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=percent_complete&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Done</a> ".($_SESSION['sortkey']=="percent_complete" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=completed_bytes&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Leeched</a> ".($_SESSION['sortkey']=="completed_bytes" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=size_bytes&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Size</a> ".($_SESSION['sortkey']=="size_bytes" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=down_rate&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Down Speed</a> ".($_SESSION['sortkey']=="down_rate" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=down_total&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Down Size</a> ".($_SESSION['sortkey']=="down_total" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=up_rate&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Up Speed</a> ".($_SESSION['sortkey']=="up_rate" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=up_total&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Seeded</a> ".($_SESSION['sortkey']=="up_total" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=peers_connected&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Peers</a> ".($_SESSION['sortkey']=="peers_connected" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=ratio&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Ratio</a> ".($_SESSION['sortkey']=="ratio" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "<td nowrap width='7%' align=center><a href='?setsortkey=priority_str&setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Priority</a> ".($_SESSION['sortkey']=="priority_str" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</td>\n";
echo "</tr>";

// List the torrents...
$thisrow="row1";
$totcompleted_bytes=0;
$totsize=0;
$totpercent_complete=0;
$totdown_rate=0;
$totdown_total=0;
$totup_rate=0;
$totup_total=0;
$totratio=0;
$totcount=0;
foreach($data AS $item) {
   if ($item['complete']==1) {
      $statusstyle="complete";
   } else {
      $statusstyle="incomplete";
   }
   if ($item['is_active']==1) {
      $statusstyle.="active";
   } else {
      $statusstyle.="inactive";
   }

   echo "<tr class='$thisrow'>";
   echo "<td colspan=12><span class='torrenttitle'>";

   echo "<a class='submodal-600-500 $statusstyle' href='view.php?hash=".$item['hash']."'>".$item['name']."</a></span> ";

   // message...
   if ($item['message']!="") echo "<br>\n<span class='error'>".$item['message']."</span>\n";
   echo "</tr>";

   echo "<tr class='$thisrow'>";
   echo "<td nowrap class='datacol'>";
   // Stop/start controls...
   echo "<a href='control.php?hash=".$item['hash']."&cmd=".($item['is_active']==1 ? "stop" : "start")."'>".($item['is_active']==1 ? "<img alt='Stop torrent' border=0 src='images/stop.gif' width=16 height=16>" : "<img alt='Start torrent' border=0 src='images/start.gif' width=16 height=16>")."</a>&nbsp;";
   echo "<a href='control.php?hash=".$item['hash']."&cmd=delete' onClick='return confirm(\"Delete torrent - are you sure? (This will not delete data from disk)\");'><img alt='Delete torrent' border=0 src='images/delete.gif' width=16 height=16></a>&nbsp;";
   echo "<a class='submodal-600-500' href='view.php?hash=".$item['hash']."'><img alt='Torrent info' src='images/view.gif' width=16 height=16></a></span><br>\n";
   // stats row...
   echo "<td nowrap align=center class='datacol'><img src='images/".$statusstyle.".gif' width=10 height=9 alt='Status'>".$item['status_string']."</td>";
   echo "<td nowrap align=center class='datacol'>".$item['percent_complete']." %<br>\n";
   echo percentbar(@round(($item['percent_complete']/2)));
   echo "</td>\n";
   echo "<td nowrap align=center class='datacol'>".format_bytes($item['completed_bytes'])."</td>\n";
   echo "<td nowrap align=center class='datacol'>".format_bytes($item['size_bytes'])."</td>\n";

   echo "<td nowrap align=center class='datacol'>".format_bytes($item['down_rate'])."</td>\n";
   echo "<td nowrap align=center class='datacol'>".format_bytes($item['down_total'])."</td>\n";
   echo "<td nowrap align=center class='datacol'>".format_bytes($item['up_rate'])."</td>\n";
   echo "<td nowrap align=center class='datacol'>".format_bytes($item['up_total'])."</td>\n";
   echo "<td nowrap align=center class='datacol'>".$item['peers_connected']."/".$item['peers_not_connected']." (".$item['peers_complete'].")</td>\n";
   echo "<td nowrap align=center class='datacol'>".round(($item['ratio']/1000),2)." %</td>\n";
   echo "<td nowrap align=center>";
   echo "<input type='hidden' name='hash[$totcount]' value='".$item['hash']."'>";
   echo "<select name='set_tpriority[$totcount]'>\n";
   echo "<option value='0' ".($item['priority']==0 ? "selected" : "").">Off</option>\n";
   echo "<option value='1' ".($item['priority']==1 ? "selected" : "").">Low</option>\n";
   echo "<option value='2' ".($item['priority']==2 ? "selected" : "").">Normal</option>\n";
   echo "<option value='3' ".($item['priority']==3 ? "selected" : "").">High</option>\n";
   echo "</select>\n";
   echo "</td>\n";
   echo "</tr>\n";

   // Keep tally for totals
   $totcompleted_bytes+=$item['completed_bytes'];
   $totsize+=$item['size_bytes'];
   $totpercent_complete+=$item['percent_complete'];
   $totdown_rate+=$item['down_rate'];
   $totdown_total+=$item['down_total'];
   $totup_rate+=$item['up_rate'];
   $totup_total+=$item['up_total'];
   $totratio+=$item['ratio'];
   $totcount++;
   if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
}
// Display message if no torrents to list...
if (!$data) {
   echo "<tr class='row1'><td colspan=12 align=center>Empty</td>\n</tr>";
}

// Totals row...
echo "<tr class='tablehead'><td>&nbsp;</td>\n";
echo "<td>&nbsp;</td>\n";
echo "<td align=center nowrap class='datacol'>".@round(($totpercent_complete/$totcount),2)." %</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totcompleted_bytes)."</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totsize)."</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totdown_rate)."</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totdown_total)."</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totup_rate)."</td>\n";
echo "<td align=center nowrap class='datacol'>".format_bytes($totup_total)."</td>\n";
echo "<td class='datacol'>&nbsp;</td>\n";
echo "<td align=center nowrap class='datacol'>".@round((($totratio/$totcount)/1000),2)." %</td>\n";
echo "<td align=center><input type='submit' value='Set'></td>\n";
echo "</td>\n";
echo "</table>\n";
echo "</form><br>\n<br>\n";

// Footer...
echo "<center class='smalltext'>\n";
echo "<a href='http://libtorrent.rakshasa.no/' target='_blank'>rTorrent ".$globalstats['client_version']."/".$globalstats['library_version']."</a> | ";
echo "Disk Free: ".format_bytes(disk_free_space($globalstats['diskspace']))." | ";
echo "Page created in ".$restime=round(microtime(true)-$execstart,3)." secs.<br>\n";
echo "<a href='http://rtgui.googlecode.com' target='_blank'>rtGui v0.2.2</a> - by Simon Hall 2007";
echo "</center>\n";

?>
</body>
</html>