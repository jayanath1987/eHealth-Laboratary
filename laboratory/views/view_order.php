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
with this program. If not, see <http://www.gnu.org/licenses/> 
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
	
	<div class="col-md-10 " >
		<?php
			if (isset($PID)){
				echo Modules::run('patient/banner',$PID);
			}
		?>
		<div class="panel panel-default  "  style="padding:2px;margin-bottom:1px;" >
			<div class="panel-heading" ><b>Lab order</b></div>
			<div class="" style="margin-bottom:1px;padding-top:8px;">
			<?php 
					echo '<table class="table table-condensed table-hover" style="margin-bottom:0px;">';
						echo '<tr>';
							echo '<td>';
								if (isset($opd_lab_info["Status"])){
									echo 'Status : <b>'.$opd_lab_info["Status"].'</b>';
								}
							echo '</td>';						
							echo '<td>';
							echo '</td>';
							echo '<td>';							
								if (isset($opd_lab_info["CreateDate"])){
									echo 'Order Date : <b>'.$opd_lab_info["CreateDate"].'</b>';
									
								}
							echo '</td>';
						echo '</tr>';	
						echo '<tr>';
							echo '<td>';
								echo '<b>Priority:</b>';
								if (isset($oreder_info)){
									echo $oreder_info["Priority"];
								}
							echo '</td>';						
							echo '<td >';							
								echo '<b>Test group:</b>';
								if (isset($oreder_info)){
									echo $oreder_info["TestGroupName"];
								}
							echo '</td>';
							if (isset($oreder_info)){
								echo '<td >';
									echo 'Order Date : '.$oreder_info["OrderDate"] ;
								echo '</td >';
							}	
						echo '</tr>';
					echo '</table>';		
		
			?>
			<form id="order_form" method="POST" action="<?php 
			?>" >
				<div id="lab_block" >
					<div class="panel panel-default  "  style="padding:2px;margin-bottom:1px;" >
						<div class="panel-heading" id="test_head"><b>Tests</b></div>
						<div id="lab_cont">
							<?php	
								//print_r($orederd_test_list);
								if (($mode=="view")||($mode=="process")){
									if (!empty($orederd_test_list)){
										$i=1;
										echo '<table class="table">';
										echo '<tr>';
											echo '<th width=15px>';
												echo "#";
											echo '</th>';
											echo '<th >';
												echo "Name";
											echo '</th>';
											echo '<th >';
												echo "Result";
											echo '</th>';
											echo '<th>';
												echo "Ref. value";
											echo '</th>';
                                            echo '<th>';
												echo "Print";
											echo '</th>';
										echo '</tr>';											
										foreach($orederd_test_list as $key=>$value) {
											echo '<tr>';
												echo '<td width=15px>';
													echo $i;
												echo '</td>';	
												echo '<td>';
													echo $value["Name"];
												echo '</td>';	
												if ($value["Status"] == "Pending"){
													if ($mode=="process"){
														echo '<td>';
															echo '<textarea id="'.$value["LAB_ORDER_ITEM_ID"].'"  name="'.$value["LAB_ORDER_ITEM_ID"].'"   cols=40 rows=4></textarea>';
														echo '</td>';
														echo '<td>';
															echo $value["RefValue"];
														echo '</td>';
                                                        echo '<td>';
                                                        echo '<input type="checkbox" name="print[]" id="print" value="'.$value["LAB_ORDER_ITEM_ID"].'" />';
                                                        echo '</td>';
													}
													else{
														echo '<td>';
															echo '<b class="blink_me" style="color:red">Pending .....</b>';
														echo '</td>';
                                                        echo '<td></td>';
                                                        echo '<td>';
                                                        echo '<input type="checkbox" name="print[]" id="print" value="'.$value["LAB_ORDER_ITEM_ID"].'" />';
                                                        echo '</td>';
													}
												}
												else{
													echo '<td>';
														echo $value["TestValue"];
													echo '</td>';				
													echo '<td>';
														echo $value["RefValue"];
													echo '</td>';
                                                    echo '<td>';
                                                    echo '<input type="checkbox" name="print[]" id="print" value="'.$value["LAB_ORDER_ITEM_ID"].'" />';
													echo '</td>';
												}
											echo '</tr>';
											$i++;
										}
										echo '</table>';
										echo '<center>';
        if (isset($oreder_info) && ($oreder_info["Status"] == "Available")) {
            if(isset($oreder_info["ResultSeenBy"])){
                echo '<b>Result seen by ' . $oreder_info["ResultSeenBy"]. '        at        ' .$oreder_info["ResultSeenTime"];
            }
 else {
            echo '<button onclick=result_seen("' . $oreder_info["LAB_ORDER_ID"] . '","' . $oreder_info["OBJID"] . '") type="button" id="btn_by_group" class="btn btn-primary btn-sm">Result seen by ' . $this->session->userdata("FullName") . '</button>';
 }
            echo '&nbsp;&nbsp;<a href="' . site_url($_GET["CONTINUE"]) . '">Back to list</a>';
        } else {
            if ($mode == "process") {
                echo '<input type="checkbox" class=""   id="confirm"> <b>Result confirmed by ' . $this->session->userdata("FullName") . '</b><br>';
                echo '<button onclick=result_save() type="button" id="btn_by_group" class="btn btn-success btn-sm">Save Result</button>';
                echo '&nbsp;&nbsp;<a href="' . site_url($_GET["CONTINUE"]) . '">Back to list</a>';
            } else {
                echo '&nbsp;&nbsp;<a href="' . site_url($_GET["CONTINUE"]) . '">Back to list</a>';
            }
        }
                                            echo "<button onclick=\"printLabTests('" . site_url("report/pdf/printLabTests/print/") . "')\"  type='button' class=\"btn btn-primary btn-sm\"  >Print</button>";
                                            echo "<script language=\"javascript\">\n";
                                            echo "function printLabTests(url){\n";
                                            echo "    var data='?';\n";
                                            echo "    $.each( $(\"#print:checked\" ), function( key, value ) {\n";
                                            echo "        data+='print[]='+$(this).val()+'&';\n";
                                            echo "    });\n";
                                            echo "    var pId=$PID;\n";
                                            echo "    data+='pid='+pId;\n";
                                            echo "    var params = \"menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,width=750,height=700\";\n";
                                            echo "    window.open(url + data, \"lookUpW\",params);\n";
                                            echo "}\n";
                                            echo "</script>\n";
											echo '</center>';
									}
								}
							?>
						</div >
					</div>
				</div>	
			</form>
			</div>
		</div>	
	</div>

	</div>
</div>

<script language="javascript">
    
        function result_seen(lab_order_id,objid) {
       var request = $.ajax({
            url: "<?php echo base_url(); ?>index.php/laboratory/result_seen/" + lab_order_id,
            type: "post"
        });
        request.done(function (response, textStatus, jqXHR) {
            window.location.href = "<?php echo base_url(); ?>index.php/opd/view/" + objid;
            
            });
    }
    </script>
