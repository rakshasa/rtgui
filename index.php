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
session_start();
include "config.php";
include "functions.php";
import_request_variables("gp","r_");

// Try using alternative XMLRPC library from http://sourceforge.net/projects/phpxmlrpc/  (see http://code.google.com/p/rtgui/issues/detail?id=19)
if(!function_exists('xml_parser_create')) {
   include("xmlrpc.inc");
   include("xmlrpc_extension_api.inc");
}

// Sort out the session variables for sort order, sort key and current view...
if (!isset($_SESSION['sortkey'])) $_SESSION['sortkey']="name";
if (isset($r_setsortkey)) $_SESSION['sortkey']=$r_setsortkey;

if (!isset($_SESSION['sortord'])) $_SESSION['sortord']="asc";
if (isset($r_setsortord)) $_SESSION['sortord']=$r_setsortord;

if (!isset($_SESSION['view'])) $_SESSION['view']="main";
if (isset($r_setview)) $_SESSION['view']=$r_setview;

if (!isset($_SESSION['tracker_filter'])) $_SESSION['tracker_filter']="";
if (isset($r_settrackerfilter)) $_SESSION['tracker_filter']=$r_settrackerfilter;

if (!isset($_SESSION['filter_invert'])) $_SESSION['filter_invert']=0;
if (isset($r_setfilterinvert)) $_SESSION['filter_invert']=$r_setfilterinvert;

if (isset($r_reload)) unset($_SESSION['lastget']);

if (!isset($_SESSION['refresh'])) $_SESSION['refresh']=$defaultrefresh;

if (!isset($r_debug)) $r_debug=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="submodal/subModal.css" />
<script type="text/javascript" src="submodal/common.js"></script>
<script type="text/javascript" src="submodal/subModal.js"></script>
<script type="text/javascript" src="rtgui.js"></script>
<script type="text/javascript" language="Javascript">
function start(view,timer) {
   <?php if (isset($r_reload)) echo "ajax(view);"; ?>
   ajax(view);
   setInterval("ajax('"+view+"')",timer)
}
</script>
<title><?php echo php_uname('n') ?>::rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<?php
echo "<body ".($_SESSION['refresh']!=0 ? "onLoad=\"start('".$_SESSION['view']."',".$_SESSION['refresh'].")\"" : "").">\n";
echo "<div id='wrap'>\n";

$globalstats=get_global_stats();
$rates=get_global_rates();

// Title Block...
echo "<div id=header>\n";
echo "<h1><a href='index.php?reload=1'>rt<span class=green>gui</span></a></h1><br/>\n";

echo "<div id='boxright'>\n";

// Global upload/download...
echo "<p>\n";
echo "Down: <span class='inline download' id='glob_down_rate'>".(format_bytes($rates[0]['ratedown'])=="" ? "0 KB" : format_bytes($rates[0]['ratedown']))."/sec</span> ".($globalstats['download_cap']!=0 ? "<span class='smalltext'>[".format_bytes($globalstats['download_cap'])."]</span>" : "")."&nbsp;&nbsp;&nbsp;\n";
echo "Up: <span class='inline upload' id='glob_up_rate'>".(format_bytes($rates[0]['rateup'])=="" ? "0 KB" : format_bytes($rates[0]['rateup']))."/sec</span> ".($globalstats['upload_cap']!=0 ? "<span class='smalltext'>[".format_bytes($globalstats['upload_cap'])."]</span>" : "")."\n";
echo "</p>\n";

if (isset($downloaddir)) {
   echo "<div id='glob_diskfree'>";
   echo "<div ".( (round($rates[0]['diskspace']/disk_total_space($downloaddir)*100) <= $alertthresh  )  ? "class='diskalert'"  : ""  ).">\n";
   echo "Disk Free: ".format_bytes($rates[0]['diskspace'])." / ".format_bytes(disk_total_space($downloaddir))." (".(round($rates[0]['diskspace']/disk_total_space($downloaddir)*100))."%)\n";
   echo "</div>\n";
   echo "</div>\n";
}


