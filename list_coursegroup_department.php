<!DOCTYPE html>
<html>
<head>

	<script language="javascript" type="text/javascript" src="<?php echo base_url(). 'js/jquery.min.js'; ?>"></script>

</head>
<script type="text/javascript">		
	

	$(function() {

		/*
		$('#DSdate').click( function()
		{
			var GetDate=$('#DSdate').val();
			alert(GetDate);
		});
		*/

		$('#sch').change( function()
		{
		
			GetNumOfDept();

		});

	});

	function GetNumOfDept()
	{
	 
		var GetDate=$('#sch').val();
		var dataString='dsSchool='+GetDate;
		dataString=dataString.trim();
		echo GetDate;
		if (dataString !='')
		{			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('/admin/getDepartmentNumber'); ?>",
				data: dataString,
                 
				success: function(data)
				{
					$("#department").empty();
				  var item = '';
				for(var i=1;i<= data;i++)
                   {
                   		//alert(i);
                   		item += '<option value="'+data+'">'+123+'</option>';
                   		
                   }
                   $("#department").append(item);
                  //  item +='</select>' ;      
				//	$("#DSslot").		
					}
				});
			}
		}		
	



</script>



<body>

			<div class="span8 center-spread">			
			<!--?php if (!empty($class) { ?--> 
			<br/><br/>	<br/><br/>															
				<fieldset>
				<legend> <b>List of Classes Taught by <?php echo  '&nbsp;'.$teacherName; ?> </b></legend>
				
				<?php  
				  if(!empty($imagePath))
				    {  
				    	echo'<table><tr> <td><img src="'.base_url().'uploads/'.$imagePath.'" width="200px" height="300px" alt="img" class="thumbnail"/></td></tr></table>';
				    }
						if($teacherDetails->num_rows() > 0)
						{ ?>
								<table class="table-striped table">
								<thead>
									<tr><th>SN</th><th>Level </th><th>Class </th>
								    </tr>
								</thead>
								<tbody>
								<?php 
									
									$serial = 1;                               
		                                   foreach($teacherDetails->result() as $row)
 									        {
		                                        	echo "<tr>
		                                        	<td>$serial</td>
										 		    <td>$row->level</td>
										 		     <td>$row->class</td>";
										 		     $serial++;

										       $itemsToDelete = $row->level.'---'.$row->class;
										       $deleteParameter = 'admin-delete_teacher_details-'.$row->td_ID.'-'.$teacherID.'-'.$teacherName;
										       $newcaller = 'admin-teacher_details-'.$teacherID.'-'.$teacherName.'-'.$caller;
								 		    echo  "<td>".anchor('admin/confirm_delete/'.$deleteParameter.'/'.$itemsToDelete.'/'.$newcaller ,"Delete")."</td>";
								/*		" <td>". form_input('teacherName', $teacherNameArr[$i])."</td><td><select class=\"input-xlarge\" id=\"courseID\" name=\"courseID\" width=\"120px\">"; 
                                            foreach($courseDetails->result() as $rowCs)
 									        {
 									           if($rowCs->id == $courseIDArr[$i])
 									             {
                                                    echo '<option value="'.$rowCs->id.'" selected="selected">'.$rowCs->code.'</option>' ;
                                                          
 									              }
 									            else
 									              {
 									           		echo '<option value="'.$rowCs->id.'">'.$rowCs->code.'</option>' ;
 									              }
 									        }						   
										   
								 		  
							
					                     echo "</select></td>"		  	
								*/	}		
                            $dcdCaller = str_replace('-', '/', $caller);
                            $attr = 'class="btn btn-danger btn-large"';
                         echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         ".anchor($dcdCaller,"Back to Summary",$attr) ; 

		                  }                      
					else 
					{echo "<p>There are no classes attached yet for &nbsp;$teacherName in this app!</p>";}
									

                      			?>
									
										
						</tbody>
				</table>
				<hr/>				
				
				<a data-toggle="modal" href="#addNewDept" class="btn btn-success btn-large">Add Class and Level</a>
				</fieldset>	
				<div id="addNewDept" class="modal hide fade">
		            <div class="modal-header">
		              <a class="close" data-dismiss="modal" >&times;</a>
		              <h3>Add Class and Level taught by the Teacher</h3>
		            </div>
		            <div class="modal-body">
		              
		              <?php
						$attributes = array('class'=>'form-horizontal'); 
						echo form_open_multipart("admin/Add_class_level", $attributes); 
					//	form_open_multipart('admin/upload_images', $attributes)

				  
					   ?>
                     <div class="control-group" >
							<?php echo form_label('Teacher Name','Teacher Name',array('class'=>'control-label disabled'));?>						
							<div class="controls">											
								<?php 
									echo "<b>$teacherName</b>";
								?>									
							</div> 
						</div>
                     
                     <div class="control-group" >
							<?php echo form_label('Class','Class',array('class'=>'control-label disabled'));?>						
							<div class="controls">											
								
								<select class="input-xlarge" id="class" name="class"  width="120px">
									
										<?php
										   foreach($classList as $row3)
 									        { 									       
                                                     echo '<option value="'.$row3.'"> '.$row3.'</option>' ;
 									        }
									?>
					
					</select>
					</div>
					</div>

                   <div class="control-group" >
							<?php echo form_label('Select Level','Select Level',array('class'=>'control-label disabled'));?>						
							<div class="controls">																			
								<?php
								         
										
                                            $i=0;
                                                foreach ($levelList as $lvl) 
                                                {
                                                                                         
	                                                    	 echo '<input type="checkbox" class="input-xlarge" name="'."level".$i.'" value="'.$lvl.'" />'.$lvl."&nbsp;&nbsp;&nbsp;&nbsp;";
	                                         
	                                                  $i++;  
	                                                  if(($i % 4)== 0)
	                                                       echo '<br/><br/>'; 
 									            }
 									     echo form_hidden("count",$i);
 									     echo form_hidden("teacherName",$teacherName);
 									     echo form_hidden("teacherID",$teacherID);
 									     echo form_hidden("caller",$caller);
									?>
									
																
							</div>

					   	
					
			          <div class="modal-footer form-actions">
			            <?php 
							  $attr = array('id'=>'submit', 'value'=>'Submit','class'=>'btn btn-success');
						      echo form_submit($attr);
							  echo '&nbsp; &nbsp; &nbsp';
							  $attr = array('id'=>'cancel', 'value'=>'Cancel','class'=>'btn'); 
						      echo form_reset($attr);						    
						?>									            
						 
			          </div>
						<?php echo form_close(); ?>
                   
          		</div>
			
			</div>

			