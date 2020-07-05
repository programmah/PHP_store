<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	

class allocation extends CI_Controller
{
	var $base;
	var $css_custom;
	var $css_bootstrap;
	var $css_bootstrap_responsive;
	var $image_url;
	var $numeric;
	var $app_name;
	var $jquery;
  
	function allocation()
	{
		parent::__construct();
		$this->base = $this->config->item('base_url');
		$this->css_custom = $this->config->item('css_custom');
		$this->css_bootstrap =  $this->config->item('css_bootstrap');
		$this->app_name = $this->config->item('app_name');
		$this->css_bootstrap_responsive =  $this->config->item('css_bootstrap_responsive');	
		$this->numeric = 	$this->config->item('numeric') ;
		$this->jquery  = 	$this->config->item('jquery') ;
	}
	function index()
	{
		$data = $this->_set_vars();
		$data['title']= $this->app_name." >> Welcome!";
		$this->load->view("header",$data);
		$this->load->view("super/template_header",$data);				
		$this->load->view('super/prepare_course_allocation',$data);
		$this->load->view("super/template_footer",$data);		
		$this->load->view("footer",$data);
	}	

	function _is_logged_in()
	{
		$current_user = $this->session->userdata('current_user');
		return $current_user['logged_in'];
	}

	function home()
	{
		$data = $this->_set_vars();
		$data['title']= $this->app_name." >> Welcome!";
		$this->load->view("header",$data);		
		$this->load->view("super/template_header",$data);	
		$this->load->view("super/home",$data);	
		$this->load->view("super/template_footer",$data);	
		$this->load->view('footer',$data);
	}
	
