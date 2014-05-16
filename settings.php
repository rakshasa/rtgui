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
$execstart=$start=microtime(true);
include "functions.php";
include "config.php";
import_request_variables("gp","r_");

if (!isset($_SESSION['refresh'])) $_SESSION['refresh']=$defaultrefresh;
if (isset($r_setrefresh)) $_SESSION['refresh']=$r_setrefresh;

if (isset($r_setmaxup) || isset($r_setmaxdown)) {
   $response = do_xmlrpc(xmlrpc_encode_request("set_upload_rate",array("$r_setmaxup")));    
   $response = do_xmlrpc(xmlrpc_encode_request("set_download_rate",array("$r_setmaxdown")));
}

$globalstats=get_global_stats();

if (isset($r_submit)) {
   echo "<script>window.top.location='index.php?reload=1';</script>";
   die();
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
<body >
<div class='modal'>
<?php
echo "<form method='post' action='settings.php'>\n";
echo "<h3>Settings</h3>";
echo "<p>&nbsp;</p>";
echo "<p><label for='setrefresh'>Refresh interval:</label>";
echo "<select name='setrefresh' id='setrefresh'>\n";
foreach (array(0=>"Off",2000=>"2 secs",5000=>"5 secs",10000=>"10 secs",20000=>"20 secs",30000=>"30 secs",60000=>"1 min",300000=>"5 mins",60000=>"10 mins") as $ms=>$txt) {
   echo "<option value=$ms ".($_SESSION['refresh']==$ms ? "selected" : "").">$txt</option>\n";
}
echo "</select>\n";
echo "</p>\n";
echo "<p>&nbsp;</p>";
echo "<p><label>Download limit:</label>";
echo "<select name='setmaxdown' class='download'>\n";
if ( !in_array(($globalstats['download_cap']/1024),$defspeeds) && $globalstats['download_cap']>0 ) echo "<option value='".$globalstats['download_cap']."' selected>".format_bytes($globalstats['download_cap'])."</option>";
echo "<option value='0' ".($globalstats['download_cap']==0 ? "selected" : "").">-Unlimited-</option>\n";
foreach ($defspeeds AS $i) {
   $x=($i*1024);
   echo "<option value='$x' ";
   if ($x==$globalstats['download_cap']) echo "selected";
   echo ">".format_bytes($x)."</option>\n";
}
echo "</select>";
echo "</p>";
echo "<p>&nbsp;</p>";
echo "<p><label for='setmaxup'>Upload limit:</label>";
echo "<select name='setmaxup' id='setmaxup' class='upload'>\n";
if ( !in_array(($globalstats['upload_cap']/1024),$defspeeds) && ($globalstats['upload_cap']>0) ) echo "<option value='".$globalstats['upload_cap']."' selected>".format_bytes($globalstats['upload_cap'])."</option>";
echo "<option value='0' ".($globalstats['upload_cap']==0 ? "selected" : "").">-Unlimited-</option>\n";
foreach ($defspeeds AS $i) {
   $x=($i*1024);
   echo "<option value='$x' ";
   if ($x==$globalstats['upload_cap']) echo "selected";
   echo ">".format_bytes($x)."</option>\n";
}
echo "</select>\n";
echo "</p>\n";
echo "<p>&nbsp;</p>";
echo "<p>&nbsp;</p>";
echo "<p><input type='reset' onclick=\"parent.window.location='index.php';\" value='Cancel' />&nbsp;<input type='submit' name='submit' value='Save' /></p>\n";
echo "</form>\n";
?>
</div>
</body>
</html>
