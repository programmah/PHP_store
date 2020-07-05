			<div class="span8 center-spread">			
			 	<br/><br/><br/>
				<fieldset>
				<legend><b><h1>Course Allocation By Date</h1></b></legend>
				
				<?php  
						if(count($allocationList->num_rows()) > 0)
						{ ?>
						<table class="table-striped table">
						<thead>
							<tr><th>DAY NO</th><th>EXAM DATE </th><th>VIEW</th><th>PDF</th>
						    </tr>
						</thead>
						<tbody>
						<?php 
							
							        $serial = 0;
						   
 									$aIdArr   = array();
 									//$venueArr = array();
 									//$deptArr  = array();
 									$examdate  = array();
 								   $rr = 0;
 									foreach($allocationList->result() as $row)
 									{
										//$deptArr[]  = $row->department;
             							//$venueArr[] = $row->venue;
             							$examdate[] = $row->dayDate;
             						    $aIdArr	[]	= $rr + 1;
             						   
 									}
 									
 									 if($click =="")
 									  {
 									  	$end = 30 ;
                                        $start = 0 ;
                                        $TrackClick = $click;
 									  }
 									 elseif($click =="next")
 									  {
 									  	$start = $end ;
 									    $end = $start + 30;
 									    $TrackClick = $click;
 									  } 
 									 elseif($click =="previous")
 									  {
                                        $start = $end - 30;
                                        if($end <= 30)
                                         $TrackClick = "";	
                                        else 
 									    $TrackClick = $click;
 									    
 									  } 
 									//$serial = 0;
 									 
 									for($i = $start ; $i < $end; $i++ ) //  page next previous
 									{
 									//	 do{
 										   if($i >= count($aIdArr) )
 										   	{   break; }
 						                   
 										   $count = $i + 1;  

 									
									echo '<tr>
								 		  <td>'.$count.'</td>
								 		  
								 		 <td> '.strtoupper($examdate[$i]).' </td> 
								 		 <td>'.anchor('admin/courseAllocationPreviewB/'.$examdate[$i]," View " , "['target' => '_blank']").' </td>
								 		 <td>'.anchor('admin/courseAllocationPreviewBrpt/'.$examdate[$i], " pdf ", "['target' => '_blank']").' </td>
								 		
								     </tr>';
									
 									}
 									

 									 if($start == 0)
 									 { 
									echo  anchor('admin/courseAllocationMasterList/0/?end='.$end.'&click=next&flag=next',"Next->") ;
									 }
									 elseif ($i > count($aIdArr) - 1 )
									  {
									    echo anchor('admin/courseAllocationMasterList/0/?end='.$start.'&click=previous&flag=previous',"<-Previous");
									 }
									 else
									 {
                                        echo  anchor('admin/courseAllocationMasterList/0/?end='.$end.'&click=next&flag=next',"Next->")."&nbsp;&nbsp;&nbsp;".anchor('admin/venueAllocationMasterList/0/?end='.$start.'&click=previous&flag=previous',"<-Previous") ; 
									 }



                            }
                       
							else 
								{
									echo "<p>There are no Venue Allocated yet in this app to preview!</p>";
						        }
									
                                  
                      			?>
									
						</tbody>
				</table>
				<hr/>				
				
				
				</fieldset>	
				<div id="addDept" class="modal hide fade">
		            <div class="modal-header">
		              <a class="close" data-dismiss="modal" >&times;</a>
		              <h3>List of Unassiged Department</h3>
		            </div>
		            <div class="modal-body">
		              
		              <?php
						$attributes = array('class'=>'form-horizontal'); 
						echo form_open_multipart("admin/allocateUnassigneVenueOrDept/D/?end=".$start."&click=".$TrackClick, $attributes); 
					      echo form_hidden('totalValue',count($unassignDeptListArr))
					   ?>
					   
					   <table class="table-striped table">
						<thead>
							<tr><th>SN</th><th>Department </th><th> Venue </th><th><?php  echo"Total: ".count($unassignDeptListArr); ?></th>
						    </tr>
						</thead>
						<tbody>
						<?php	
						    if(count($unassignDeptListArr > 0))
						    {
						   		  $count = 1;
						    	  foreach ( $unassignDeptListArr as $dept) 
						   			 {
						     			 echo '<tr><td>'.$count.'</td><td>'.$dept.'</td>

						          		 <td><select class="input-large" id="'."venueList".$count.'" name="'."venueList".$count.'">';


 										 foreach($VenueList as $row2)
 									        {   	
 											   echo '<option value="'.$dept.','.$row2->Name.'">'.$row2->Name.'</option>' ;
                                                
 									        }
						          		 "</select></td></tr>"; 
						           		$count++;  
						   			 }
                                 ?>
						   	   <div class="modal-footer form-actions">
			                  <?php 
							  $attr = array('id'=>'submit', 'value'=>'Update','class'=>'btn btn-success');
						      echo "<tr><td colspan=\"3\" align=\"center\">".form_submit($attr)."</td></tr>";
							  
						     ?>									            
						 
			          </div>
						<?php
							}
							else
							{
								echo '<tr><th colspan="3"> There are '.count($unassignDeptListArr).' Department Left. All Department Has Heen Allocated</th></tr>';
							}
						?>
						
					<?php echo form_close(); ?>
				 </tbody>
                     </table>
						
          		</div>
			
			</div>

				<div id="addVenue" class="modal hide fade">
		              <div class="modal-header">
		                <a class="close" data-dismiss="modal" >&times;</a>
		                <h3>List of Unassiged Venue</h3>
		            </div>
		            <div class="modal-body">
		              <table class="table-striped table">
						<thead>
							<tr><th>SN</th><th>Venue </th><th> Department </th><th><?php  echo"Total: ".count($unassignVenListArr); ?></th>
						    </tr>
						</thead>
						<tbody>
		              <?php
						$attributes = array('class'=>'form-horizontal'); 
						echo form_open_multipart("admin/allocateUnassigneVenueOrDept/V/?end=".$start."&click=".$TrackClick, $attributes); 
					    echo form_hidden('totalValue',count($unassignVenListArr))  
					   ?>
	
						
						<?php
						 $count = 1;
						    if(count($unassignVenListArr) > 0 )
						    {
						    
						   		   foreach ( $unassignVenListArr as $venue) 
						    	    	{
						     		    	 echo "<tr><td>".$count."</td><td>".$venue."</td>"; 

						     		    echo '<td><select class="input-small" id="'."deptCodeList".$count.'" name="'."deptCodeList".$count.'">';
								 		  	foreach($DeptList as $row2)
 									        { 
 												   echo '<option value="'.$venue.','.$row2->DeptCode.'" selected> '.$row2->DeptCode.'</option>' ;                                                                                        
 									        }
 									      
								 		echo  "</select></td></tr>";
						          		     $count++;
						          	    } 
						          
                               ?>
						      <div class="modal-footer form-actions">
			                   <?php 
							      $attr = array('id'=>'submit', 'value'=>'Update','class'=>'btn btn-success');
						         echo "<tr><td colspan=\"3\" align=\"center\">".form_submit($attr)."</td></tr>";
							      
							     
						      ?>									            
						 
			               </div> 
				<?php    }
						    else
						    {
						    	echo '<tr><th colspan="3"> There are '.count($unassignVenListArr).' Venues Left. All Venue Has Heen Allocated</th></tr>';
						    }
						
						?>
						


						<?php echo form_close(); ?>
                   </tbody>
					<table>	
          		</div>
			
			</div>
		

			