	function _set_vars()
	{
		$data['base'] = $this->base;		
		$data['css_bootstrap'] = $this->css_bootstrap;
		$data['css_bootstrap_responsive'] = $this->css_bootstrap_responsive;
		$data['css_custom'] = $this->css_custom;	
		$data['app_name'] = $this->app_name;
		$data['numeric'] = $this->numeric;
		$data['jquery'] = $this->jquery;
		return $data;
	}
 
function allocateDayandPeriod()
	  {
         
		      $TBoardObj              = new TableBoard();
          $DayListObj             = $TBoardObj->getDays();
          $FPD                    = $TBoardObj->getFreePeriod();
          $PD                     = $TBoardObj->getPeriod();
          $VenueObj               = $TBoardObj->getVenue() ;
          $MergerObj              = $TBoardObj->generatePeriodSet(2,$PD,$FPD,$DayListObj);
          $ObjMergerArr           = $TBoardObj->attachedVenueToMerger($MergerObj,$VenueObj);
          $CBoardObj              = new CourseParameter();
          $ObjCourseArr           =  $CBoardObj->getCourse() ;
        
          $MergerAllocatedCourseObj  = $TBoardObj->attachCourseToMerger($ObjMergerArr, $ObjCourseArr) ;
          $i = 0;
          echo '<table><tr><th>Day</th><th>Period</th><th> Venue</th><th>Course</th><tr>';
        foreach ($MergerAllocatedCourseObj as $Obj) 
          {     
                if( $Obj->Status ==1)
                {
                    echo "<tr><td>$Obj->Day</td><td>$Obj->Period</td><td>&nbsp;&nbsp;&nbsp;$Obj->Venue</td><td>&nbsp;&nbsp;&nbsp;$Obj->Course</td></tr>";
                    $i++; 
                }
             
          } 
          echo'</table>';
          echo "Total Courses: ".$i;
          
        /*foreach ($ObjCourseArr as $row)
           {
                $lvl = '';
                $dept = '';
                for($i = 0; $i < count($row->ListOfOferredDepartment); $i++)
                {
                        $lvl  .= $row->ListOfCorespondingLevel[$i].',' ;
                        $dept .= $row->ListOfOferredDepartment[$i].',' ;
                }
                echo  $row->CourseCode.'&nbsp;'.$row->TotalNosOfOferedDept.'&nbsp;'.$row->L.'&nbsp;&nbsp;&nbsp;&nbsp;'.$lvl .'&nbsp;&nbsp;'.$dept.'<br/>';//.'&nbsp;&nbsp;&nbsp;&nbsp;'.$row->VenueCapacity.'<br/>';
                 
           } */  
      
         
        //  redirect('admin/ShowCourseAllocationOptions');
	 }

/////////////////////////////////////
///////                     ////////
//////////END--NEW--PROG--//////////
   //  paste
 function allocationDateAndSlotToCourse() // allocating courses with no constraint (actual allocation)
      {
      	  $otherCourses = $this->_getOtherCourses();
          $dateList = $this->mconfigs->getAnyColumnItemList('examdates', 'dayDates');
          $AlloObj = new AllocateConstriantCoures();
          $AlloObj->allocateGroupCourse($otherCourses,  $dateList) ; 
          redirect('admin/ShowCourseAllocationOptions');

      }
      function _getOtherCourses()
      {
      	  $semester = $this->mconfigs->getAnyColumnItemList('semesterTbl', 'semester');
          $couresList = $this->mconfigs->getAllCoursesBySemester($semester);
          $courseOnConstraint = $this->_getConstraintsCourses();
          $logicObj = new logic();
          $otherCourses = array();
           foreach ($couresList as $course)
            {
              if(!$logicObj->contains($course,$courseOnConstraint) )
              	 {
                    $otherCourses[] = $course;   
              	 }
           }
         return $otherCourses;
      }
      function _getConstraintsCourses()
        {
        	 $Obj = new ConstraintClass();
    	  		$Obj->getAllConstraintItem();
    	 		$Obj->SetConstraintObject();
    	  		$list =  $Obj->getConstraintList(); 
    	  		
    	  	//	$this->mconfigs->clearTableContent('courseallocationtbl') ;
    	  		$AlloObj = new AllocateConstriantCoures();
    	  		$count = 1;
    	  		$courseList = array();
    	  		$courseArr = array();
    	  		$slotArr =  array();
    	 		foreach ($list as  $value)
    	  			{ 
    	  				if($value->Code == "GD")
    	  				{

    	 	  				if(count($value->Item)> 1)
                        		{ 
                        		    foreach ($value->Item as $item)
                                     {
                          	            $courseArr[] = $item;
                                     }
                                }
                            else
                             {
                                $courseArr[] = $value->Item;	
                             }
                        }
    	 	  				
    	 	     		$courseList = $Obj->getGroupLevelList( $value->Level);      			      
    	 		    } 
                 $courseList = array_merge($courseArr, $courseList) ;  
           
           return  $courseList ;
        }
      function ShowCourseAllocationOptions()
      {
          $data = $this->_set_vars();
		 $current_user = $this->session->userdata('current_user');
		// echo "here i See am oo";
		if($current_user['logged_in'])
		{
                $data['courseOnConstraints'] = $this->_getConstraintsCourses();
                $data['otherCourses'] = $this->_getOtherCourses() ;
                $this->load->view("header",$data);		
			    $this->load->view("super/template_header",$data);		
			    $this->load->view("super/show_courseOnConstaint", $data);
			    $this->load->view("super/template_footer",$data);		
			    $this->load->view("footer",$data);
		}
		else
    	{
		    redirect('start/index');
    	}     

      }
    function allocateCourseOnConstraint()  /// tentatively for course allocation
    {
    	//  $this->mconfigs->clearTableContent("courseallocationtbl");
    	 $data = $this->_set_vars();
		 $current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
    	 	 	$Obj = new ConstraintClass();
    	  		$Obj->getAllConstraintItem();
    	 		$Obj->SetConstraintObject();
    	  		$list =  $Obj->getConstraintList(); 
    	  		
    	  		$this->mconfigs->clearTableContent('courseallocationtbl') ;
    	  		$AlloObj = new AllocateConstriantCoures();
    	  		$count = 1;
    	 		foreach ($list as  $value)
    	  			{ 
    	 	             
    	 	 			 if($value->Code == "DS")
    	 	 				 {
    	 	  					$course = $value->Item[0];
    	 	    				$slot   = $value->Constraint[0];
    	 	    				$date   = $value->Constraint[1];
    	 	    		   	$AlloObj->allocateCourseOnDateAndSlot($course,$date,$slot);
    	 	    				
    	 	  				}
    	 	  			elseif($value->Code =="S")
    	 	 				 {
                 				$course = $value->Item;
    	 	 	 				$slot   = $value->Constraint[0];
    	 	 	 				$AlloObj->allocateCourseOnSlotOnly($course, $slot);
  									
    	 	  				} 
    	 	  			elseif($value->Code =="D")
    	 	  				{
    	 		 				$course = $value->Item;
    	 		 				$date   = $value->Constraint[0];
    	 		 				$AlloObj->allocationCourseOnDateOnly($course, $date);

    	 	  				}
    	 	  			elseif($value->Code == "GD")
    	 	  				{
    	 	  					$dateArr = array();
    	 	  	 				$value->Item;
    	 	     				$dateArr = $value->Constraint;
    	 	     				$couresList = $Obj->getGroupLevelList( $value->Level);
                   			    $AlloObj->allocateGroupCourse($couresList,  $dateArr) ;
              				}	 		  
    	 		    } 
    	 		  $this->allocationDateAndSlotToCourse();  
    	 //	redirect('admin/ShowCourseAllocationOptions');	   
     	 }
    	else
    	{
		    redirect('start/index');
    	}  
    }

    //
    function allocatePreview()
     {
         $data = $this->_set_vars();
		 $current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{  

         	$data['title'] = $this->app_name." >> Admin Dashboard >> Venue Allocation";					
            $AlloObj   = new SetAllocation();
      		$DeptList  = $AlloObj->getAllDepartmentDetails();
        	$VenueList = $AlloObj->getAllVenueDetails();
        	//echo count($DeptList);
        	/*foreach($VenueList as $value)
      		  {

      		 	echo $value->VenueID;
        		echo $value->Name;
        		echo $value->Capacity;
      		  } */
        	$VenDeptObject = new AssignVenueToDepartment($DeptList, $VenueList);

        	    $VenDeptObject->extractVenueOwnedByDept();
      			$VenDeptObject->extractVenueOwnedBySchool();
      			$VenDeptObject->extractGeneralVenue();
      			$VenDeptObject->AssignLeftOverDeptToVenue(); 
      			$Allo =  $VenDeptObject->getAllocationObject();
                
      	       $totalDeptLeft       =  $VenDeptObject->getTotalUnassignedDept($DeptList);
               $totalVenueLeft      =  $VenDeptObject->getTotalUnassignedVenue($VenueList);
               // $data['AlloVenDeptObjTable'] = $Allo; // $AlloVenDeptObjTable;

                $vArr = $VenDeptObject->getLeftVenueList($VenueList);
                $dArr = $VenDeptObject->getLeftDepartmentList($DeptList);
		        
		         if( count($Allo) > 0)
		         {
		         	$this->mconfigs->clearTableContent('allocationvenue');
		         	 foreach ($Allo as $Obj) 
		         	 {
		         	     $Allo_details = array(
						  
						  'department'=> $Obj->Department,
						  'venue'=>$Obj->Venue,
						  'venueCapacity'=>$Obj->Capacity
						  );
		         	     $this->mconfigs->insertIntoAnyTable('allocationvenue', $Allo_details) ;
		         	 }
		         	
		         	
					
		         }
		         if($totalDeptLeft > 0 || $totalVenueLeft > 0)
		         {
		             $this->mconfigs->clearTableContent('unassignlist');	
		            
		             if($totalDeptLeft > 0)
		               {
		           		   foreach ($dArr as $deptObj) 
		            		 {
		              			  $Dept_details = array(  'name'=> $deptObj->DeptCode, 'type'=>'D'); 	

		                          $this->mconfigs->insertIntoAnyTable('unassignlist', $Dept_details) ;
		                     }
		               }

		              if($totalVenueLeft > 0)
		                 {             
	                         foreach ($vArr as $venObj) 
		                         {
		                             $Ven_details = array( 'name'=> $venObj->Name, 'type'=>'V'); 
		                             $this->mconfigs->insertIntoAnyTable('unassignlist', $Ven_details) ;	
		                         }
		                       
		                 }
		         
		         }

		         

		        $data['LeftVenueList']       = $vArr;
		        $data['LeftDepartmentList']  = $dArr;
		        $data['totalDeptLeft']       = $totalDeptLeft;
		        $data['totalVenueLeft']      = $totalVenueLeft;
		        $data['VenueList']			 = $VenueList;
		  		$data['DeptList']			 = $DeptList;

			    $this->load->view("header",$data);		
			    $this->load->view("super/template_header",$data);		
			    $this->load->view("super/listAllocation", $data);
			    $this->load->view("super/template_footer",$data);		
			    $this->load->view("footer",$data); 
			      
		}		  
		else 
		{
			redirect('start/index');
		}

     }
    function allocate($allo_edit) 
    { 
    	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	

   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> Venue Allocation";					
			if(!empty($_GET['click']) )
			{
	
		            $data['click']= $this->input->get('click') ;
			 		
			 		$data['end'] = $this->input->get('end') ;
		    
			}
			else
			{
			  $data['click'] = "";
		    }
		    if(!empty($_GET['Dvalue']) )
			{
               $data['Dvalue'] = $this->input->get('Dvalue') ;
               $data['Vvalue'] = $this->input->get('Vvalue') ;
			}
			$AlloObj   = new SetAllocation();
      		$DeptList  = $AlloObj->getAllDepartmentDetails();
        	$VenueList = $AlloObj->getAllVenueDetails();
 		if(isset($_GET['flag'] )&& isset($_GET['op']))  // after update task
 		{
            
            $data['allo_edit'] = $allo_edit;
		    $data['AlloVenDeptObjTable'] =  $_SESSION['AlloVenDeptObjTable'];// $_GET['Obj']; //$AlloVenDeptObjTable;
		    $data['LeftVenueList']       =  $_SESSION['LeftVenueList']; //  $vArr;
		    $data['LeftDepartmentList']  = $_SESSION['LeftDepartmentList']; //$dArr;
		    $data['totalDeptLeft']       = count($_SESSION['LeftDeptList']);// $totalDeptLeft;
		    $data['totalVenueLeft']      = count($_SESSION['LeftVenueList']); //$totalVenueLeft;
		    echo count($_SESSION['LeftDepartmentList'])."update"; 

 		}
 		else
 		{
 			$VenDeptObject = new AssignVenueToDepartment($DeptList, $VenueList);
 			  if(($_GET['flag'] == $this->input->get('click')) && isset($_GET['flag']))  // edit clicks
 			  {
 			  	$data['AlloVenDeptObjTable'] =  $_SESSION['AlloVenDeptObjTable'];
 			  	$data['LeftVenueList']       =  $_SESSION['LeftVenueList']; //  $vArr;
		        $data['LeftDepartmentList']  = $_SESSION['LeftDepartmentList']; //$dArr;
		        $data['totalDeptLeft']       = count($_SESSION['LeftDepartmentList']);// $totalDeptLeft;
		        $data['totalVenueLeft']      = count($_SESSION['LeftVenueList']); //$totalVenueLeft;
		        $data['allo_edit'] = $allo_edit;
		        echo count($_SESSION['LeftDepartmentList'])."click";
 			  }
		     else
		     {
        	
      			$VenDeptObject->extractVenueOwnedByDept();
      			$VenDeptObject->extractVenueOwnedBySchool();
      			$VenDeptObject->extractGeneralVenue();
      			$VenDeptObject->AssignLeftOverDeptToVenue(); 
      			$Allo =  $VenDeptObject->getAllocationObject();

      		    $totalDeptLeft       =  $VenDeptObject->getTotalUnassignedDept($DeptList);
                $totalVenueLeft      =  $VenDeptObject->getTotalUnassignedVenue($VenueList);
                $data['AlloVenDeptObjTable'] = $Allo; // $AlloVenDeptObjTable;

                $vArr = $VenDeptObject->getLeftVenueList($VenueList);
                $dArr = $VenDeptObject->getLeftDepartmentList($DeptList);
		        $data['allo_edit'] = $allo_edit;
		    
		        $data['LeftVenueList']       = $vArr;
		        $data['LeftDepartmentList']  = $dArr;
		        $data['totalDeptLeft']       = $totalDeptLeft;
		        $data['totalVenueLeft']      = $totalVenueLeft;
            }
            
		    
		
		}
		    $data['VenueList']			 = $VenueList;
		    $data['DeptList']			 = $DeptList;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/allocationForm", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);   
		}		  
		else 
		{
			redirect('start/index');
		}
           
      
    }
   function update_Allocate( $aId)
   {
       	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		// session_start();
		if($current_user['logged_in'])
		{
             
			 	$click = $this->input->get('click') ;
                $end = $this->input->get('end') ;
               
                $venueArr = explode(',', $_REQUEST['venueCodeList'])  ;
                $deptArr =  explode(',',$_REQUEST['deptCodeList']);
                $oldVenueCode = $_REQUEST['oldVenueID'];
               $oldDeptCode  = $_REQUEST['oldDeptID'];  

               
              
                $AllObj = new AllocatedVenueDepatmentList();
                $AllObj->Department = $deptArr[1];
                $AllObj->Venue      = $venueArr[1];
                $AlloVenDeptObjTable  = $_SESSION['AlloVenDeptObjTable'];

                $UpdateAllObj = new UpdateAllocateClass($AlloVenDeptObjTable) ;
                $UpdateAllObj->Update($aId, $AllObj) ;
                


            //   $AlloObj   = new SetAllocation();
      		   $LeftDepartmentList =  $_SESSION['LeftDepartmentList'] ;
			   $LeftVenueList      =  $_SESSION['LeftVenueList']  ;
			   
	
               $LeftVenueList = $UpdateAllObj->UpdateVeneObjectArr($LeftVenueList,$venueArr[0]);
               $LeftDepartmentList  = $UpdateAllObj->UpdateDepartmentObjectArr($LeftDepartmentList,$deptArr[0]);
               
           if($oldVenueCode != $venueArr[1])
               {

                  $LeftVenueList =  $UpdateAllObj->updateLeftVenueList($LeftVenueList, $oldVenueCode);
                   $_SESSION['LeftVenueList'] = $LeftVenueList;
               }
               else
                {
                	$_SESSION['LeftVenueList'] = $LeftVenueList;
              }
                if ($oldDeptCode != $deptArr[1])
                {
                
                	$LeftDepartmentList = $UpdateAllObj->updateLeftDeptList($LeftDepartmentList, $oldDeptCode); 
                    $_SESSION['LeftDepartmentList']  = $LeftDepartmentList;
                 }
                 else
                {
                 	 $_SESSION['LeftDepartmentList']  = $LeftDepartmentList;
                }
               
               $AllocatedUpdateObject = $UpdateAllObj->getUpdatedObject() ;
               $_SESSION['AlloVenDeptObjTable'] =  $AllocatedUpdateObject;
              
			 
            redirect('admin/allocate/0/?end='.$end.'&click='.$click.'&flag=U&op=U' );

		}
		else
		{
			redirect('start/index');
		}

   }
