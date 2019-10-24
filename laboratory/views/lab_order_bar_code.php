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
Date : June 2016
Author: Mr. Jayanath Liyanage   jayanathl@icta.lk

Programme Manager: Shriyananda Rathnayake
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/

	include("header.php");	///loads the html HEAD section (JS,CSS)
?>
<?php echo Modules::run('menu'); //runs the available menu option to that usergroup ?>
<div class="container" style="width:95%;">
	<div class="row" style="margin-top:55px;">
	  <div class="col-md-2 ">
	  </div>
		<div class="col-md-4" >
		<div class="panel panel-default"  >
			<div class="panel-heading"><b>Lab order</b>
			</div>
				<div style="padding:10px;">
				<?php
					echo "Patient : ".$patient_info["Personal_Title"].' '.$patient_info["Full_Name_Registered"].' ' .$patient_info["Personal_Used_Name"]."<br>";
					echo "HIN : ".$patient_info["HIN"]."<br>";
					echo "Test : <b>".$order_info["TestGroupName"]."</B><br>";
					echo "Order date : ".$order_info["OrderDate"]."<br>";
					echo "Doctor : ".$order_info["OrderBy"]. "<br>";
					echo "<b>ORDER NO : ".$order_info["LAB_ORDER_ID"]. "<hr>";
                    echo "<a class='btn btn-default' onclick=\"openWindow('" . site_url(
                    "report/pdf/order/print/".$order_info["LAB_ORDER_ID"]
                ) . "')\" href='#'>Print </a>";
				?>


<!--                    <button type="button" onclick="window.location=--><?php //echo site_url("report/pdf/patientSlip/print/".$this->uri->segment(3)); ?><!--" class="btn btn-primary">Print token</button>-->
				<?php	if($order_info["Dept"]=='OPD') { 
                                    echo '<a  class="btn btn-default" href="'.site_url("search/opd_sample_order").'">Back to List</a>';
                                }
                                	else if($order_info["Dept"]=='CLN') {
                                echo  '<a  class="btn btn-default" href="'.site_url("search/clinic_sample_order").'"?>">Back to Clinic list</a>';
                                }
                                else if($order_info["Dept"]=='ADM') {
                                    $wid=$adm_info["Ward"];
                                echo  '<a  class="btn btn-default" href="'.site_url("search/adm_sample_order/$wid").'"?>Back to list</a>';
                                }
                                
                                ?>
				</div>
			</div>
		</div>
	</div>
</div>