// Tracker filter
echo "<form method='post'>\n";
if ($_SESSION['tracker_filter']=="") {
   echo "<p>Group filter:";
   echo "<select name='settrackerfilter'>";
   foreach ($tracker_hilite as $num=>$tracker) {
      echo "<option value='_group_".$num."'>".$tracker[1].(isset($tracker[2]) ? "..." : "")."</option>";
   }
   echo "</select><input type='submit' value='Go'></p>\n";
} else {
   echo "<p>Showing ".($_SESSION['filter_invert']==0 ? "ONLY" : "all EXCEPT")." <b>";
   if (substr($_SESSION['tracker_filter'],0,7)=="_group_") {
      echo "Group (".$tracker_hilite[str_ireplace("_group_","",$_SESSION['tracker_filter'])][1].(isset($tracker_hilite[str_ireplace("_group_","",$_SESSION['tracker_filter'])][2]) ? "..." : "").")";
   } else {
      echo $_SESSION['tracker_filter'];
   }
   echo "</b> [ <a href='?settrackerfilter=&setfilterinvert=0'>Reset</a> | <a href='?setfilterinvert=".($_SESSION['filter_invert']==0 ? "1" : "0")."'>Invert</a> ] </p>\n";
}
echo "</form>\n";

// Settings/Add Torrent etc...
echo "<p><a class='submodal-600-520' href='settings.php'>Settings</a> | <a href=\"javascript:toggleLayer('divadd');\">Add Torrent</a></p>\n";

// Hidden Add Torrent form...
echo "<div id='divadd' class='togglevis' style='width:350px;float:right;' align='right'>";
echo "<form method='post' action='control.php' enctype='multipart/form-data'>\n";
echo "URL: <input type=text name='addurl' size=38 maxlength=500 /> <input type='submit' value='Go' /><br/>\n";
echo "File: <input name='uploadtorrent' type='file' size=25 /> <input type='submit' value='Go' />\n";
echo "</form>\n";
echo "</div>\n";  // end of divadd div

echo "</div>\n";  // end of boxright div

echo "</div>\n";  // end of header div
// Title Block End

// Get the list of torrents downloading
$data=get_full_list($_SESSION['view']);

// Get tracker URL for each torrent - this does an RPC query for every torrent - might be heavy on server so you might want to disable in config.php
if ($displaytrackerurl==TRUE && is_array($data)) {
   foreach($data as $key=>$item) {
      $data[$key]['tracker_url']=tracker_url($item['hash']);
   }
}

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

echo "<form action='control.php' method='post' name='control'>";

// View selection...
echo "<div id='navcontainer' style='clear:both;'>\n";

echo "<ul id='navlist'>\n";
echo "<li><a ".($_SESSION['view']=="main" ? "id='current'" : "")." href='?setview=main'>All</a></li>\n";
echo "<li><a ".($_SESSION['view']=="started" ? "id='current'" : "")." href='?setview=started'>Started</a></li>\n";
echo "<li><a ".($_SESSION['view']=="stopped" ? "id='current'" : "")." href='?setview=stopped'>Stopped</a></li>\n";
echo "<li><a ".($_SESSION['view']=="complete" ? "id='current'" : "")." href='?setview=complete'>Complete</a></li>\n";
echo "<li><a ".($_SESSION['view']=="incomplete" ? "id='current'" : "")." href='?setview=incomplete'>Incomplete</a></li>\n";
echo "<li><a ".($_SESSION['view']=="seeding" ? "id='current'" : "")." href='?setview=seeding'>Seeding</a></li>\n";
echo "<li><a ".($_SESSION['view']=="active" ? "id='current'" : "")." href='?setview=active'>Active</a></li>\n";
if ($debugtab) {
   echo "<li><a ".($r_debug==1 ? "id='current'" : "")." href='?setview=main&amp;debug=1'>Debug</a></li>\n";
}
echo "</ul>\n";
echo "</div>\n";

echo "<div class ='container'>\n";

