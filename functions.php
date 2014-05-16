<?php
//
// rtGui - Copyright Simon Hall 20007
//
// http://rtgui.googlecode.com/
//
function do_xmlrpc($request) {
   global $rpc_connect;
   $context = stream_context_create(array('http' => array('method' => "POST",'header' =>"Content-Type: text/xml",'content' => $request)));
   $file = file_get_contents($rpc_connect, false, $context);
   $file=str_replace("i8","double",$file);
   return xmlrpc_decode($file);
}

// Get full list - retrieve full list of torrents 
function get_full_list($view) {
   $request = xmlrpc_encode_request("d.multicall",
       array($view,"d.base_filename=","d.base_path=","d.bytes_done=","d.chunk_size=","d.chunks_hashed=","d.complete=","d.completed_bytes=","d.completed_chunks=","d.connection_current=","d.connection_leech=","d.connection_seed=","d.creation_date=","d.directory=","d.down_rate=","d.down_total=","d.free_diskspace=","d.hash=","d.hashing=","d.ignore_commands=","d.left_bytes=","d.local_id=","d.local_id_html=","d.max_file_size=","d.message=","d.peers_min=","d.name=","d.peer_exchange=","d.peers_accounted=","d.peers_complete=","d.peers_connected=","d.peers_max=","d.peers_not_connected=","d.priority=","d.priority_str=","d.ratio=","d.size_bytes=","d.size_chunks=","d.size_files=","d.skip_rate=","d.skip_total=","d.state=","d.state_changed=","d.tied_to_file=","d.tracker_focus=","d.tracker_numwant=","d.tracker_size=","d.up_rate=","d.up_total=","d.uploads_max=","d.is_active=","d.is_hash_checked=","d.is_hash_checking=","d.is_multi_file=","d.is_open=","d.is_private="));

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
         $retarr[$index]['completed_bytes']=$item[6];
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
         $retarr[$index]['size_bytes']=$item[35];
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
         $retarr[$index]['up_total']=$item[47];
         $retarr[$index]['uploads_max']=$item[48];
         $retarr[$index]['is_active']=$item[49];
         $retarr[$index]['is_hash_checked']=$item[50];
         $retarr[$index]['is_hash_checking']=$item[51];
         $retarr[$index]['is_multi_file']=$item[52];
         $retarr[$index]['is_open']=$item[53];
         $retarr[$index]['is_private']=$item[54];

			$retarr[$index]['percent_complete']=@round(($retarr[$index]['completed_bytes'])/($retarr[$index]['size_bytes'])*100);
			if ($retarr[$index]['is_active']==0) $retarr[$index]['status_string']="Stopped";
      	if ($retarr[$index]['complete']==1) $retarr[$index]['status_string']="Complete";
			if ($retarr[$index]['is_active']==1 && $retarr[$index]['connection_current']=="leech") $retarr[$index]['status_string']="Downloading";
			if ($retarr[$index]['is_active']==1 && $retarr[$index]['connection_current']=="seed") $retarr[$index]['status_string']="Seeding";
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
   $request = xmlrpc_encode_request("f.multicall",
       array($hash,"","f.completed_chunks=","f.frozen_path=","f.is_created=","f.is_open=","f.last_touched=","f.match_depth_next=","f.match_depth_prev=","f.offset=","f.path=","f.path_components=","f.path_depth=","f.priority=","f.range_first=","f.range_second=","f.size_bytes=","f.size_chunks="));
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
       array($hash,"","t.group=","t.id=","t.min_interval=","t.normal_interval=","t.scrape_complete=","t.scrape_downloaded=","t.scrape_time_last=","t.type=","t.url=","t.is_enabled=","t.is_open="));
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
             $index++;
      }
   return $retarr;
   }
}

// Get upload/download cap
function get_global_stats() {
   $retarr['upload_cap'] = do_xmlrpc(xmlrpc_encode_request("throttle.global_up.max_rate",array("")));
   $retarr['download_cap'] = do_xmlrpc(xmlrpc_encode_request("throttle.global_down.max_rate",array("")));
   $retarr['diskspace'] = do_xmlrpc(xmlrpc_encode_request("directory.default",array("")));
   $retarr['library_version'] = do_xmlrpc(xmlrpc_encode_request("system.library_version",array("")));
   $retarr['client_version'] = do_xmlrpc(xmlrpc_encode_request("system.client_version",array("")));
   return $retarr;
}

// Format no.bytes nicely...
function format_bytes($bytes) {
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

function percentbar($percent) {
   $retvar="<table border=0 cellspacing=0 cellpadding=1 bgcolor=#666666 width=50px><tr><td align=left>";
   $retvar.="<img src='images/percentbar.gif' height=4px width=".$percent."px></td></tr>";   
   $retvar.="</table>\n";
   return $retvar;
}
?>