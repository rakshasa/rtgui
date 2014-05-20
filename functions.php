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

// Optionally use alternative XMLRPC library from http://sourceforge.net/projects/phpxmlrpc/
// See http://code.google.com/p/rtgui/issues/detail?id=19
if(!function_exists('xml_parser_create')) {
  include("xmlrpc.inc");
  include("xmlrpc_extension_api.inc");
}

function do_xmlrpc($request) {
   global $rpc_connect;
   $context = stream_context_create(array('http' => array('method' => "POST",'header' =>"Content-Type: text/xml",'content' => $request)));
   if ($file = @file_get_contents($rpc_connect, false, $context)) {
      $file=str_replace("i8","double",$file);
      $file = utf8_encode($file); 
      return xmlrpc_decode($file);
   } else {
      die ("<h1>Cannot connect to rtorrent :(</h1>");
   }
}

// Get full list - retrieve full list of torrents 
function get_full_list($view) {
   $request = xmlrpc_encode_request("d.multicall",
       array($view,"d.base_filename=","d.base_path=","d.bytes_done=","d.chunk_size=","d.chunks_hashed=","d.complete=","d.completed_bytes=","d.completed_chunks=","d.connection_current=","d.connection_leech=","d.connection_seed=","d.creation_date=","d.directory=","d.down.rate=","d.down.total=","d.free_diskspace=","d.hash=","d.hashing=","d.ignore_commands=","d.left_bytes=","d.local_id=","d.local_id_html=","d.max_file_size=","d.message=","d.peers_min=","d.name=","d.peer_exchange=","d.peers_accounted=","d.peers_complete=","d.peers_connected=","d.peers_max=","d.peers_not_connected=","d.priority=","d.priority_str=","d.ratio=","d.size_bytes=","d.size_chunks=","d.size_files=","d.skip.rate=","d.skip.total=","d.state=","d.state_changed=","d.tied_to_file=","d.tracker_focus=","d.tracker_numwant=","d.tracker_size=","d.up.rate=","d.up.total=","d.uploads_max=","d.is_active=","d.is_hash_checked=","d.is_hash_checking=","d.is_multi_file=","d.is_open=","d.is_private="));
   $response = do_xmlrpc($request);

   if (xmlrpc_is_fault($response)) {
       trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
   } else {
      $index=0;
      foreach($response AS $item) {
         $retarr[$index]['base_filename']=$item[0];
         $retarr[$index]['base_path']=$item[1];
         $retarr[$index]['bytes_done']=$item[2];
         $retarr[$index]['chunk_size']=$item[3];
         $retarr[$index]['chunks_hashed']=$item[4];
         $retarr[$index]['complete']=$item[5];
         $retarr[$index]['completed_bytes'] = $item[7] * $item[3]; // completed_chunks * chunk_size
         $retarr[$index]['completed_chunks']=$item[7];
         $retarr[$index]['connection_current']=$item[8];
         $retarr[$index]['connection_leech']=$item[9];
         $retarr[$index]['connection_seed']=$item[10];
         $retarr[$index]['creation_date']=$item[11];
         $retarr[$index]['directory']=$item[12];
         $retarr[$index]['down_rate']=$item[13];
         $retarr[$index]['down_total']=$item[14];
         $retarr[$index]['free_diskspace']=$item[15];
         $retarr[$index]['hash']=$item[16];
         $retarr[$index]['hashing']=$item[17];
         $retarr[$index]['ignore_commands']=$item[18];
         $retarr[$index]['left_bytes']=$item[19];
         $retarr[$index]['local_id']=$item[20];
         $retarr[$index]['local_id_html']=$item[21];
         $retarr[$index]['max_file_size']=$item[22];
         $retarr[$index]['message']=$item[23];
         $retarr[$index]['peers_min']=$item[24];
         $retarr[$index]['name']=$item[25];
         $retarr[$index]['peer_exchange']=$item[26];
         $retarr[$index]['peers_accounted']=$item[27];
         $retarr[$index]['peers_complete']=$item[28];
         $retarr[$index]['peers_connected']=$item[29];
         $retarr[$index]['peers_max']=$item[30];
         $retarr[$index]['peers_not_connected']=$item[31];
         $retarr[$index]['priority']=$item[32];
         $retarr[$index]['priority_str']=$item[33];
         $retarr[$index]['ratio']=$item[34];
         $retarr[$index]['size_bytes']=$item[36] * $item[3]; // size_chunks * chunk_size
         $retarr[$index]['size_chunks']=$item[36];
         $retarr[$index]['size_files']=$item[37];
         $retarr[$index]['skip_rate']=$item[38];
         $retarr[$index]['skip_total']=$item[39];
         $retarr[$index]['state']=$item[40];
         $retarr[$index]['state_changed']=$item[41];
         $retarr[$index]['tied_to_file']=$item[42];
         $retarr[$index]['tracker_focus']=$item[43];
         $retarr[$index]['tracker_numwant']=$item[44];
         $retarr[$index]['tracker_size']=$item[45];
         $retarr[$index]['up_rate']=$item[46];
         $retarr[$index]['up_total']=$item[7] * $item[3] * ($item[34]/1000);
         $retarr[$index]['uploads_max']=$item[48];
         $retarr[$index]['is_active']=$item[49];
         $retarr[$index]['is_hash_checked']=$item[50];
         $retarr[$index]['is_hash_checking']=$item[51];
         $retarr[$index]['is_multi_file']=$item[52];
         $retarr[$index]['is_open']=$item[53];
         $retarr[$index]['is_private']=$item[54];

         $retarr[$index]['percent_complete']=@floor(($retarr[$index]['completed_bytes'])/($retarr[$index]['size_bytes'])*100);
         $retarr[$index]['bytes_diff']=($retarr[$index]['size_bytes']-$retarr[$index]['completed_bytes']);

         if ($retarr[$index]['is_active']==0) $retarr[$index]['status_string']="Stopped";
         if ($retarr[$index]['complete']==1) $retarr[$index]['status_string']="Complete";
         if ($retarr[$index]['is_active']==1 && $retarr[$index]['connection_current']=="leech") $retarr[$index]['status_string']="Leeching";
         if ($retarr[$index]['is_active']==1 && $retarr[$index]['complete']==1) $retarr[$index]['status_string']="Seeding";
         if ($retarr[$index]['hashing']>0) {
            $retarr[$index]['status_string']="Hashing";
            $retarr[$index]['percent_complete']=@round(($retarr[$index]['chunks_hashed'])/($retarr[$index]['size_chunks'])*100);
         }
         $retarr[$index]['filemtime']=@filectime($retarr[$index]['base_path']);

         $index++;
      }
      if (isset($retarr)) {
         return $retarr;
      } else {
         return FALSE;
      }
   }
}

