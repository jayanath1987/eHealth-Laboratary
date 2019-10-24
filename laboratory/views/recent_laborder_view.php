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

	include("header.php");	///loads the html HEAD section (JS,CSS)
?>
<?php echo Modules::run('menu'); //runs the available menu option to that usergroup ?>
<div class="container" style="width:95%;">
	<div class="row" style="margin-top:55px;">
	<div class="col-md-2 " ></div>
	<div class="col-md-10 " >
		<?php
			if (isset($PID)){
				echo Modules::run('patient/banner_full',$PID);
			}
		?>
		<div class="panel panel-default  "  style="padding:2px;margin-bottom:1px;" >
			<div class="panel-heading" ><b>Latest Lab orders</b></div>
			<div class="" style="margin-bottom:1px;padding-top:8px;">
				<?php
					//var_dump($lab_list);
					echo '<table class="table table-hover"  style="font-size:14px;margin-bottom:0px;">';
					for ($i=0;$i<count($lab_list); ++$i){
						echo '<tr >';
						echo '<td width=10px>';
						echo '' . $lab_list[$i]["Dept"] . '';
						echo '</td>';
						echo '<td>';
								echo $lab_list[$i]["OrderDate"];
						echo '</td>';
						echo '<td>';
							//echo '<a title="Click here to view the lab test result" href="'.site_url("laboratory/order/".$lab_list[$i]["LAB_ORDER_ID"]).'?CONTINUE=laboratory/view/'.$PID.'" >';
								echo '<b>'.$lab_list[$i]["TestGroupName"].'</b>';
							//echo '</a>';		
						echo '</td>';
						echo '<td>';
						if ($lab_list[$i]["Status"] =="Pending"){
							echo '<span class=" blink_me" style="color:red">'.$lab_list[$i]["Status"].'...</span>';
						}
						else{
							echo '<span class=" label label-success">'.$lab_list[$i]["Status"].'</span>';
						}
						echo '</td>';
						echo '<td>';
							if ($lab_list[$i]["Status"] =="Pending"){
								echo '<a href="' .site_url('laboratory/process_result/'.$lab_list[$i]["LAB_ORDER_ID"].'?CONTINUE=laboratory/view/'.$PID.''). '" class="btn btn-xs btn-primary"  default="" ">Process</a>';
							}
							else{
								echo '<a href="'.site_url("laboratory/order/".$lab_list[$i]["LAB_ORDER_ID"]).'?CONTINUE=laboratory/view/'.$PID.'" class="btn btn-xs btn-success"  default="" ">View</a>';
							}
						echo '</td>';
						echo '</tr>';
					}
					echo '</table>';					
				?>
			</div>
		</div>	
	</div>

	</div>
</div>
