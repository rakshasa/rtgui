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

function ajax(view) { 
   var req = null; 
   if (window.XMLHttpRequest) {
      req = new XMLHttpRequest();
      if (req.overrideMimeType) {
         req.overrideMimeType('text/plain');
      }
   } else if (window.ActiveXObject) {
      try {
         req = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (e) {
         try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
         } catch (e) {}
      }
   }

   req.onreadystatechange = function() { 
      if(req.readyState == 4) {
         if(req.status == 200) {
            resp = eval( "(" + req.responseText + ")" );
            i=0;
            while (i < resp.total) {
               div = eval ("resp.change"+i+"['div']");
               val = eval ("resp.change"+i+"['val']");
               if (!document.getElementById(div)) {
                  document.location='index.php?reload=2';
               }
               document.getElementById(div).innerHTML  = val;
               i+=1;
            }
         } else {
            document.write="Javascript Ajax Error: returned status code " + req.status + " " + req.statusText;
         }	
      } 
   }; 
   req.open("GET", "json.php?view="+view, true); 
   req.send(null); 
}

function checkAll(field) {
   for (i = 0; i < field.length; i++)
	   field[i].checked = true ;
}

function uncheckAll(field) {
   for (i = 0; i < field.length; i++)
	   field[i].checked = false ;
}

function toggleLayer( whichLayer ) {
  var elem, vis;
  if( document.getElementById ) 
    elem = document.getElementById( whichLayer );
  else if( document.all ) 
      elem = document.all[whichLayer];
  else if( document.layers )
    elem = document.layers[whichLayer];
  vis = elem.style;
  if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
    vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
  vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}
