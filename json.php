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

session_start();
include "config.php";
include "functions.php";
import_request_variables("gp","r_");

if (!isset($r_view)) $r_view="main";
$data=get_full_list("$r_view");

// If tracker_filter is set, get tracker URL for each torrent
if ($_SESSION['tracker_filter']!="") {
   if ($displaytrackerurl==TRUE && is_array($data)) {
      foreach($data as $key=>$item) {
         $data[$key]['tracker_url']=tracker_url($item['hash']);
      }
   }
}

$totcount=0;
if ($data) {
   foreach($data as $subitem) {
      $displaythis=FALSE;
      if ($_SESSION['tracker_filter']=="") { 
         $displaythis=TRUE; 
      }
      if (@stristr($subitem['tracker_url'],$_SESSION['tracker_filter'])==TRUE) {
         $displaythis=TRUE;
      }
      if ($displaythis) {
         if ($subitem['complete']==1) { $statusstyle="complete"; } else { $statusstyle="incomplete"; }
         if ($subitem['is_active']==1) { $statusstyle.="active"; } else { $statusstyle.="inactive"; }
         $subdata[$totcount]['control']=($subitem['is_active']==1 ? "stop" : "start" );
         $eta="";
         if ($subitem['down_rate']>0) {
            $eta=formateta(($subitem['size_bytes']-$subitem['completed_bytes'])/$subitem['down_rate'])." Remaining... ";
         }
         $subdata[$totcount]['name']="<input type='checkbox' name='select[]' value='".$subitem['hash']."' style='checkbox'> <a class='submodal-600-500 $statusstyle' href='view.php?hash=".$subitem['hash']."'>".$subitem['name']."</a>";
         $subdata[$totcount]['message']=$eta.$subitem['message'];
         $subdata[$totcount]['status_string']="<img src='images/".$statusstyle.".gif' width=10 height=9 alt='Status'>".$subitem['status_string'];
         $subdata[$totcount]['percent_complete']=$subitem['percent_complete']." %<br>".percentbar(@round(($subitem['percent_complete']/2)));
         $subdata[$totcount]['size_bytes']=format_bytes($subitem['size_bytes']);
         $subdata[$totcount]['down_rate']=format_bytes($subitem['down_rate']);
         $subdata[$totcount]['up_rate']=format_bytes($subitem['up_rate']);
         $subdata[$totcount]['up_total']=format_bytes($subitem['up_total']);
         $subdata[$totcount]['peers']=$subitem['peers_connected']."/".$subitem['peers_not_connected']." (".$subitem['peers_complete'].")";
         $subdata[$totcount]['ratio']=@round(($subitem['ratio']/1000),2);
         $subdata[$totcount]['priority_str']=$subitem['priority_str'];
         $subdata[$totcount]['hash']=$subitem['hash'];
         $subdata[$totcount]['bytes_diff']=completed_bytes_diff($subitem['size_bytes'],$subitem['completed_bytes']);
         $subdata[$totcount]['filemtime']=age($subitem['filemtime']);

         $totcount++;
      }
   }
}

// Get overall download/upload speed...
$rates=get_global_rates();
$subdata[$totcount]['glob_down_rate']=(format_bytes($rates[0]['ratedown'])=="" ? "0 KB" : format_bytes($rates[0]['ratedown'])."/sec" );
$subdata[$totcount]['glob_up_rate']=(format_bytes($rates[0]['rateup'])=="" ? "0 KB" : format_bytes($rates[0]['rateup'])."/sec" );
//$subdata[$totcount]['glob_diskfree']=format_bytes($rates[0]['diskspace'])." / ".format_bytes(disk_total_space($downloaddir))." (".(round($rates[0]['diskspace']/disk_total_space($downloaddir)*100))."%)";
$subdata[$totcount]['glob_diskfree']="<div ".( (round($rates[0]['diskspace']/disk_total_space($downloaddir)*100) <= $alertthresh  )  ? "class=\"diskalert\""  : ""  ).">";
$subdata[$totcount]['glob_diskfree'].="Disk Free: ".format_bytes($rates[0]['diskspace'])." / ".format_bytes(disk_total_space($downloaddir))." (".(round($rates[0]['diskspace']/disk_total_space($downloaddir)*100))."%)";
$subdata[$totcount]['glob_diskfree'].="</div>";
//   echo "Disk Free: ".format_bytes($rates[0]['diskspace'])." / ".format_bytes(disk_total_space($downloaddir))." (".(round($rates[0]['diskspace']/disk_total_space($downloaddir)*100))."%)\n";
//   echo "</div>\n";


if (@is_array($_SESSION['lastget'])) {
   $last=$_SESSION['lastget'];
} else {
   $last=array();
}

// Write out JSON format string...
echo "{";
$count=0;
foreach($subdata as $item => $val) {
   if (isset($last[$item])) {
      $diff=array_diff_assoc($val,$last[$item]);
      foreach($diff as $changes => $cval) {
         if (isset($val['hash'])) { 
            $div="t".$val['hash'].$changes;
         } else {
            $div=$changes;
         }
         echo '"change'.$count.'": { ';
         echo '   "div" : "'.$div.'",';
         echo '   "val" : "'.str_replace('"','\"',$cval).'"';
         echo '},'."\n";
         $count++;
      }
   }
}
echo '"total":"'.$count.'" ';
echo '}';

// Copy subdata array to session var, so we can compare it for changes on next call of this page...
$_SESSION['lastget']=$subdata;
?>