//used
    
//used
  
//used
   

    function show_lecture_time($time_edit)
    {
       $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
								
			$dayDetails = $this->mconfigs->getAnyTableContent('days');
			
             $dayIDarr = array();
             $dayNamearr = array();
			 if($dayDetails->num_rows() > 0)
               {
	             
	               	foreach( $dayDetails->result() as $row)
	               	{
	               	  $dayIDarr[] = $row->did;
 					  $dayNamearr[] = $row->day;
	               	}
               }
 
             $tymeDayIDArr = array();
			 $timeframe = $this->mconfigs->getAnyTableContent('timeframe');     
           
            
             if($timeframe->num_rows() > 0)
             {
                 foreach ($timeframe->result() as $srow) 
                 {
                 	   
                 	    $tymeDayIDArr[] = $srow->d_id;

                 }
                
             }
              $tools = new logic();  // calling class
              $newDayList = array();
              $newDayName = array();
             	  for($j = 0; $j < count($dayIDarr); $j++)
             	  {
             	  	  if( !($tools->contains($dayIDarr[$j], $tymeDayIDArr )) )   // ensuring that already mapped day doesnt reappear in the list again
             	        {
                           $newDayList[] = $dayIDarr[$j] ;
                            $newDayName[] =  $dayNamearr[$j];
             	        }
             	  }
             
            $data['title'] = $this->app_name." >> Admin Dashboard >> Mapping";
			 
			// $data['school'] = $this->mconfigs->getAnyTableContent('skool');
            $data['DID'] = $newDayList;
            $data['DNAME'] = $newDayName;
            $data['ltime'] = $timeframe;
			$data['time_edit'] = $time_edit;
			$this->load->view("header",$data);	
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_lecturetime", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}  

    }

 /*    function insert_lecture_time()
     {
            $current_user = $this->session->userdata('current_user');
		if((!empty($_POST['day']))  && (!empty($_POST['stime']))  )
				{	
				
					$stime =	$this->input->post('stime');
				    $etime =	$this->input->post('etime');
				 
					$tyme_details = array(
						  
						  'd_id'=>$this->input->post('day'),
						  'starttime'=>$stime,
						  'endtime'=>$etime
						  );

					$timeClass = new TimeCheck($stime, $etime ) ;
					$timeClass->splitTime();
					if($timeClass->CorrectTimeFrame())
					{

						$this->mconfigs->insertIntoAnyTable('timeframe', $tyme_details) ;
						redirect('admin/show_lecture_time/0');
				    }
				   else
					{
						
						redirect('admin/show_lecture_time/E');	
					}
		
			 }
		else{
				redirect('admin/show_lecture_time/0');
			}
     }


     function update_lecture_time($tid)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
             
         	 $stime =	$this->input->post('stime');
			 $etime =	$this->input->post('etime');
          $tyme_details = array(
						  
						  'starttime'=>$stime,
						  'endtime'=>$etime
						  );
                  $timeClass = new TimeCheck($stime, $etime ) ; // calling time checker class
					$timeClass->splitTime();
				if($timeClass->confirmCorrectTimeFrame())
				{
        			$this->mconfigs->updateAnyTableRow('tid', $tid, 'timeframe', $tyme_details) ; 
                     redirect('admin/show_lecture_time/0');
                }
               else
				{
						
						redirect('admin/show_lecture_time/E');	
				}
           }		
		else 
		{
			redirect('start/index');
		}
     }
*/
     function delete_time($tid)
     {
     	 $this->mconfigs->deleteAnyRowFromAnyTable('tid', $tid,'timeframe') ;	
		redirect('admin/show_lecture_time/0');
     }

    function show_free_time($free_edit)
    {
       $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
								
			$dayDetails = $this->mconfigs->getAnyTableContent('days');
			
             $dayIDarr = array();
             $dayNamearr = array();
			 if($dayDetails->num_rows() > 0)
               {
	             
	               	foreach( $dayDetails->result() as $row)
	               	{
	               	  $dayIDarr[] = $row->did;
 					  $dayNamearr[] = $row->day;
	               	}
               }
 
             $tymeDayIDArr = array();
			 $freetime = $this->mconfigs->getAnyTableContent('freetime');     
           
            
             if($freetime->num_rows() > 0)
             {
                 foreach ($freetime->result() as $srow) 
                 {
                 	   
                 	    $tymeDayIDArr[] = $srow->d_id;

                 }
                
             }
              $tools = new logic();  // calling class
              $newDayList = array();
              $newDayName = array();
             	  for($j = 0; $j < count($dayIDarr); $j++)
             	  {
             	  	  if( !($tools->contains($dayIDarr[$j], $tymeDayIDArr )) )   // ensuring that already mapped days doesnt reappear in the list again
             	        {
                           $newDayList[] = $dayIDarr[$j] ;
                            $newDayName[] =  $dayNamearr[$j];
             	        }
             	  }
             
            $data['title'] = $this->app_name." >> Admin Dashboard >> Lecture Free Time";
			 
		//	 $data['school'] = $this->mconfigs->getAnyTableContent('skool');
            $data['DID'] = $newDayList;
            $data['DNAME'] = $newDayName;
            $data['freetime'] = $freetime;
			$data['free_edit'] = $free_edit;
			$this->load->view("header",$data);	
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_freetime", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}  

    }

   function insert_free_time()
     {     

            $current_user = $this->session->userdata('current_user');
		if((!empty($_POST['day']))  && (!empty($_POST['stime']))  )
				{	
				$stime =	$this->input->post('stime');
				$etime =	$this->input->post('etime');
				 
					$tyme_details = array(
						  
						  'd_id'=>$this->input->post('day'),
						  'sfreetime'=>$stime,
						  'efreetime'=>$etime,
						  'event'=>$this->input->post('event'),
						  'venue'=>$this->input->post('venue')
						  );
                    
					$timeClass = new TimeCheck($stime, $etime ) ;
					$timeClass->splitTime();
					if($timeClass->confirmCorrectTimeFrame())
					{
						$this->mconfigs->insertIntoAnyTable('freetime', $tyme_details) ;
						redirect('admin/show_free_time/0');	
					}
					else
					{
						
						redirect('admin/show_free_time/E');	
					}
					
		
			 }
		else{
				redirect('admin/show_free_time/0');
			}
     }


     function update_free_time($fid)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
             
             $stime =	$this->input->post('stime');
			 $etime =	$this->input->post('etime');
          $tyme_details = array(
						  
						  'sfreetime'=>$stime,
						  'efreetime'=>$etime,
						  'event'=>$this->input->post('event'),
						  'venue'=>$this->input->post('venue')
						  );
                  $timeClass = new TimeCheck($stime, $etime ) ; // calling time checker class
					$timeClass->splitTime();
				if($timeClass->confirmCorrectTimeFrame())
				{
        			$this->mconfigs->updateAnyTableRow('fid', $fid, 'freetime', $tyme_details) ; 
                     redirect('admin/show_free_time/0');
                }
                else
				{
						
					redirect('admin/show_free_time/E');	
				}
           }		
		else 
		{
			redirect('start/index');
		}
     }

     function delete_free($fid)
     {
     	 $this->mconfigs->deleteAnyRowFromAnyTable('fid', $fid,'freetime') ;	
		redirect('admin/show_free_time/0');
     }
    
   

    function show_setupTimeTable($msg)
    {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			  $postID = explode('-', $msg);
              $semester =	$this->input->post('semester');
			  $dayId =	$this->input->post('day');
			  if($dayId =="" ) // returning unposted day id and semester
			  {
			  	$dayId = $postID[1] ;
			  	$semester = $postID[2];
			  }
            //   echo $dayId." :day";
               $lectrTymeFrame = $this->mconfigs->getAnyTableRow('d_id' ,$dayId, 'timeframe') ;
			   $dayName = $this->mconfigs->getDayByID($dayId);
               $semesterCourseList = $this->mconfigs->getAnyTableRow('semester' ,$semester, 'courses');
			   $theatre = $this->mconfigs->getAnyTableContent('ltheatre');
			   
			   $timeTableList = $this->mconfigs->getTimeTableDetails($dayId,$semester) ;
			  
			  $freeLtrTyme =  $this->mconfigs->getAnyTableRow('d_id' ,$dayId, 'freetime') ;
               $gnrtdSTymeArr = array();
               $gnrtdEDTymeArr = array();
               $gnrtdHeaderTymeArr = array();
			//   echo  $lectrTymeFrame->num_rows()." :here";
			 if($lectrTymeFrame->num_rows() > 0)
               {
	                $startTime ="";
	                $endTime = "" ;
	               	foreach( $lectrTymeFrame->result() as $row)
	               	{
	               	  $startTime = $row->starttime;
 					  $endTime   = $row->endtime;
	               	}
	             	$timeClass = new TimeCheck($startTime, $endTime) ;
			        $timeClass->splitTime();
                   $gnrtdSTymeArr = $timeClass->generateLectTymeList("S");     // S implies start time
                   $gnrtdEDTymeArr = $timeClass->generateLectTymeList("E");   // E implies end time
                   $gnrtdHeaderTymeArr = $timeClass->generateLectTymeList("H");  // H denote header
               }
               

               $courseID = array();
               $courseCode = array();
              if($semesterCourseList->num_rows() > 0)
               {
	             
	               	foreach( $semesterCourseList->result() as $row2)
	               	{
	               	  $courseID[]     = $row2->cid;
 					  $courseCode[]   = $row2->code;
	               	}
               }
               $theatreID = array();
               $theatrename = array();
               if($theatre->num_rows() > 0)
               {
	             
	               	foreach($theatre->result() as $row3)
	               	{
	               	  $theatreID[]     = $row3->lid;
 					  $theatrename[]   = $row3->theatrename;
	               	}
               }
               
               $freeContent ="";
               $sfreeTym ="";
               $efreeTym ="";
               if($freeLtrTyme->num_rows() > 0)
               {
	               	foreach( $freeLtrTyme->result() as $row4)
	               	{
	               	  $freeContent     = $row4->event."<br/>".$row4->venue;
 					  $sfreeTym   = $row4->sfreetime;
 					  $efreeTym  = $row4->efreetime;
	               	}
               }
              $data['MSG'] = $msg;
             $data['TTL'] = $timeTableList; 
             $data['semester'] = $semester; 
             $data['day'] =$dayId;
             $data['FC'] = $freeContent;
            $data['FST'] = $sfreeTym;
            $data['FET'] =  $efreeTym;
             $data['CID'] = $courseID;
            $data['CODE'] = $courseCode;
            $data['DAY'] =  $dayName;
            $data['semester'] =  $semester;
            $data['LID'] =  $theatreID;
            $data['THEATRE'] =  $theatrename;
            $data['GTHEADER'] =  $gnrtdHeaderTymeArr;
            $data['GTSTART'] =  $gnrtdSTymeArr;
            $data['GTEND'] =  $gnrtdEDTymeArr;
			$data['title'] = $this->app_name." >> Admin Dashboard >> Time-Table Setup";					
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_lecturesetup", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		} 

    }


    function insert_timeTable()
     {     

            $current_user = $this->session->userdata('current_user');
		if((!empty($_POST['etime']))  && (!empty($_POST['stime'])) && (!empty($_POST['cid'])) && (!empty($_POST['lid']))  )
				{	
				$nstime =	$this->input->post('stime');   // new set time
				$netime =	$this->input->post('etime');   // new set time 
                $dayId  =    $this->input->post('day');
                $semester=    $this->input->post('semester'); 
                $cInf    =     $this->input->post('cid');
                $cDt   =       explode('/', $cInf);
                $lInf    =     $this->input->post('lid');
                $lDt   =      explode('/', $lInf);
                 

                	
                    $newTime = new TimeCheck($nstime,$netime) ;
                    $newTime->splitTime();
                    $nstH = $newTime->getSTH();
                	 $netH = $newTime->getETH();
                   if($newTime->confirmCorrectTimeFrame())  // checking correct time range
		               {

                               $timeTableList = $this->mconfigs->getLtheatreDetailsForLectrTymeAllocation(  $dayId,$lDt[0]) ;
                
                               // $counter = 0;
                               if( $timeTableList->num_rows() > 0 ) //since theartre is used for that day, check for it availability time
                                   {   
                                        $numbersOfFoundRows = $timeTableList->num_rows() ;
                 	                    $counter = 0;
                                        foreach ($timeTableList->result() as  $row)
                                          {
                                                $fstime = $row->startLT;
                                                $fetime = $row->endLT ;

                                                $fixedTime = new TimeCheck($fstime, $fetime); 
                                                $fixedTime->splitTime();
                                                $fstH = $fixedTime->getSTH(); 
                                                $fstP = $fixedTime->getStP();
                                                $fetH = $fixedTime->getETH();
                                                $fetP = $fixedTime->getEtP();

                                                $nstH = $newTime->getSTH();
                                                $nstP = $newTime->getStP();
                                                $netH = $newTime->getETH();
                                                $netP = $newTime->getEtP();

                                                $Logic = new logic();
                                                if(  $Logic->checkAvailabilityOfLectureTime( $fstH, $fstP, $fetH,$fetP,$nstH,$nstP,$netH,$netP ) ) // check if time is available
                                                {
                                                	$counter++;
                                                }
                                          }   // end foreach loop
 											
                                          if( $counter == $numbersOfFoundRows  ) // theartre is available for that time
                                          {
    											
    												$ltDesptArr    =  $this->mconfigs->getTheatreCategoryDescrptnByID($lDt[0]);  // category at index 0 and description(skool or dept) at index 1
             										$courseDespArr =  $this->mconfigs->courseCategoryDept($cDt[0]); // category at index 0 and dept at index 1
             						            	$skoolID     =  $this->mconfigs->getSkoolIdByDept($courseDespArr[1]);
             						            	$skoolcode     =  $this->mconfigs->getSchoolByID($skoolID);
                                   
             							            $Logic = new logic();
                                  	 	         if( $Logic->CheckCorrectRelationshipBtwLTheatreAndCourse($ltDesptArr[0],$ltDesptArr[1], $courseDespArr[0], $courseDespArr[1],$skoolcode,$semester)  ) //dept and lT relationship checking
                                  	 	           {
                                  	 	           	    $res = $this->mconfigs->checkIfCourseHasBinSchedule4DdayB4($cDt[0], $dayId, $semester) ;  // preventing double scheduling course per day
  													    if($res->num_rows() > 0)
  													    {
  													    	$msg = "1Sorry ".$cDt[1]." has been schedule for today already. You can not schedule a course twice in a day -".$dayId.'-'.$semester ;; 
						                	                redirect('admin/show_setupTimeTable/'.$msg);
  													    }
  													    else
  													    {    
  													    	if( $nstH ==12){   $duration = (int)$netH; }
  													    		
  													    	 else  {   $duration =(int) $netH - (int)$nstH ;}
  													    		
                                             					$timeTable_details = array(
						                       					'td_id'=> $dayId, 
						                       					'tc_id'=>$cDt[0],
						                       					'tl_id'=>$lDt[0],
						                       					'startLT'=>$nstime,
						                       					'endLT'=>$netime,						                     
						                       					'semester'=>$semester,
						                       					'duration'=>$duration
						                       					);

                                             			$this->mconfigs->insertIntoAnyTable('timetable', $timeTable_details) ;
						                        		redirect('admin/show_setupTimeTable/S-'.$dayId.'-'.$semester); // s denote success	
						                        	  }
						                           }
						                        else
						                           {
						                	          $msg = "Sorry there still a course offered by more student populated expected to be fixed to this lecture theatre-".$dayId.'-'.$semester ;; 
						                	          redirect('admin/show_setupTimeTable/'.$msg);
						                           }
                                          }
                                         else  // theatre not available at that specified time
                                         {
                                            $msg = $lDt[1]." is fixed for ".$nstime." to ".$netime."-".$dayId.'-'.$semester ;                     
                                            redirect('admin/show_setupTimeTable/'.$msg) ;
                                         }

                                   }
                               else  // since theatre is free check for skool and department coinciseness
                               {

             						$ltDesptArr    =  $this->mconfigs->getTheatreCategoryDescrptnByID($lDt[0]);  // category at index 0 and description(skool or dept) at index 1
             						$courseDespArr =  $this->mconfigs->courseCategoryDept($cDt[0]); // category at index 0 and dept at index 1
             						$skoolID       =  $this->mconfigs->getSkoolIdByDept($courseDespArr[1]);
             						$skoolcode     =  $this->mconfigs->getSchoolByID($skoolID);
                                   
             							$Logic = new logic();
                                  	 	if( $Logic->CheckCorrectRelationshipBtwLTheatreAndCourse($ltDesptArr[0],$ltDesptArr[1], $courseDespArr[0], $courseDespArr[1],$skoolcode,$semester)  ) //dept and lT relationship checking
                                  	 	{
                                  	 		
                                  	 			$res = $this->mconfigs->checkIfCourseHasBinSchedule4DdayB4($cDt[0], $dayId, $semester) ;  // preventing double scheduling course per day
  													    if($res->num_rows() > 0)
  													    {
  													    	$msg = "Sorry ".$cDt[1]." has been schedule for today already. You can not schedule a course twice in a day -".$dayId.'-'.$semester ;; 
						                	                redirect('admin/show_setupTimeTable/'.$msg);
  													    }
  													    else
  													    {		
  													    	if( $nstH ==12){   $duration = (int)$netH; }
  													    		
  													    	 else  {   $duration =(int) $netH - (int)$nstH ;}


                               									$timeTable_details = array(
						                      				 		'td_id'=> $dayId, 
						                       				 		'tc_id'=>$cDt[0],
						                       						 'tl_id'=>$lDt[0],
						                       						 'startLT'=>$nstime,
						                       						 'endLT'=>$netime,
						                       						 'semester'=>$semester,
						                       						 'duration'=>$duration 
						                       			   		 );

                                             	   				 $this->mconfigs->insertIntoAnyTable('timetable', $timeTable_details) ;
						                            			redirect('admin/show_setupTimeTable/S-'.$dayId.'-'.$semester); // s denote success
						               					}
						                }
						                else
						                {
						                	$msg = "2Sorry there still a course offered by more student population expected to be fixed the this lecture theatre-".$dayId.'-'.$semester ;; 
						                	redirect('admin/show_setupTimeTable/'.$msg);
						                }
                               }  

				           
                     	 }  // clossing if ($newTime->confirmCorrectTimeFrame() )
			        else   // else of  if($newTime->confirmCorrectTimeFrame()  )
			         {
			         	$msg = "Sorry You have Selected a Wrong Time Range ".$nstime." to ".$netime."-".$dayId.'-'.$semester ;
			        	redirect('admin/show_setupTimeTable/'.$msg);	
			         }
			}
		else{
				redirect('admin/show_setupTimeTable/action failed-'.$dayId.'-'.$semester);
			}



     }


     function timeTable_report($day,$dayId, $semester)
	  {
		
		$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
            $timeTableList = $this->mconfigs->getTimeTableDetails($dayId,$semester) ;
			
			  $STarr = array();
			  $EDarr = array();
			  $CourseidArr = array();
			  $LTidArr = array();
              $ltCourse = array();
             
             
			 if($timeTableList->num_rows() > 0)
               {
	             
	               	foreach( $timeTableList->result() as $row)
	               	{
	               	     $STarr[] = $row->startLT;
 						$EDarr[] = $row->endLT;
 						$CourseidArr[] = $row->tc_id;
 						$LTidArr[] =  $row->tl_id ;   
	               	}

	               	for($i = 0 ; $i < count($CourseidArr); $i++)
					 {
						   $courseCode  = $this->mconfigs->getTimeTableRowNameByID($CourseidArr[$i],'cid','courses', 'code');
						   $theatreName = $this->mconfigs->getTimeTableRowNameByID($LTidArr[$i],'lid','ltheatre', 'theatrename');
						    $ltCourse[]  =  $courseCode.'  '.$theatreName;
					 }
               }

             $data['LTCOURSE'] = $ltCourse;
            $data['START'] = $STarr;
            $data['END'] = $EDarr;
            $data['DAY'] = $day;
            $data['SEMESTER'] = $semester;
			//$data['title'] = $this->app_name." >> Admin Dashboard >> Time-Table Report";					
			//$this->load->view("header",$data);		
			//$this->load->view("super/template_header",$data);		
			$this->load->view("super/timeTable_report", $data);
		//	$this->load->view("super/template_footer",$data);		
			//$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		} 		
	 }
	
  function getReader($tableName, $ColumnName, $caller, $sheetNos)
        {
                
                $ColumnName = explode('-', $ColumnName) ;
               // echo $tableName."&nbsp;&nbsp;".count($ColumnName)."&nbsp;&nbsp;".$caller."&nbsp;&nbsp;".$sheetNos;
                 require_once 'C:\wamp\www\LectureTimeTable\application\libraries\excel_reader2.php';
        		  $filename = $_FILES["file"]["tmp_name"] ;
	          
	              $excel = new Spreadsheet_Excel_Reader();
                  $newCaller  = str_replace('-', '/',$caller);
                
	                // read spreadsheet data
	           // $excel->read('C:\wamp\www\ExamTimeTable\application\views\super\Book1.xls');
                   $excel->read($filename); 
                   
                    
                    $batchOfExcelData = array();
	               $x=1;
	               try{
			               while($x <= $excel->sheets[$sheetNos]['numRows']) 
			                { 
				                $details = array();
				                 
				                for ($j = 0; $j< count($ColumnName); $j++) 
				                {    if(isset($excel->sheets[$sheetNos]['cells'][$x][$j+1]))
				                    {
				                         $details[ $ColumnName[$j] ] = $excel->sheets[$sheetNos]['cells'][$x][$j+1] ; 
				                    }
				                }
				               
		                         $batchOfExcelData[] = $details;
		                         $x++;
			                }
	                

	                 $this->mconfigs->insertBatchArrayIntoAnyTable($tableName , $batchOfExcelData);
	               	   
	              	   redirect($newCaller );
	              }
	              catch(Exception $ex){
	                     echo '<script language="javascript">alert('. $ex->getMessage().'); </script>'; //Operation Failed due to wrong input "); </script> '
	                    redirect($newCaller );
	                 }
             
        }


