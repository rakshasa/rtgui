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

include "config.php";
include "functions.php";

$count=0;

function startElement($parser, $name, $attrs) {
	global $insideitem, $tag, $title, $description, $link, $pubdate, $category;
	if ($insideitem) {
		$tag = $name;
	} elseif ($name == "ITEM") {
		$insideitem = true;
	}
}

function endElement($parser, $name) {
	global $insideitem, $tag, $title, $description, $link, $category, $pubdate, $count, $ddl;
	if ($name == "ITEM") {
      echo "<p class='feeditem' align='left'>\n";
      
      echo "<a href=\"javascript:toggleLayer('desc".$count."');\"><img src='images/view.gif'></a>\n";

      if ($ddl) {
         echo "<a href='control.php?addurlsub=1&amp;addurl=".addslashes(trim($link))."'><img src='images/addtorrent.gif' alt='Download torrent'></a>";
      } else {
         echo "<a href='".addslashes(trim($link))."' target='_blank'><img src='images/weblink.gif' alt='Visit web link'></a>";
      }
      
      echo "&nbsp;<a href=\"javascript:toggleLayer('desc".$count."');\">".htmlspecialchars(trim($title))."</a>\n";
      if (trim($category!="")) echo " (".$category.")";
      echo "<div class='togglevis' id='desc".$count."' align='left'><b>".$pubdate."</b><br>".trim($description)."</div>\n";
      echo "</p>\n";
		$title="";
		$description="";
		$link="";
      $category="";
      $pubdate="";
		$insideitem=false;
      $count++;
	}
}

function characterData($parser, $data) {
	global $insideitem, $tag, $title, $description, $link, $pubdate, $category;
	if ($insideitem) {
	switch ($tag) {
		case "TITLE":
		$title .= $data;
		break;
      case "DESCRIPTION":      
		$description .= $data;
		break;
      case "COMMENTS":       
		$description .= $data;
		break;
      case "PUBDATE":       
		$pubdate.=$data;
		break;
		case "LINK":
		$link .= $data;
		break;
		case "CATEGORY":
		$category .= $data;
		break;
	}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="favicon.ico" />
<title>rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="rtgui.js"></script>
</head>
<body>

<div id=header>
<h1><a href='index.php'>rt<span class=green>gui</span></a></h1>
</div>

<?php
echo "<p>This feed reader feature of rtGui is 'work-in-progress' - please don't report bugs regarding it!</p>";

echo "<ul class='feedlistul'>\n";
foreach($feeds as $feed) {
   if (trim($feed[1])!="") {
      echo "<li class='feedlist'><a href='#".$feed[1]."'>".$feed[0]."</a></li>";
   }
}
echo "</ul>";

foreach($feeds as $feed) {
   echo "<div class='feedtitle' id='".$feed[1]."'>";
   echo "<h2>".$feed[0]."</h2>\n";
   echo "<p class='smalltext'>".$feed[1]."</p>";
   echo "</div>\n";

   $insideitem = false;
   $tag="";
   $title="";
   $description="";
   $link="";
   $pubdate="";
   $category="";
   $ddl=$feed[2];

   $xml_parser = xml_parser_create();
   xml_set_element_handler($xml_parser, "startElement", "endElement");
   xml_set_character_data_handler($xml_parser, "characterData");
   if (!($fp = @fopen($feed[1],"r"))) {
      echo "Couldn't read URL";
   } else {
      while ($data = fread($fp, 4096)) {
      	if (!xml_parse($xml_parser, $data, feof($fp))) {
            sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
         }
      }
      fclose($fp);
      xml_parser_free($xml_parser);
   }
   ob_flush();
}
?>

</body>
</html>
