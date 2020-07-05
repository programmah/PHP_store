			<div class="span8 center-spread">			
			 				<br/><br/><br/>
				<fieldset>
				<legend><h2>Venue Allocation Preview</h2></legend>
				
				<?php  
						if(count($allocationList->num_rows()) > 0)
						{ ?>
						<table class="table-striped table">
						<thead>
							<tr><th>SN</th><th>Department </th><th> Venue </th><th> Available Space </th>
						    </tr>
						</thead>
						<tbody>
						<?php 
							
							        $serial = 0;
						   
 									$aIdArr   = array();
 									$venueArr = array();
 									$deptArr  = array();
 								   
 									foreach($allocationList->result() as $row)
 									{
										$deptArr[]  = $row->department;
             							$venueArr[] = $row->venue;
             							$capacity[] = $row->venueCapacity;
             						    $aIdArr	[]	= $row->vID;
             						   
 									}
 									 if($click =="")
 									  {
 									  	$end = 20 ;
                                        $start = 0 ;
                                        $TrackClick = $click;
 									  }
 									 elseif($click =="next")
 									  {
 									  	$start = $end ;
 									    $end = $start + 20;
 									    $TrackClick = $click;
 									  } 
 									 elseif($click =="previous")
 									  {
                                        $start = $end - 20;
                                        if($end <= 20)
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

 									 if($allo_edit == $aIdArr[$i] )   // editing
								        {

								     	$attributes = array('class'=>'form-horizontal'); 
					            	 echo form_open_multipart("admin/updateMasterAllocation/".$aIdArr[$i]."/?end=".$start."&click=".$TrackClick, $attributes);
								 	 echo form_hidden('cid',$aIdArr[$i]) ;
								 	 echo form_hidden('oldDeptID',$Dvalue) ;
								 	 echo form_hidden('oldVenueID',$Vvalue) ;
																		 	 
								 	$attr = array('id'=>'submit', 'value'=>'Update','class'=>'btn btn-success');
											   echo "<tr>
										  <td>$count</td>
										  
								 		  <td><select class=\"input-small\" id=\"deptCodeList\" name=\"deptCodeList\">";
								 		  	foreach($DeptList as $row2)
 									        {
 									        	 if($row2->DeptCode == $Dvalue)
 									        	 	{ 
 												      echo '<option value="'.$row2->DeptCode.'" selected> '.$row2->DeptCode.'</option>' ;
                                                    }
                                                else
                                                     echo '<option value="'.$row2->DeptCode.'"> '.$row2->DeptCode.'</option>' ;
 									    

 									        }
 									      
								 		echo  "</select></td>
								           <td><select class=\"input-large\" id=\"venueCodeList\" name=\"venueCodeList\">
									        ";
									        foreach($VenueList as $row2)
 									        {
 									        	 if($row2->Name == $Vvalue)
 									        	 	{  
 												      echo '<option value="'.$row2->Name.'" selected> '.$row2->Name.'</option>' ;
                                                    }
                                                else
                                                     echo '<option value="'.$row2->Name.'"> '.$row2->Name.'</option>' ;
 									    

 									        } 
 									        echo " </select><td colspan=2>". form_submit($attr)."</td><td>";
 									 if($start == 0)
 									 { 
									echo  anchor('admin/venueAllocationMasterList/0/?end=0&click=next',"Cancle") ;
									 }
									 elseif ($i > count($aIdArr) - 1 )
									  {
									    echo anchor('admin/venueAllocationMasterList/0/?end='.($start).'&click=previous',"Cancle");
									 }
									 else
									 {
                                        echo  anchor('admin/venueAllocationMasterList/0/?end='.($end-20).'&click=next',"Cancle") ; 
									 }
 									     echo   "</td></tr>"; 

								 		   echo form_close();
 							     }        
                                    
                                    else
                                    {
									echo "<tr>
								 		  <td>$count</td>
								 		  
								 		  <td>".strtoupper($deptArr[$i])."</td>
								 		  <td>".strtoupper($venueArr[$i])."</td>
								 		  <td>".strtoupper($capacity[$i])."</td>
								 		  <td>".anchor('admin/venueAllocationMasterList/'.$aIdArr[$i].'/?Dvalue='.$deptArr[$i].'&Vvalue='.$venueArr[$i].'&end='.$start.'&click='.$TrackClick.'&flag='.$TrackClick,"Edit")."</td>
								 		  <td>".anchor('admin/deleteAllocationMasterList/'.$aIdArr[$i].'/?Dvalue='.$deptArr[$i].'&Vvalue='.$venueArr[$i].'&end='.$start.'&click='.$TrackClick.'&flag='.$TrackClick,"Delete")."</td>		
								     </tr>";
										  	
									   }
 									}
 									 if($start == 0)
 									 { 
									echo  anchor('admin/venueAllocationMasterList/0/?end='.$end.'&click=next&flag=next',"Next->") ;
									 }
									 elseif ($i > count($aIdArr) - 1 )
									  {
									    echo anchor('admin/venueAllocationMasterList/0/?end='.$start.'&click=previous&flag=previous',"<-Previous");
									 }
									 else
									 {
                                        echo  anchor('admin/venueAllocationMasterList/0/?end='.$end.'&click=next&flag=next',"Next->")."&nbsp;&nbsp;&nbsp;".anchor('admin/venueAllocationMasterList/0/?end='.$start.'&click=previous&flag=previous',"<-Previous") ; 
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
				
				<a data-toggle="modal" href="#addDept" class="btn btn-success btn-large">View Unassigned Department</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a data-toggle="modal" href="#addVenue" class="btn btn-danger btn-large">View Unassigned Venue</a>
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
		

			