// New Added codes == Tunde
  function show_freeperiod($day_edit)
   {
         
    $data = $this->_set_vars();
    $current_user = $this->session->userdata('current_user');
    if($current_user['logged_in'])
    { 
        
      $data['title'] = $this->app_name." >> Admin Dashboard >> level";          
      $data['freeperiodL'] = $this->mconfigs->getAnyTableContent('freeperiod');
      $data['dayOfLecture'] = $this->mconfigs->getAnyTableContent('lecturedays');
      $data['day_edit'] = $day_edit;
      $this->load->view("header",$data);    
      $this->load->view("super/template_header",$data);   
      $this->load->view("super/list_freeperiod", $data);
      $this->load->view("super/template_footer",$data);   
      $this->load->view("footer",$data);
    }   
    else 
    {
      redirect('start/index');
    }
   }


    function delete_freeperiod($id)
     {
       $click = $this->input->get('click') ;
         $end = $this->input->get('end') ;

       $this->mconfigs->deleteAnyRowFromAnyTable('id', $id,'freeperiod') ;  
    redirect('constraint/show_freeperiod/0/?end='.$end.'&click='.$click);
     }  


     function insert_freePeriod()
    {
       
    if(!empty($_POST['day'])) 
    { 
        $current_user = $this->session->userdata('current_user');
           $dday = $this->input->post('day');
           $res =  $this->mconfigs->getAnyTableRow('days', $dday, 'freeperiod');
        $msg = "-" ;
         if( $res->num_rows() > 0 )
        {   
            $mmsg = 1;
            $msg = "Free period-already-Exist for the day" ; 
            redirect('constraint/show_freeperiod/0');  
        }

           $dDescription = $this->input->post('Description');
           $sstarttime =  $this->input->post('starttime');
           $ffduration = $this->input->post('fduration');
           $aaPeriodInt = $this->input->post('aPeriodInt');
       $sstarttime2 = explode(" ",$sstarttime);
           $sstarttimeAdd =  $sstarttime2[0] + $ffduration;
           // $sstarttimeAddgmt =  $sstarttime2[0] + $ffduration;
            $sstarttime3 = "am";
        
           if($sstarttimeAdd >= 12)
           {
            //echo "done";
            $sstarttime3 = "pm";
           }
          
           if($sstarttimeAdd > 12)
           {
            //echo "done22";
            $sstarttimeAdd = $sstarttimeAdd - 12;
            //$sstarttime2[1] = "pm";
           }
            if($sstarttime2[1] == 'pm')
           {
            //echo "done33";
            $sstarttime3 = "pm";
            $sstarttimeAddgmt = $sstarttime2[0] + 12;
           }
            
          $lecture_details = array( 'days'=>$dday, 'Description'=>$dDescription, 'TimeFrom'=>$sstarttime2[0] , 'TimeFrom_gmt'=>$sstarttimeAddgmt, 'TimeTo'=>$sstarttimeAdd, 'TimeFrom_ampm'=>$sstarttime2[1], 'TimeTo_ampm'=>$sstarttime3, 'duration'=>$ffduration) ;
          $this->mconfigs->insertIntoAnyTable('freeperiod', $lecture_details) ; 
          redirect('constraint/show_freeperiod/0');
      
      }
    else{
              redirect('constraint/show_freeperiod/0');
      }
   }


   
  function delete_constraint_list($item)
     {
       $click = $this->input->get('click') ;
         $end = $this->input->get('end') ;

       $this->mconfigs->deleteAnyRowFromAnyTable('item', $item,'constrainttbl') ; 
    redirect('constraint/show_constraint_list/0/?end='.$end.'&click='.$click);
     }  
          
