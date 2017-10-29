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

extract($_REQUEST, EXTR_PREFIX_ALL|EXTR_REFS, 'r');
if ($r_dir=="" || !isset($r_dir)) $r_dir="/";
if (!isset($r_hilitedir)) $r_hilitedir="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<?php
echo "<body class='dirbrowse'  onLoad=\"window.parent.document.getElementById('seldir').innerHTML='".htmlentities($r_dir,ENT_QUOTES,"UTF-8")."';\" >\n";
echo "<div align='left'>\n";
if ($r_dir!="/" ) echo "<a href='dirbrowser.php?dir=".urlencode(substr($r_dir,0,strrpos($r_dir,"/")))."&amp;hilitedir=".urlencode($r_hilitedir)."'>[..]</a><br>";

$files=array();
if ($dirarray=@scandir($r_dir)) {
   foreach($dirarray as $file) {
      if ($r_dir=="/") {
         $truedir=$r_dir.$file;
      } else {
         $truedir=$r_dir."/".$file;
      }
      if ($file!="." && $file!="..") {
         if (is_dir($truedir)) {
            if (substr($r_hilitedir,1)==$file) echo "<b>";
            echo "<img src='images/folder.gif'> <a href='dirbrowser.php?dir=".urlencode($truedir)."&amp;hilitedir=".urlencode($r_hilitedir)."'>".htmlentities($file,ENT_QUOTES,"UTF-8")."</a><br>";
            if (substr($r_hilitedir,1)==$file) echo "</b>";
         } else {
            $files[]=$file;
         }
      }
   }
   foreach($files as $file) {
      echo "<img src='images/file.gif'> ".htmlentities($file,ENT_QUOTES,"UTF-8")."<br>\n";
   }
} else {
   echo "!Invalid directory!";
}
   ?>
</div>
</body>
</html>