// Get list of files associated with a torrent...
function get_file_list($hash) {
   $globalstats=get_global_stats();
   if($globalstats['client_version']=="0.7.9") {
      $cmdarray=array($hash,"","f.completed_chunks=","f.frozen_path=","f.is_created=","f.is_open=","f.last_touched=","f.match_depth_next=","f.match_depth_prev=","f.offset=","f.path=","f.path_components=","f.path_depth=","f.priority=","f.range_first=","f.range_second=","f.size_bytes=","f.size_chunks=");
   } else {
      $cmdarray=array($hash,"","f.completed_chunks=","f.frozen_path=","f.is_created=","f.is_open=","f.last_touched=","f.match_depth_next=","f.match_depth_prev=","f.offset=","f.path=","f.path_components=","f.path_depth=","f.priority=","f.range_first=","f.range_second=","f.size_bytes=","f.size_chunks=");
   }
   $request = xmlrpc_encode_request("f.multicall",$cmdarray);
   $response = do_xmlrpc($request);
   if (xmlrpc_is_fault($response)) {
       trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
   } else {
      $index=0;
      foreach($response AS $item) {
             $retarr[$index]['completed_chunks']=$item[0];
             $retarr[$index]['frozen_path']=$item[1];
             $retarr[$index]['is_created']=$item[2];
             $retarr[$index]['is_open']=$item[3];
             $retarr[$index]['last_touched']=$item[4];
             $retarr[$index]['match_depth_next']=$item[5];
             $retarr[$index]['match_depth_prev']=$item[6];
             $retarr[$index]['offset']=$item[7];
             $retarr[$index]['path']=$item[8];
             $retarr[$index]['path_components']=$item[9];
             $retarr[$index]['path_depth']=$item[10];
             $retarr[$index]['priority']=$item[11];
             $retarr[$index]['range_first']=$item[12];
             $retarr[$index]['range_second']=$item[13];
             $retarr[$index]['size_bytes']=$item[14];
             $retarr[$index]['size_chunks']=$item[15];
             $index++;
      }
   return $retarr;
   }
}

