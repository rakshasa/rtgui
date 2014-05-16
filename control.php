<?php
//
// rtGui - Copyright Simon Hall 20007
//
// http://rtgui.googlecode.com/
//

include "functions.php";
include "config.php";
import_request_variables("gp","r_");

if (isset($r_setmaxup) || isset($r_setmaxdown)) $r_cmd="setcap";
if (isset($r_addurl)) $r_cmd="addurl";

// Set file priorities...
if (isset($r_set_fpriority)) {
   $index=0;
   foreach($r_set_fpriority as $item) {
      $response=do_xmlrpc(xmlrpc_encode_request("f.set_priority",array("$r_hash",$index,"$item")));
      $index++;
   }
   $r_cmd="";
}

// Set torrent priorities...
if (isset($r_set_tpriority)) {
   $index=0;
   foreach($r_set_tpriority as $item) {
      $response=do_xmlrpc(xmlrpc_encode_request("d.set_priority",array($r_hash[$index],$item)));
      $index++;
   }
   $r_cmd="";
}

switch($r_cmd) {
   case "stop":
      $response = do_xmlrpc(xmlrpc_encode_request("d.stop",array("$r_hash")));
      break;
   case "start":
      $response = do_xmlrpc(xmlrpc_encode_request("d.start",array("$r_hash")));
      break;
   case "delete":
      $response = do_xmlrpc(xmlrpc_encode_request("d.erase",array("$r_hash")));
      break;
   case "hashcheck":
      $response = do_xmlrpc(xmlrpc_encode_request("d.check_hash",array("$r_hash")));
      break;
   case "setcap":
      $response = do_xmlrpc(xmlrpc_encode_request("set_upload_rate",array("$r_setmaxup")));    
      $response = do_xmlrpc(xmlrpc_encode_request("set_download_rate",array("$r_setmaxdown")));
      break;
   case "addurl":
      $response = do_xmlrpc(xmlrpc_encode_request("load_start",array("$r_addurl")));    
      break;
}
$referer=parse_url($_SERVER['HTTP_REFERER']);
$script=basename($referer['path']);
if (($script!='index.php' && $script!='view.php') || $r_cmd=="delete" ) $script='index.php';
header("Location: $script?".$referer['query']);
?>