// The headings, with sort links...
$uparr="<img src='images/uparrow.gif' height=8 width=5 alt='Descending' />";
$downarr="<img src='images/downarrow.gif' height=8 width=5 alt='Ascending' />";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=name&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Name</a> ".($_SESSION['sortkey']=="name" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"");
echo " / <a href='?setsortkey=filemtime&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>age</a> ".($_SESSION['sortkey']=="filemtime" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"");
echo "</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=status_string&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Status</a> ".($_SESSION['sortkey']=="status_string" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=percent_complete&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Done</a> ".($_SESSION['sortkey']=="percent_complete" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=bytes_diff&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Remain</a> ".($_SESSION['sortkey']=="bytes_diff" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=size_bytes&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Size</a> ".($_SESSION['sortkey']=="size_bytes" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=down_rate&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Down</a> ".($_SESSION['sortkey']=="down_rate" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=up_rate&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Up</a> ".($_SESSION['sortkey']=="up_rate" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=up_total&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Seeded</a> ".($_SESSION['sortkey']=="up_total" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=ratio&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Ratio</a> ".($_SESSION['sortkey']=="ratio" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:89px;'><a href='?setsortkey=peers_connected&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Peers</a> ".($_SESSION['sortkey']=="peers_connected" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"")."</div>\n";
echo "<div class='headcol' style='width:84px;'>";
echo "<a href='?setsortkey=priority_str&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Pri</a> ".($_SESSION['sortkey']=="priority_str" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"");
if ($displaytrackerurl==TRUE) {
   echo "/ <a href='?setsortkey=tracker_url&amp;setsortord=".($_SESSION['sortord']=="asc" ? "desc" : "asc")."'>Trk</a> ".($_SESSION['sortkey']=="tracker_url" ? ($_SESSION['sortord']=="asc" ? "$downarr" : "$uparr") :"");
}
echo "</div>\n";
// End of headings

echo "<div class=spacer></div>\n";

if ($r_debug==1) {
   echo "<br><pre>";
   echo htmlspecialchars(var_export($data));
   echo "</pre>";
}

// List the torrents...
$totdisp=0;
$thisrow="row1";
$tottorrents=0;
foreach($data AS $item) {
   $tottorrents++;
   $displaythis=FALSE;
   if ($_SESSION['tracker_filter']=="") { 
      $displaythis=TRUE; 
   }
   if ($_SESSION['filter_invert']==0) {
      // Filter by tracker URL
      if (@stristr($item['tracker_url'],$_SESSION['tracker_filter'])==TRUE) {
         $displaythis=TRUE;
      }
      // Filter by group (defined in config)
      if (substr($_SESSION['tracker_filter'],0,7)=="_group_") {
         $groupid=str_replace("_group_","",$_SESSION['tracker_filter']);
         foreach($tracker_hilite[$groupid] as $tracker) {
            if (stristr($item['tracker_url'],$tracker)) {
               $displaythis=TRUE;
            }
         }
      }
   } else {
      // Filter by NOT tracker URL
      if (@stristr($item['tracker_url'],$_SESSION['tracker_filter'])==FALSE) {
         $displaythis=TRUE;
      }
      // Filter by NOT group
      if (substr($_SESSION['tracker_filter'],0,7)=="_group_") {
         $displaythis=TRUE;
         $groupid=str_replace("_group_","",$_SESSION['tracker_filter']);
         foreach($tracker_hilite[$groupid] as $tracker) {
            if (stristr($item['tracker_url'],$tracker)) {
               $displaythis=FALSE;
            }
         }
      }
      
   }   
   if ($displaythis) {
      $totdisp++;
      echo "<div class='$thisrow'>\n";

      if ($item['complete']==1) { $statusstyle="complete"; } else { $statusstyle="incomplete"; }
      if ($item['is_active']==1) { $statusstyle.="active"; } else { $statusstyle.="inactive"; }
      $eta="";
      if ($item['down_rate']>0) {
         $eta=formateta(($item['size_bytes']-$item['completed_bytes'])/$item['down_rate']);
      }

      echo "<div class='namecol' id='t".$item['hash']."name'>\n";
      // Tracker URL
      if ($displaytrackerurl==TRUE) {
         // echo $item['tracker_url'];
         $urlstyle=$tracker_hilite_default;
         foreach($tracker_hilite as $hilite) {
            foreach ($hilite as $thisurl) { 
               if (stristr($item['tracker_url'],$thisurl)==TRUE) { $urlstyle=$hilite[0]; }
            }
         }
         echo "<div class='trackerurl' id='tracker' ><a style='color: $urlstyle ;' id='tracker' href='?settrackerfilter=".$item['tracker_url']."&setfilterinvert=0'>".$item['tracker_url']."</a>&nbsp;</div>";
      }
      
      // Torrent name
      echo "<input type='checkbox' name='select[]' value='".$item['hash']."'  /> ";
      echo "<a class='submodal-600-520 $statusstyle' href='view.php?hash=".$item['hash']."'>".htmlspecialchars($item['name'], ENT_QUOTES)."</a>&nbsp;";
      echo "<span class='age' id='t".$item['hash']."filemtime'>".age($item['filemtime'])."</span>\n";
      echo "</div>\n";

      // message...
      echo "<div class='errorcol' id='t".$item['hash']."message'>\n";
      if ($eta!="") echo $eta." Remaining... ";
      if ($item['message']!="") echo $item['message']."\n";
      echo "</div>\n";

      // Stop/start controls...
      echo "<div class='datacol'  style='width:89px;'>\n";
      echo "<a href='control.php?hash=".$item['hash']."&amp;cmd=".($item['is_active']==1 ? "stop" : "start")."'>".($item['is_active']==1 ? "<img alt='Stop torrent' border=0 src='images/stop.gif' width=16 height=16 />" : "<img alt='Start torrent' border=0 src='images/start.gif' width=16 height=16 />")."</a> \n";
      echo "<a href='control.php?hash=".$item['hash']."&amp;cmd=delete' onClick='return confirm(\"Delete torrent - are you sure? (This will not delete data from disk)\");'><img align='bottom' alt='Delete torrent' border=0 src='images/delete.gif' width=16 height=16 /></a> \n";
      echo "<a class='submodal-600-520' href='view.php?hash=".$item['hash']."'><img alt='Torrent info' src='images/view.gif' width=16 height=16 /></a><br/>\n";
      echo "</div>\n";
      
      // Stats row...
      echo "<div class='datacol' style='width:89px;' id='t".$item['hash']."status_string'><img src='images/".$statusstyle.".gif' width=10 height=9 alt='Status' />".$item['status_string']."</div>\n";
      echo "<div class='datacol' style='width:89px;' id='t".$item['hash']."percent_complete'>".$item['percent_complete']." %<br/>".percentbar(@round(($item['percent_complete']/2)))."</div>\n";
      echo "<div class='datacol' style='width:89px;' id='t".$item['hash']."bytes_diff'>".completed_bytes_diff($item['size_bytes'],$item['completed_bytes'])."</div>\n";
      echo "<div class='datacol' style='width:89px;' id='t".$item['hash']."size_bytes'>".format_bytes($item['size_bytes'])."</div>\n";
      echo "<div class='datacol download' style='width:89px;' id='t".$item['hash']."down_rate'>".format_bytes($item['down_rate'])."</div>\n";
      echo "<div class='datacol upload' style='width:89px;' id='t".$item['hash']."up_rate'>".format_bytes($item['up_rate'])."</div>\n";
      echo "<div class='datacol' style='width:89px;' id='t".$item['hash']."up_total'>".format_bytes($item['up_total'])."</div>\n";
      echo "<div class='datacol' style='width:70px;' id='t".$item['hash']."ratio'>".@round(($item['ratio']/1000),2)."</div>\n";
      echo "<div class='datacol' style='width:105px;' id='t".$item['hash']."peers'>".$item['peers_connected']."/".$item['peers_not_connected']." (".$item['peers_complete'].")"."</div>\n";
      echo "<div class='datacollast' style='width:70px;' id='t".$item['hash']."priority_str'>".$item['priority_str']."</div>\n";
      echo "<div class=spacer> </div>\n";

      echo "</div>\n"; // end of thisrow div
      if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
   }
}

// Display message if no torrents to list...
if (!$data || $totdisp==0 ) {
   echo "<div class='row1'>\n";
   echo "<div class='namecol' align='center'><p>&nbsp;</p>No torrents to display.<p>&nbsp;</p></div>\n";
   echo "</div>\n";
}

echo "</div>\n";  // end of container div

// Bulk control...
echo "<div class='bottomtab'>";
echo "<div style='float:left;'>\n";
echo "<input type='button' value='Select All' onClick='checkAll(this.form)' />\n";
echo "<input type='button' value='Unselect All' onClick='uncheckAll(this.form)' />\n";

echo "<select name='bulkaction' >\n";
echo "<optgroup label='With Selected...'>\n";
echo "<option value='stop'>Stop</option>\n";
echo "<option value='start'>Start</option>\n";
echo "<option value='delete'>Delete</option>\n";
echo "</optgroup>\n";
echo "<optgroup label='Set Priority...'>\n";
echo "<option value='pri_high'>High</option>\n";
echo "<option value='pri_normal'>Normal</option>\n";
echo "<option value='pri_low'>Low</option>\n";
echo "<option value='pri_off'>Off</option>\n";
echo "</optgroup>\n";
echo "</select>\n";
echo "<input type='submit' value='Go' />\n";
echo "</div>";

// Number of torrents displayed/total torrents for this view
echo "<div style='float:right;'>\n";
echo $totdisp."/".$tottorrents;
echo "</div>";

echo "<br style='clear:both;'></div>";
echo "</form>\n";

// Footer...
echo "<p>&nbsp;</p>";
echo "<div align='center' class='smalltext'>\n";
echo "<a href='http://libtorrent.rakshasa.no/' target='_blank'>rTorrent ".$globalstats['client_version']."/".$globalstats['library_version']."</a> | ";
echo "<a href='rssfeed.php'>RSS Feed</a> | ";
echo "Page created in ".$restime=round(microtime(true)-$execstart,3)." secs.<br/>\n";
echo "<a href='http://rtgui.googlecode.com' target='_blank'>rtGui v0.2.8</a> - by Simon Hall 2007-2011\n";
echo "</div>\n";
?>
</div>
</body>
</html>