// Get list of trackers associated with torrent...
function get_tracker_list($hash) {
   $request = xmlrpc_encode_request("t.multicall",
       array($hash,"","t.group=","t.id=","t.min_interval=","t.normal_interval=","t.scrape_complete=","t.scrape_downloaded=","t.scrape_time_last=","t.type=","t.url=","t.is_enabled=","t.is_open=","t.scrape_incomplete="));
   $response = do_xmlrpc($request);
   if (xmlrpc_is_fault($response)) {
       trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
   } else {
      $index=0;
      foreach($response AS $item) {
             $retarr[$index]['group']            =$item[0];
             $retarr[$index]['id']               =$item[1];
             $retarr[$index]['min_interval']     =$item[2];
             $retarr[$index]['normal_interval']  =$item[3];
             $retarr[$index]['scrape_complete']  =$item[4];
             $retarr[$index]['scrape_downloaded']=$item[5];
             $retarr[$index]['scrape_time_last'] =$item[6];
             $retarr[$index]['type']             =$item[7];
             $retarr[$index]['url']              =$item[8];
             $retarr[$index]['is_enabled']       =$item[9];
             $retarr[$index]['is_open']          =$item[10];
             $retarr[$index]['scrape_incomplete']=$item[11];
             $index++;
      }
   return $retarr;
   }
}
// Get list of peers associated with torrent...
function get_peer_list($hash) {
   $globalstats=get_global_stats();
   if($globalstats['client_version']=="0.7.9") {
      return array();
   } else {
      $cmdarray=array($hash,"","p.address=","p.client_version=","p.completed_percent=","p.down_rate=","p.down_total=","p.id=","p.id_html=","p.options_str=","p.peer_rate=","p.peer_total=","p.port=","p.up_rate=","p.up_total=","p.is_encrypted=","p.is_incoming=","p.is_obfuscated=","p.is_snubbed=");
   }

   $request = xmlrpc_encode_request("p.multicall",$cmdarray);
   $response = do_xmlrpc($request);
   if (xmlrpc_is_fault($response)) {
       trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
   } else {
      $index=0;
      foreach($response AS $item) {
             $retarr[$index]['address']             =$item[0];
             $retarr[$index]['client_version']      =$item[1];
             $retarr[$index]['completed_percent']   =$item[2];
             $retarr[$index]['down_rate']           =$item[3];
             $retarr[$index]['down_total']          =$item[4];
             $retarr[$index]['id']                  =$item[5];
             $retarr[$index]['id_html']             =$item[6];
             $retarr[$index]['options_str']         =$item[7];
             $retarr[$index]['peer_rate']           =$item[8];
             $retarr[$index]['peer_total']          =$item[9];
             $retarr[$index]['port']                =$item[10];
             $retarr[$index]['up_rate']             =$item[11];
             $retarr[$index]['up_total']            =$item[12];
             $retarr[$index]['is_encrypted']        =$item[13];
             $retarr[$index]['is_incoming']         =$item[14];
             $retarr[$index]['is_obfuscated']       =$item[15];
             $retarr[$index]['is_snubbed']          =$item[16];
             $index++;
      }
   if (!isset($retarr)) $retarr=array();
   return $retarr;
   }
}

// Get gloabal stats
function get_global_stats() {
   $retarr['upload_cap'] = do_xmlrpc(xmlrpc_encode_request("throttle.global_up.max_rate",array("")));
   $retarr['download_cap'] = do_xmlrpc(xmlrpc_encode_request("throttle.global_down.max_rate",array("")));
   $retarr['library_version'] = do_xmlrpc(xmlrpc_encode_request("system.library_version",array("")));
   $retarr['client_version'] = do_xmlrpc(xmlrpc_encode_request("system.client_version",array("")));
   return $retarr;
}