function show_priority_list($priority_edit)
   {
         
    $data = $this->_set_vars();
    $current_user = $this->session->userdata('current_user');
    if($current_user['logged_in'])
    { 
         if(!empty($_GET['rank']))
         {
              $data['rank'] =  $this->input->get('rank');
             // echo $this->input->get('rank');
         }
         else
         {
          $data['rank'] = "";
         }
      $data['title'] = $this->app_name." >> Admin Dashboard >> level";          
      $data['ratingDetails'] = $this->mconfigs->getAnyTableContent('constraintratings');
      $data['priority_edit'] = $priority_edit;
      $this->load->view("header",$data);    
      $this->load->view("super/template_header",$data);   
      $this->load->view("super/list_constraintRatings", $data);
      $this->load->view("super/template_footer",$data);   
      $this->load->view("footer",$data);
    }   
    else 
    {
      redirect('start/index');
    }
   }

  
     function confirm_delete($deleteParameter, $itemToDelete, $caller)
    {
        $data = $this->_set_vars();
        $this->load->view("header",$data);    
      //$data['constraintDetails'] = $this->mconfigs->getAnyTableContent('constrainttbl');
      $newDeleteParameter = str_replace('-', '/',$deleteParameter);
      $newCaller  = str_replace('-', '/',$caller);
      $data['deleteParameter'] = $newDeleteParameter;
      //$data['deleteParameter'] = $DeleteParameter;
      $data['itemToDelete']  = $itemToDelete;
      $data['caller'] = $newCaller ;
      //$data['caller'] = $Caller;
      $this->load->view("super/template_header",$data);
      $this->load->view("super/confirmationList", $data);
        $this->load->view("super/template_footer",$data);   
      $this->load->view("footer",$data);
  

    }


    function show_LectureTime_list($edit,$msg)
     {
        $data = $this->_set_vars();
    $current_user = $this->session->userdata('current_user');
    if($current_user['logged_in'])
    { 
        
      $data['title'] = $this->app_name." >> Admin Dashboard >> School";         
      $data['LectureTime'] = $this->mconfigs->getAnyTableContent('period');
        $data['msg'] = $msg;
        $data['edit'] = $edit;
      $this->load->view("header",$data);    
      $this->load->view("super/template_header",$data);   
      $this->load->view("super/list_LecturetimePeriod", $data);
      $this->load->view("super/template_footer",$data);   
      $this->load->view("footer",$data);
    }   
    else 
    {
      redirect('start/index');
    }

     }

     function insert_lecture_time()
     {
      if(!empty($_POST['day'])) 
       { 
        $current_user = $this->session->userdata('current_user');
           $dday = $this->input->post('day');
           $data['LectureTime'] = $this->mconfigs->getAnyTableContent('period');
           $res =  $this->mconfigsc->getAnyTableRow('days', $dday, 'freeperiod');
        $msg = "-" ;
         if( $res->num_rows() > 0 )
        {   
            $mmsg = 1;
            $msg = "Free period-already-Exist for the day" ; 
            redirect('constraint/show_freeperiod/0');  
        }

           $dDescription = $this->input->post('Description');
           $sstarttime =  $this->input->post('starttime');
           $ffduration = $this->input->post('fduration');
           $aaPeriodInt = $this->input->post('aPeriodInt');
       $sstarttime2 = explode(" ",$sstarttime);
           $sstarttimeAdd =  $sstarttime2[0] + $ffduration;
         
            $sstarttime3 = "am";
        
           
           if($sstarttimeAdd >= 12)
           {
            //echo "done";
            $sstarttime3 = "pm";
           }
          
           if($sstarttimeAdd > 12)
           {
            //echo "done22";
            $sstarttimeAdd = $sstarttimeAdd - 12;
            //$sstarttime2[1] = "pm";
           }
            if($sstarttime2[1] == 'pm')
           {
            //echo "done33";
            $sstarttime3 = "pm";
           }
            
          $lecture_details = array( 'days'=>$dday, 'Description'=>$dDescription, 'TimeFrom'=>$sstarttime2[0], 'TimeTo'=>$sstarttimeAdd, 'TimeFrom_ampm'=>$sstarttime2[1], 'TimeTo_ampm'=>$sstarttime3)   ;
          //$lecture_details = array( 'days'=>$this->input->post('day'), 'Description'=>$this->input->post('Description')   );
          $this->mconfigs->insertIntoAnyTable('periodtime', $lecture_details) ; 
          redirect('constraint/show_LectureTime_list/0');
      
            }
    else{
              redirect('constraint/show_LectureTime_list/0');
      }


     }

     function update_Lecture_Time()
     {
       $fStart = $this->input->post('uStart');
       $tEnd =  $this->input->post('uEnd');
       $fampm = $this->input->post('fromampm');
       $tampm =  $this->input->post('toampm');
       $periodId = $this->input->post('periodId');
       //$remarks = $this->input->post('$rrm');
      // echo $fampm;
       //echo $tampm;
      // die;
       if ($fampm == 2) 
       {
        $fampm = 'pm';
       }
       elseif ($fampm == 1) 
       {
        $fampm = 'am';
       }
        if ($tampm == 1) 
       {
        $tampm = 'am';
       }
       elseif ($tampm == 2) 
       {
        $tampm = 'pm';
       }
      
       $periodsetup = array(  
        'Start'=>$fStart,
        'End'=>$tEnd,
        'TimeFrom_ampm'=>$fampm,
        'TimeTo_ampm'=>$tampm
        //'remarks'=>$remarks
        );

      $this->mconfigs->updateAnyTableRow('id', $periodId, 'period', $periodsetup) ;
    $msg = "Update-Successful" ; 

      redirect('constraint/show_LectureTime_list/0/""');
     }

      function periodalign()
    {
    $data = $this->_set_vars();
    $this->load->view("header",$data);    
    
    $this->load->view("super/template_header",$data);
    $this->load->view("super/test", $data);
      $this->load->view("super/template_footer",$data);   
    $this->load->view("footer",$data);
  

    }

    function periodalignoperation ()
    {
      //echo 'I am here';
      //die;
      $periodDetails = $this->mconfigs->getAnyTableContent('period');
      $freeperiodDetails = $this->mconfigs->getAnyTableContent('freeperiod');
      $maxslot = $this->mconfigs->getAnyTableContent('maxhourperslot');
      $venueDetails = $this->mconfigs->getAnyTableContent('venue');
      //$courseDetails = $this->mconfigs->getAnyTableContent('coursedetails');
      $courseDetails = $this->mconfigs->getAnyTableRow('semester','1st','coursedetails');
      $courseDistinct = $this->mconfigs->getCourseTableDistinct();
      //$coursebydept2 = $_session['coursebydeptt'];
      $coursenn2 = $_SESSION['coursennn'];
      $coursebycode2 = $_SESSION['coursebycodee'];
        $coursebyl2 = $_SESSION['coursebyll'];
        $coursebyp2 = $_SESSION['coursebypp'];
        $coursebydept2 = $_session['coursebydeptt'];
        $coursebylevel2 = $_SESSION['coursebylevell'];
      $coursep2 = $_SESSION['coursepp'];
      $coursebylec2 = $_SESSION['coursebyllec'];
        $coursebylect2 = $_SESSION['coursebyllect'];
      
      $coursebyddeptarray[][] = array();
      $nk[] = array();
      $coursebycodearray[] = array();
      $checkcourse[] = array();
        $checkvenue[] = array();

        $slotkk[] = array();
        $venuenamekk[] = array();
        $coursebycodekk[] = array();
        $venueownedbykk[] = array();

      $m = 1;

      foreach($_SESSION['coursebydeptt'] as $key2) 
      {
         
        $n = 1;
        $checkcourse[$m] = 0;
          foreach ($key2  as $key3) 
        {
          //$n++;
          $coursebyddeptarray[$m][$n] = $key3;
          //echo $key3 . ' ';
          $n++;
        }
        $nk[$m] = $n;
        $m++;
        $check1[$p] = 0;

      }
      
      //getAnyTableRow($idColumnName ,$rowID, $table)
            $freeperioddays = array();
            $freeperiodfrom = array();
            $freeperiodto = array();
            $freeperiodfrom_ampm = array();
            $freeperiodto_ampm = array();
            $slot = array();
            $periodslot = array();
            $dayslotvenuedept = array();
            $courselp = array();
            $slotarray[]= array();
            $venuenamearray[] = array();
            $venueownedbyarray[] = array();
            
             if($venueDetails->num_rows() > 0)
             {
              $venueno = $venueDetails->num_rows();
                 foreach ($venueDetails->result() as $srow) 
                 {  
                  $venueid[] = $srow->venueid;
                  $venuename[] = $srow->name;
                  $venueownedBy[] = $srow->ownedBy;
                  //$venueownedBy1[0] = $srow->explode(',', ownedBy);
                 // $venueownedBy1[1] = $srow->explode(',', ownedBy);
                  //$venueownedByid[] = $venueownedBy1[0];
                 // $venueownedByid[] = $venueownedBy1[1];
                 
                 }
               
             }

             if($courseDetails->num_rows() > 0)
             {
              $courseno = $courseDetails->num_rows();
                 foreach ($courseDetails->result() as $srow) 
                 {  
                  $coursecode[] = $srow->code;
                  $coursel[] = $srow->l;
                  $coursep[] = $srow->p;
                  $courselp[] = $srow->code . '  '. $srow->l. ','. $srow->p. '<br />';
                  
                 }
               
             }

            if($periodDetails->num_rows() > 0)
             {
                 foreach ($periodDetails->result() as $srow) 
                 {  
                  $periodstart = $srow->Start;
                  $periodfrom = $srow->TimeFrom_ampm;
                  $periodend = $srow->End;
                  $periodto = $srow->TimeTo_ampm;
                  $periodstartB = $periodstart;
                 }
                  
             }
             if($freeperiodDetails->num_rows() > 0)
             {
              $noOfdays = $freeperiodDetails->num_rows();
                 foreach ($freeperiodDetails->result() as $srow) 
                 {  
                  $freeperioddays[] = $srow->days;
                  $freeperiodfrom[] = $srow->TimeFrom;
                  $freeperiodfrom_ampm[] = $srow->TimeFrom_ampm;
                  $freeperiodto[] = $srow->TimeTo;
                  $freeperiodto_ampm[] = $srow->TimeTo_ampm;
                  $freeperiodduration[] = $srow->duration;

                 //$freeperiodfromgmt[] = $srow->TimeFrom_gmt;
                 }
                
             }

             if($maxslot->num_rows() > 0)
             {
                 foreach ($maxslot->result() as $srow) 
                 {  
                  $maxhour = $srow->hour;
               }
             }
              $chk = 1;
             if ($periodfrom == 'am' && $periodto == 'pm') 
             {
              $periodendk = $periodend + 12;
             }
             for ($i=0; $i < $noOfdays; $i++) 
             { 
              $chk = 1;
              if ($freeperioddays[$i] != 'xyz') 
              {
                 $freeperiodfromkk = $freeperiodfrom[$i];
                 if ($freeperiodfrom_ampm[$i] == 'pm') 
                 {
                  $freeperiodfromkk = $freeperiodfromkk + 12 ;
                 }
                if ($maxhour == 1) 
                {
                  $freeperioddurationk = $freeperiodduration[$i];
                  $periodstartk = $periodstart;
                  $periodstartk2 = $periodstart;

                  for ($j=$periodstartk2; $j <= $periodendk; $j++) 
                  { 
                    $periodstartend = $periodstartk + 1;
                    if ($j != $freeperiodfromkk) 
                    {
                      $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                      $periodstartk = $periodstartk + 1;
                    
                    }else
                    {
                      $periodstartk = $periodstartk + $freeperioddurationk;
                      $j = $j + $freeperioddurationk;
                    }
                  }
    
                }
                if ($maxhour >= 2) 
                {
                  $freeperioddurationk = $freeperiodduration[$i];
                  $periodstartk = $periodstart;
                  $periodstartk2 = $periodstart;
                  for ($j=$periodstartk2; $j < $periodendk; $j+=$maxhour) 
                  { 
                    
                    $periodstartend = $periodstartk + $maxhour;
                    if ($periodstartend <= $freeperiodfromkk) 
                    {
                      $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                      $periodstartk = $periodstartk + $maxhour;
                      
                    }else
                    {
                      if ($chk == 1) 
                      {
                        $slot[] = $freeperioddays[$i].$periodstartk.'-'. $freeperiodfromkk;
                        $periodstartk = $periodstartk + $freeperioddurationk + ($freeperiodfromkk - $periodstartk);
                        $j = $j + $freeperioddurationk + ($freeperiodfromkk - $periodstartk);
                        $chk = 2;

                      }else
                      {
                        if ($periodstartend < $periodendk) 
                        {
                          $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                          $periodstartk = $periodstartk + $maxhour;
                        }else
                        {
                          $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodendk . "<br />";
                        }
                      }
                      

                    }
                  }
                 
                } 

             }
           
      }
      $slotcount = count($slot);
      
      $k = 1;
      for ($i=0; $i < $slotcount; $i++) 
      { 
        for ($j=0; $j < $venueno; $j++) 
        { 
          $dayslotvenuedept[] = $slot[$i]. ' '. $venuename[$j]. ' ' . $venueownedBy[$j] . "<br />";
          $slotarray[$k] = $slot[$i];
          $venuenamearray[$k]= $venuename[$j];
          $venueownedbyarray[$k] = $venueownedBy[$j];
          $checkvenue[$k] = 0;
          //$check1[$k] = 0;
          $k++;
        }
      }
      $k= $k -1;
      //print_r($dayslotvenuedept);
      //echo '<br />';
        //echo '<br />';
      //echo $k-1;
      //exit;
            foreach ($courseDistinct->result() as $row)
            {
                 $coursedist2[] = $row->code . '<br \>';
            }

            //echo $coursebycode2[$ii]. '-';
        //echo $coursebylect2[$ii]. ' '. ' ' ;
        //echo $coursep2[$ii];
        //$nk[$m];
            // scheduling ...............................
            $kk = 1;
            for ($p=1; $p<=$coursenn2; $p++) 
            { 
              
              if ($checkcourse[$p] == 0 && $coursep2[$p] >= 6) 
              {
                for ($d=1; $d <= $k; $d++)
                {

                  if ($checkvenue[$d] == 0 && $venueownedbyarray[$d] = 'G')
                  {
                    
                    /*echo $p;
                     echo '<br />';
                    echo $checkcourse[$p];
                    echo '<br />';
                    echo $coursep2[$p];
                    echo '<br />';
                    echo $checkvenue[$d];
                    echo '<br />';
                    echo $venueownedbyarray[$d];

          exit;*/
                    $coursebycodekk[$kk] = $coursebycode2[$p];
                    $venuenamekk[$kk] = $venuenamearray[$d];
                    $slotkk[$kk] = $slotarray[$d];
                    $venueownedbykk[$kk] = $venueownedbyarray[$d];
                    $checkvenue[$d] = 1;
                    $checkcourse[$p] = 1;
                    $kk++;
                    break;
                    //$p++;
                    //$d++;
                    

                  }
                }
              }

                elseif ($checkcourse[$p] == 0 && $coursep2[$p] >= 2 && $coursep2[$p] <= 5) 
                {
                  for ($d=1; $d <= $k; $d++)
                  {
                    if ($checkvenue[$d] == 0 && $venueownedbyarray[$d] = 'S')
                    {
                      $coursebycodekk[$kk] = $coursebycode2[$p];
                      $venuenamekk[$kk] = $venuenamearray[$d];
                      $slotkk[$kk] = $slotarray[$d];
                      $venueownedbykk[$kk] = $venueownedbyarray[$d];
                      $checkvenue[$d] = 1;
                      $checkcourse[$p] = 1;
                      $kk++;
                      break;
                      //$p++;
                    //  $d++;
                      

                    }
                  }
                }
                elseif ($checkcourse[$p] == 0 && $coursep2[$p] < 2) 
                {
                  for ($d=1; $d <= $k; $d++)
                  {
                    if ($checkvenue[$d] == 0 && $venueownedbyarray[$d] = 'D')
                    {
                      $coursebycodekk[$kk] = $coursebycode2[$p];
                      $venuenamekk[$kk]  = $venuenamearray[$d];
                      $slotkk[$kk] = $slotarray[$d];
                      $venueownedbykk[$kk] = $venueownedbyarray[$d];
                      $checkvenue[$d] = 1;
                      $checkcourse[$p] = 1;
                      $kk++;
                      break;
                      //$p++;
                    //  $d++;
                    
                    }
                  }
                }

              
              
            }
         
           // exit;
            for ($tt=0; $tt < $kk; $tt++) 
            { 
              echo $tt . ' '; 
              echo $coursebycodekk[$tt]. ' ';
        echo $venuenamekk[$tt]. ' ';
        echo $slotkk[$tt]. ' ';
        echo $venueownedbykk[$tt];
        echo '<br />';
            }
           // exit;
            print_r($coursebycodekk);
            echo '<br />';
            echo '<br />';
            print_r($venuenamekk);
            echo '<br />';
            echo '<br />';
            print_r($slotkk);
            echo '<br />';
            
             //echo $k;
             echo '<br />';
            print_r($venueownedbykk);
             //echo $coursenn2;
             //echo $slotkk[4];


             
  }

  function periodsetup()
  {
    //echo 'here';
    //exit;
    $periodDetails = $this->mconfigs->getAnyTableContent('period');
      $freeperiodDetails = $this->mconfigs->getAnyTableContent('freeperiod');
      $maxslot = $this->mconfigs->getAnyTableContent('maxhourperslot');
      $venueDetails = $this->mconfigs->getAnyTableContent('venue');
      //$courseDetails = $this->mconfigs->getAnyTableContent('coursedetails');
      $courseDetails = $this->mconfigs->getAnyTableRow('semester','1st','coursedetails');
      $courseDistinct = $this->mconfigs->getCourseTableDistinct();

    if($periodDetails->num_rows() > 0)
             {
                 foreach ($periodDetails->result() as $srow) 
                 {  
                  $periodstart = $srow->Start;
                  $periodfrom = $srow->TimeFrom_ampm;
                  $periodend = $srow->End;
                  $periodto = $srow->TimeTo_ampm;
                  $periodstartB = $periodstart;
                 }
                  
             }
             if($freeperiodDetails->num_rows() > 0)
             {
              $noOfdays = $freeperiodDetails->num_rows();
                 foreach ($freeperiodDetails->result() as $srow) 
                 {  
                  $freeperioddays[] = $srow->days;
                  $freeperiodfrom[] = $srow->TimeFrom;
                  $freeperiodfrom_ampm[] = $srow->TimeFrom_ampm;
                  $freeperiodto[] = $srow->TimeTo;
                  $freeperiodto_ampm[] = $srow->TimeTo_ampm;
                  $freeperiodduration[] = $srow->duration;

                 //$freeperiodfromgmt[] = $srow->TimeFrom_gmt;
                 }
                
             }

             if($maxslot->num_rows() > 0)
             {
                 foreach ($maxslot->result() as $srow) 
                 {  
                  $maxhour = $srow->hour;
               }
             }
              $chk = 1;
             if ($periodfrom == 'am' && $periodto == 'pm') 
             {
              $periodendk = $periodend + 12;
             }
             for ($i=0; $i < $noOfdays; $i++) 
             { 
              $chk = 1;
              if ($freeperioddays[$i] != 'xyz') 
              {
                 $freeperiodfromkk = $freeperiodfrom[$i];
                 if ($freeperiodfrom_ampm[$i] == 'pm') 
                 {
                  $freeperiodfromkk = $freeperiodfromkk + 12 ;
                 }
                if ($maxhour == 1) 
                {
                  $freeperioddurationk = $freeperiodduration[$i];
                  $periodstartk = $periodstart;
                  $periodstartk2 = $periodstart;

                  for ($j=$periodstartk2; $j <= $periodendk; $j++) 
                  { 
                    $periodstartend = $periodstartk + 1;
                    if ($j != $freeperiodfromkk) 
                    {
                      $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                      $slotonly[] = $periodstartk.'-'. $periodstartend;
                      $periodstartk = $periodstartk + 1;
                    
                    }else
                    {
                      $periodstartk = $periodstartk + $freeperioddurationk;
                      $j = $j + $freeperioddurationk;
                    }
                  }
    
                }
                if ($maxhour >= 2) 
                {
                  $freeperioddurationk = $freeperiodduration[$i];
                  $periodstartk = $periodstart;
                  $periodstartk2 = $periodstart;
                  for ($j=$periodstartk2; $j < $periodendk; $j+=$maxhour) 
                  { 
                    
                    $periodstartend = $periodstartk + $maxhour;
                    if ($periodstartend <= $freeperiodfromkk) 
                    {
                      $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                      $slotonly[] = $periodstartk.'-'. $periodstartend;
                      $periodstartk = $periodstartk + $maxhour;
                      
                    }else
                    {
                      if ($chk == 1) 
                      {
                        $slot[] = $freeperioddays[$i].$periodstartk.'-'. $freeperiodfromkk;
                        $slotonly[] = $periodstartk.'-'. $freeperiodfromkk;
                        $periodstartk = $periodstartk + $freeperioddurationk + ($freeperiodfromkk - $periodstartk);
                        $j = $j + $freeperioddurationk + ($freeperiodfromkk - $periodstartk);
                        $chk = 2;

                      }else
                      {
                        if ($periodstartend < $periodendk) 
                        {
                          $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodstartend;
                          $slotonly[] = $periodstartk.'-'. $periodstartend;
                          $periodstartk = $periodstartk + $maxhour;
                        }else
                        {
                          $slot[] = $freeperioddays[$i].$periodstartk.'-'. $periodendk . "<br />";
                          $slotonly[] = $periodstartk.'-'. $periodendk . "<br />";
                        }
                      }
                      

                    }
                  }
                 
                } 

             }
           
      }
      //print_r($slot); to be continuing========
      $slotcount = count($slot);
      $slotdis =  array_values(array_unique($slotonly));
      $slotdiscount = count($slotdis);
      for ($ck=0; $ck < $slotdiscount ; $ck++) 
      { 
        $slotexpl = explode("-", $slotdis[$ck]);
        if (intval($slotexpl[0]) > 12) 
        {
          $slotexplL = intval($slotexpl[0]) - 12 .'pm';

        }else
        {
          $slotexplL = $slotexpl[0]. 'am';
        }
        if (intval($slotexpl[1]) > 12) 
        {
          $slotexplR = intval($slotexpl[1]) - 12 . 'pm';

        }else
        {
          $slotexplR = $slotexpl[1] . 'am';
        }
        $slotdisNew[] = $slotexplL. '-' . $slotexplR;
      }
      
      $k = 1;
      for ($i=0; $i < $slotcount; $i++) 
      { 
        echo $slotdisNew[$i]. '<br />';
        
        
      }
      print_r($slotdisNew);
      //exit;
      $_SESSION['slotdisNeww'] = $slotdisNew;
      return $slotdisNew;
  }


}
?>
