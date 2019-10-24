<?php
/*
--------------------------------------------------------------------------------
HHIMS - Hospital Health Information Management System
Copyright (c) 2011 Information and Communication Technology Agency of Sri Lanka
<http: www.hhims.org/>
----------------------------------------------------------------------------------
This program is free software: you can redistribute it and/or modify it under the
terms of the GNU Affero General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,but WITHOUT ANY 
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License along 
with this program. If not, see <http://www.gnu.org/licenses/> or write to:
Free Software  HHIMS
ICT Agency,
160/24, Kirimandala Mawatha,
Colombo 05, Sri Lanka
---------------------------------------------------------------------------------- 
Author: Author: Mr. Jayanath Liyanage   jayanathl@icta.lk
                 
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
echo "\n<html xmlns='http://www.w3.org/1999/xhtml'>";
echo "\n<head>";
echo "\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>";
echo "\n<title>".$this->config->item('title')."</title>";
echo "\n<link rel='icon' type='". base_url()."image/ico' href='images/mds-icon.png'>";
echo "\n<link rel='shortcut icon' href='". base_url()."images/mds-icon.png'>";
echo "\n<link href='". base_url()."/css/mdstheme_navy.css' rel='stylesheet' type='text/css'>";
echo "\n<script type='text/javascript' src='". base_url()."js/jquery.js'></script>";
echo "\n    <script type='text/javascript' src='".base_url()."js/bootstrap/js/bootstrap.min.js' ></script>";
echo "\n    <link href='". base_url()."js/bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css' />";
echo "\n    <link href='". base_url()."js/bootstrap/css/bootstrap-theme.min.css' rel='stylesheet' type='text/css' />";  
echo "\n<script type='text/javascript' src='". base_url()."/js/mdsCore.js'></script> ";
echo "\n</head>";
	
?>
<body onclick="$('#lid').focus();" >
    <div class="" >
        <center>
            <h1>LAB SAMPLE RECEIVE</h1><hr>
            <div>
                <h4>---------Scan the barcode here----------</h4>
                <input id="lid"  type="text" class="input input-lg" style="background:#ccc;width:400px;" autofocus />
            </div>
			<div id ="err_msg" style="color:red">
			</div>
        </center>
    </div>
</body>
</html>
<script>
var token_win = null;
var ajax_url = "laboratory/update_sample";
$(function(){
    $(document).click(function(){$('#lid').focus();});
    $("body").mouseover(function(){$('#lid').focus();});
    $("#lid").keypress(
        function(e) {
            if(e.which == 13) {
                var res = $("#lid").val();
                var lid = Number(res);  
                var request = $.ajax({
                    url:ajax_url,
                    type: "post",
                    data:{"lid":lid}
                });
                clear();
                request.done(function (response, textStatus, jqXHR){
                  
                  if (response>0){ 
                    token_win = window.open('','','width=100,height=100');
                    token_win.document.write('Received');
                    token_win.focus();
                    setTimeout(function(){
                       token_win.close();
                    }, 1000); //1 seconds
                    clear();
              
                   
                  }
                   else if  (response ==-2){
                        $("#err_msg").html("Sample already Received!");
                   }
				   else if  (response ==-3){
                        $("#err_msg").html("Sample not found!");
                   }
				   else{
				      $("#err_msg").html("Input error/Invalid ID!");
				   }
                }); 
               
            }
			$("#err_msg").html("");
    });

});
function clear(){
        $("#lid").val("");
        $("#err_msg").html("");
}
</script>