// Get overall download/upload rates... (Surely there's a better way of doing this!)
function get_global_rates() {
   global $downloaddir;
   $request = xmlrpc_encode_request("d.multicall",array("main","d.down.rate=","d.up.rate="));
   $response = do_xmlrpc($request);
   if (xmlrpc_is_fault($response)) {
       trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
   } else {
      $index=0;
      $totdown=0;
      $totup=0;
      foreach($response AS $item) {
         $totdown+=$item[0];
         $totup+=$item[1];
         $index++;
      }
   }
   $retarr[0]['ratedown']=$totdown;
   $retarr[0]['rateup']=$totup;
   $retarr[0]['diskspace']=@disk_free_space($downloaddir);
   return $retarr;
}

// Format no.bytes nicely...
function format_bytes($bytes) {
    if ($bytes==0) return "";
    $unim = array("B","KB","MB","GB","TB","PB");
    $c = 0;
    while ($bytes>=1024) {
        $c++;
        $bytes = $bytes/1024;
    }
    return number_format($bytes,($c ? 1 : 0),".",",")." ".$unim[$c];
}

// Function to sort second key in array (ascending)
function sort_matches_asc($left,$right) {
   global $sortkey;
   if(strtolower($left["$sortkey"])==strtolower($right["$sortkey"])) return 0;
   return strtolower($left["$sortkey"]) < strtolower($right["$sortkey"]) ? -1 : 1 ;
}

// Function to sort second key in array (descending)
function sort_matches_desc($left,$right) {
   global $sortkey;
   if(strtolower($left["$sortkey"])==strtolower($right["$sortkey"])) return 0;
   return strtolower($left["$sortkey"]) > strtolower($right["$sortkey"]) ? -1 : 1 ;
}

// Draw the percent bar using a table...
function percentbar($percent) {
   $retvar="<table align=center border=0 cellspacing=0 cellpadding=1 bgcolor=#666666 width=50><tr><td align=left>";
   $retvar.="<img src='images/percentbar.gif' height=4 width=".round($percent)." /></td></tr>";   
   $retvar.="</table>";
   return $retvar;
}

// Format ETA time
function formateta($eta) {
   if ($eta==0) return "";
   if ($eta<60) return round($eta)." sec".($eta>1 ? "s"  : "");
   if ($eta>=60 && $eta<3600) return round($eta/60)." min".(round($eta/60)>1 ? "s"  : "");
   if ($eta>=3600 && $eta<86400) return round($eta/3600)." hour".(round($eta/3600)>1 ? "s"  : "");
   if ($eta>=86400) return round($eta/86400)." day".(round($eta/86400)>1 ? "s"  : "");
}

function age($date) {
    if ($date==0) return "(File not found)";
    $periods=array("sec", "min", "hour", "day", "week", "month", "year");
    $lengths=array("60","60","24","7","4.35","12");
    $now=time();
    // is it future date or past date
    if($now > $date) {    
        $diff=$now-$date;
    } 
    for($j = 0; $diff >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $diff /= $lengths[$j];
    }
    $diff=round($diff);
    if($diff != 1) {
        $periods[$j].= "s";
    }
    return "$diff $periods[$j] old";
}


// Format Completed bytes - total bytes diff
function completed_bytes_diff($total,$completed ) {
   if ($total > $completed ) {
      $diff="- ".format_bytes($total-$completed);
   } else {
      $diff="+ ".format_bytes($completed-$total);
   }
   if ($total==$completed) $diff="";
   return $diff;
}

// Return formated (coloured) Tracker URL
function tracker_url($hash) {
   global $tracker_hilite,$tracker_hilite_default;
   $response = do_xmlrpc(xmlrpc_encode_request("t.multicall",array($hash,"","t.url=")));
   $url=@parse_url($response[0][0],PHP_URL_HOST);
   return $url;
}

// multibyte-safe replacement for wordwrap.  (See http://code.google.com/p/rtgui/issues/detail?id=71 - Thanks llamaX)
function mb_wordwrap($string, $width=75, $break="\n", $cut=false) {
    if (!$cut) {
        $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.',}\b#U';
    } else {
        $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.'}#';
    }
    $string_length = mb_strlen($string,'UTF-8');
    $cut_length = ceil($string_length / $width);
    $i = 1;
    $return = '';
    while ($i < $cut_length) {
        preg_match($regexp, $string,$matches);
        $new_string = $matches[0];
        $return .= $new_string.$break;
        $string = substr($string, strlen($new_string));
        $i++;
    }
    return $return.$string;
}
?>
