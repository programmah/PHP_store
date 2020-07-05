<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	

class admin extends CI_Controller
{
	var $base;
	var $css_custom;
	var $css_bootstrap;
	var $css_bootstrap_responsive;
	var $image_url;
	var $numeric;
	var $app_name;
	var $jquery;
	function admin()
	{
		parent::__construct();
		$this->base = $this->config->item('base_url');
		$this->css_custom = $this->config->item('css_custom');
		$this->css_bootstrap =  $this->config->item('css_bootstrap');
		$this->app_name = $this->config->item('app_name');
		$this->css_bootstrap_responsive =  $this->config->item('css_bootstrap_responsive');	
		$this->numeric = 	$this->config->item('numeric') ;
		$this->jquery = 	$this->config->item('jquery') ;
	}
	function index()
	{    error_reporting('E_WARNING^E_NOTICE^E_ALL') ;    
		$data = $this->_set_vars();
		$data['title']= $this->app_name." >> Welcome!";
		$this->load->view("header",$data);		
		//$this->load->view("welcome", $data);
		$this->load->view('login',$data);
		
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
	function show_users_list()
	{
		$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
			//$current_user = $this->session->userdata('current_user');
			// $data['current_user'] = $current_user;	
			// $data['current_user']['login_status'] = $current_user['logged_in']? "Logged In" : "Not Logged In";
			$data['title'] = $this->app_name." >> Admin Dashboard >> Users";					
			$data['admin_users'] = $this->musers->get_users();			
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/users/list", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	}
	function insert_new_user()
	{
		if((!empty($_POST['username']))  && (!empty($_POST['password']))) 
				{					
					$user = array(
						  'user_id'=>$this->input->post('username'),
						  'password'=>$this->input->post('password')
						  );
					$success = $this->simpleloginsecure->create($user['user_id'],$user['password'], false);					
					redirect('admin/show_users_list');
				}
		else
				redirect('admin/show_users_list');
	}
	function delete_user($user_id)
	{
		$this->simpleloginsecure->delete($user_id);
		redirect('admin/show_users_list');
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
// bash  start

 function show_createdClass($class_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> level";					
			$data['classCreated'] = $this->mconfigs->getAnyTableContent('classTbl');
			$data['class_edit'] = $class_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_lecturedays", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }

  function insert_createdClasses()
   {
         $current_user = $this->session->userdata('current_user');
		if(!empty($_POST['class'])) 
				{	
					$lecture_details = array( 'class'=>$this->input->post('class')  );
					$this->mconfigs->insertIntoAnyTable('classTbl', $lecture_details) ;	
					redirect('admin/show_createdClass/0');
		
			    }
		    else{
			    	redirect('admin/show_createdClass/0');
		    	}
   }

  function update_createdClasses($id)
  {
  	    $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
          if ((!empty($_POST['day']))    )
			  {
                     $lecture_details = array( 'class'=>$this->input->post('class')  );
					$this->mconfigs->updateAnyTableRow('id', $id, 'classTbl', $lecture_details) ; 
                     redirect('admin/show_createdClass/0');
             }	
          else
		    {
		            redirect('admin/show_createdClass/'.$id.'/?end='.$end.'&click='.$click);
		    }
         }   	
		else 
		{
			redirect('start/index');
		}
  }
   
   function delete_createdClass($id)
   {
   	  $this->mconfigs->deleteAnyRowFromAnyTable('id', $id,'classTbl') ;	
		redirect('admin/show_createdClass/0');
   }

    function confirm_delete($deleteParameter, $itemToDelete, $caller)
    {
    		$data = $this->_set_vars();
    		$this->load->view("header",$data);		
			//$data['constraintDetails'] = $this->mconfigs->getAnyTableContent('constrainttbl');
			$newDeleteParameter = str_replace('-', '/',$deleteParameter);
		//	$newCaller  = str_replace('-', '/',$caller);
			$data['deleteParameter'] = $newDeleteParameter;
			$data['itemToDelete']  = str_replace('%20',' ', $itemToDelete);
			$data['caller'] = $caller;
		//	$data['caller'] = $newCaller ;
			$this->load->view("super/template_header",$data);
			$this->load->view("super/confirmationList", $data);
		    $this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
	

    }

function show_language_list($lang_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> Languages";					
			$data['langDetails'] = $this->mconfigs->getAnyTableContent('optionTbl');
			$data['lang_edit'] = $option_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_languages", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }

function show_reponseOption_list($option_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> Options";					
			$data['optionDetails'] = $this->mconfigs->getAnyTableContent('optionTbl');
			$data['option_edit'] = $option_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_response_option", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }
  function insert_responseOption()
     {
            $current_user = $this->session->userdata('current_user');
		if(!empty($_POST['option'])) 
				{	
					$option_details = array( 'options'=>$this->input->post('option'),
						'weight'=>$this->input->post('weight')   );
					$this->mconfigs->insertIntoAnyTable('optionTbl', $option_details) ;
					$msg = "level-successfully-inserted" ;   
		
					redirect('admin/show_reponseOption_list/0');
		
			 }
		else{
				   redirect('admin/show_reponseOption_list/0');
			}
     }
 function update_responseOption($optId)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
          if ((!empty($_POST['option']))    )
			  {
       $option_details = array(
						  'options'=>$this->input->post('option'),
						  'weight'=>$this->input->post('weight') 
						  );
					$this->mconfigs->updateAnyTableRow('optId', $optId, 'optionTbl', $option_details) ; 
                     redirect('admin/show_reponseOption_list/0');
           }	
            else
		           {
		           	  redirect('admin/show_reponseOption_list/'.$optId.'/?end='.$end.'&click='.$click);
		           }
         }   	
		else 
		{
			redirect('start/index');
		}
     }


   function delete_reponseOption($optId)
     {
     	// $click = $this->input->get('click') ;
       //  $end = $this->input->get('end') ;

     	 $this->mconfigs->deleteAnyRowFromAnyTable('optId', $optId,'optionTbl') ;	
		 redirect('admin/show_reponseOption_list/0');
     } 

function show_level_list($level_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> level";					
			$data['levelDetails'] = $this->mconfigs->getAnyTableContentOrderByColumn('level', 'level');
			$data['level_edit'] = $level_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_level", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }
   


 function insert_level_details()
     {
            $current_user = $this->session->userdata('current_user');
		if(!empty($_POST['levelName'])) 
				{	
					$level_details = array( 'level'=>$this->input->post('levelName')  );
					$this->mconfigs->insertIntoAnyTable('level', $level_details) ;
					$msg = "level-successfully-inserted" ;   
		
					redirect('admin/show_level_list/0');
		
			 }
		else{
				redirect('admin/show_level_list/0');
			}
     }


function update_level_details($sid)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
          if ((!empty($_POST['levelName']))    )
			  {
       $level_details = array(
						  'level'=>$this->input->post('levelName'),
						  );
					$this->mconfigs->updateAnyTableRow('levelID', $sid, 'level', $level_details) ; 
                     redirect('admin/show_level_list/0');
           }	
            else
		           {
		           	  redirect('admin/show_level_list/'.$sid.'/?end='.$end.'&click='.$click);
		           }
         }   	
		else 
		{
			redirect('start/index');
		}
     }


  function delete_level($levelID)
     {
     	// $click = $this->input->get('click') ;
       //  $end = $this->input->get('end') ;

     	 $this->mconfigs->deleteAnyRowFromAnyTable('levelID', $levelID,'level') ;	
		redirect('admin/show_level_list/0');
     }  

function show_teacher_list($teacher_edit)
	  {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
       if(!empty($_GET['click']) )
			{	
		            $data['click']= $this->input->get('click') ;
			 		
			 		$data['end'] = $this->input->get('end') ;		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> Teachers List";					

			$semester              = $this->mconfigs->getSemester();  
			$data['levelDetails'] = $this->mconfigs->getAnyColumnItemList('level','level'); 
			//$data['department']        = $this->mconfigs->getAnyTableContent('department');
			$data['classList'] = $this->mconfigs->getAnyColumnItemList('classTbl','class');
			$data['teacherName']   = $this->mconfigs->getAnyTableContent('teacherTbl');
			
			$data['teacher_edit'] = $teacher_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_course_group", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	}

	function insert_new_teacher()
     {
            $current_user = $this->session->userdata('current_user');

        
				    $file_name='';
				 $config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'gif|jpg|png';
                $config['max_size']             = 2048000;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('userfile'))
                {
                        $error = array('error' => $this->upload->display_errors());

                     //   $this->load->view('admin/show_coursegroup_list/0', $error);
                        redirect( 'admin/show_teacher_list/0', $error);
                }
                else
                {
                        $data =  $this->upload->data();
                        $file_name  =     $data['file_name'];
                     //   $this->load->view('upload_success', $data);
                        if((!empty($_POST['teacherName'])))
		                   {	
                              $group_details=array(
									  	  	 'teacherName' =>$this->input->post('teacherName'),
									  	  	 'imagePath'=>$file_name
				  	  	                 );
			          $this->mconfigs->insertIntoAnyTable('teacherTbl', $group_details) ;
						$msg = "insertion-successful" ;
				
				
				       $click = $this->input->get('click') ;
                      $end = $this->input->get('end') ;   
				      redirect('admin/show_teacher_list/0/?end='.$end.'&click='.$click);
			        }
                }
                      
                   
	

		
		redirect('admin/show_coursegroup_list/0');
	}
 /* function getDepartmentListOfSelectedCourse($courseID)
  {    
  	   $course = $this->mconfigs->getAnyColumnByID('distinctcourse','code','id', $courseID) ;  	   
  	   $where = array('coursecode'=>$course);
  	   $query = $this->mconfigs->getAnyTableRowWithArrayValue($where, 'courseswithdepts');
  	   foreach ($query->result() as  $row) 
  	   {
  	   	  $deptList[] =   $row->deptcode;
  	   }
  	   return $deptList; 
  }
*/
function teacher_details($tID,$teacherName, $dcdCaller)
   {
   	    $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
               
              $imgPath="";         
            $data['teacherDetails'] =  $this->mconfigs->getAnyTableRow('tID', $tID,'teacherdetailsTbl') ;
             $query   = $this->mconfigs->getAnyTableRow('tID', $tID, 'teacherTbl') ;
            foreach ($query->result() as  $row) 
  	        {
  	   	       $imgPath =   $row->imagePath;
  	        }
  	         $data['title'] = $this->app_name." >> Admin Dashboard >> Teacher";
  	         $data['levelList']= $this->mconfigs->getAnyColumnItemList('level', 'level');
  	         $data['classList']= $this->mconfigs->getAnyColumnItemList('classTbl', 'class');
            $data['imagePath']  = $imgPath;
            $data['caller']         =  $dcdCaller;
            $data['teacherName']      =  str_replace('%20',' ', $teacherName);
            $data['teacherID']        = $tID ;
            $this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_coursegroup_department", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
        }
       else
        {
          redirect('start/index');
        }       

   }

  function Add_class_level()
      {
      	         $levelList      = array();
      	         $caller         = $this->input->post('caller');
      	         $teacherID      = $this->input->post('teacherID');
      	         $teacherName    = $this->input->post('teacherName');
      	         $class          = $this->input->post('class');
				 $count          = $this->input->post('count');
				  for($j = 0; $j < $count; $j++)
				  {
				     if($this->input->post("level".$j) !="")
				     {
				       echo $this->input->post("level".$j);
                        $levelList[] = $this->input->post("level".$j);
                         $teacher_details=array(
									  	  	 'tID' =>$teacherID,
									  	  	 'level'=> $this->input->post("level".$j),
									  	  	 'class'=>$class
				  	  	                 );
			         $this->mconfigs->insertIntoAnyTable('teacherdetailsTbl', $teacher_details) ;
				     }
				  }
                   //   redirect('admin/detail_coursegroup_department/'.$teacherID.'/'.$teacherName.'/'.$caller);
                  // }
                  redirect('admin/teacher_details/'.$teacherID.'/'.$teacherName.'/'.$caller);

      }

 function update_teacherName($teacherID)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
			$click = $this->input->get('click') ;
      
	 		$end = $this->input->get('end') ;
		 if ((!empty($_POST['teacherName'])) )
			  {
				
                 $teacher_details = array(
						        
							     'teacherName' =>$this->input->post('teacherName')

						   );
					$this->mconfigs->updateAnyTableRow('tID', $teacherID, 'teacherTbl', $teacher_details) ; 
                     redirect('admin/show_teacher_list/0/?end='.$end.'&click='.$click);
             }
           else
		      {
		          redirect('admin/show_teacher_list/'.$teacherID.'/?end='.$end.'&click='.$click);
		      }
         }   		
		else 
		{
			redirect('start/index');
		}
     }
function delete_teacher_details($td_ID,$tID,$teacherName)
  {
  	     $caller='admin-show_teacher_list-0';
  	     $this->mconfigs->deleteAnyRowFromAnyTable('td_ID', $td_ID,'teacherdetailsTbl');		
		 redirect('admin/teacher_details/'.$tID.'/'.$teacherName.'/'.$caller);
  }
 function delete_teacher($tID)
     {
     
     	 $this->mconfigs->deleteAnyRowFromAnyTable('tID', $tID,'teacherTbl') ;
     	 $this->mconfigs->deleteAnyRowFromAnyTable('tID', $tID,'teacherdetailsTbl');		
		 redirect('admin/show_teacher_list/0');
     }  
    
  function show_student_list($stud_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			if(!empty($_GET['click']) )
			{
	
		            $data['click']= $this->input->get('click') ;
			 		
			 		$data['end'] = $this->input->get('end') ;
		    
			}
			else
			{
			  $data['click'] = "";
		    }
			$data['title'] = $this->app_name." >> Admin Dashboard >> School";					
			$data['student'] = $this->mconfigs->getAnyTableContent('studentTbl');
			$data['stud_edit'] = $stud_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_schools", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }

    function insert_student_details()
     {
            $current_user = $this->session->userdata('current_user');
		if((!empty($_POST['studReg']))  && (!empty($_POST['studName']))  )
				{	
				  
				 $res =  $this->mconfigs->getAnyTableRow('studReg' ,strtoupper($this->input->post('studReg')), 'studentTbl');
			     $msg = "-" ;
			     if( $res->num_rows() > 0 )
			     	{   
                      $msg = "skool-already-Exist" ;   
			     	}
			     else
			     {
			     	$dob = $this->input->post('year').'.'.$this->input->post('month').'.'.$this->input->post('day');
					$student_details = array(						  
						  'studReg'=>$this->input->post('studReg'),
						  'studName'=>$this->input->post('studName'),
						  'studDob' => $dob
						  );
					$this->mconfigs->insertIntoAnyTable('studentTbl', $student_details) ;
					$msg = "student-successfully-inserted" ;   
				}
					redirect('admin/show_student_list/0');
		
			 }
		else{
				redirect('admin/show_student_list/0');
			}
     }


     function update_student_details($studReg)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{ 
             $click = $this->input->get('click') ;
      
	 		$end = $this->input->get('end') ;
         if ((!empty($_POST['studName'])) )
			  {
			  	
			  	$dob = $this->input->post('year').'.'.$this->input->post('month').'.'.$this->input->post('day');
                 $student_details = array(
						  
						  'studName'=>$this->input->post('studName'),
						  'studDob'=> $dob
						  );
                
					$this->mconfigs->updateAnyTableRow('studReg', $studReg, 'studentTbl', $student_details) ; 
                     redirect('admin/show_student_list/0?end='.$end.'&click='.$click);
             }
         else
		     {
		      	  redirect('admin/show_student_list/'.$studReg.'/?end='.$end.'&click='.$click);
	         }
         }   		
		else 
		{
			redirect('start/index');
		}
     }

     function delete_student($studReg)
     {
     	 $this->mconfigs->deleteAnyRowFromAnyTable('studReg', $studReg,'studentTbl') ;	
		redirect('admin/show_student_list/0');
     }
    
function show_questionaire_list($quest_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
        if(!empty($_GET['click']) )
			{	
		             $data['click']= $this->input->get('click') ;			 		
			 		 $data['end'] = $this->input->get('end') ;		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> Questionaire";					
			$data['questDetails'] = $this->mconfigs->getAnyTableContent('questionaireListTbl');
			//$data['levelDetails'] =$this->mconfigs->getAnyTableContentOrderByColumn('level', 'level');
			//$data['deptDetails'] = $this->mconfigs->getAnyTableContent('Department');
			
			$data['quest_edit'] = $quest_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_questionaireList", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }

 function insert_questionaireList()
     {
     	 
            $current_user = $this->session->userdata('current_user');       
		if(!empty($_POST['question']))
				{		$quest_details = array('question'=>$this->input->post('question'),
					                            'optiontype'=>$this->input->post('optiontype'),
												'createdDate'=>date('Y-m-d')
											 );
					 
						$this->mconfigs->insertIntoAnyTable('questionaireListTbl', $quest_details) ;
						$msg = "insertion-successful" ;  
				 

					 $click = $this->input->get('click') ;
                     $end = $this->input->get('end') ;
					redirect('admin/show_questionaire_list/0/?end='.$end.'&click='.$click);
		
			 }
		else{
				redirect('admin/show_questionaire_list/0');
			}
     }
function update_questionaireList($questID)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
			if (!empty($_POST['question']) )
			  {
         
     				$quest_details = array('question'=>$this->input->post('question'),
					                            'optiontype'=>$this->input->post('optiontype'),
												
											 );
					$this->mconfigs->updateAnyTableRow('questID', $questID, 'questionaireListTbl', $quest_details) ; 
		                     redirect('admin/show_questionaire_list/0/?end='.$end.'&click='.$click);
             }
           else
		      {
		           redirect('admin/show_questionaire_list/'.$questID.'/?end='.$end.'&click='.$click);
		      }
         }   		
		else 
		{
			redirect('start/index');
		}
     }
    function delete_questionaireList($courseID)
    {
     	// $click = $this->input->get('click') ;
       //  $end = $this->input->get('end') ;

     	 $this->mconfigs->deleteAnyRowFromAnyTable('id', $courseID,'distinctcourse') ;	
		redirect('admin/show_course_list/0');
    }

  function show_preview_list()
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
        if(!empty($_GET['click']) )
			{	
		             $data['click']= $this->input->get('click') ;			 		
			 		 $data['end'] = $this->input->get('end') ;		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> Questionaire";					
			$data['questDetails'] = $this->mconfigs->getAnyTableContent('questionaireListTbl');
			$data['optionDetails'] =$this->mconfigs->getAnyTableContent('optiontbl');
			//$data['deptDetails'] = $this->mconfigs->getAnyTableContent('Department');
			
		//	$data['quest_edit'] = $quest_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_preview", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }








 
   function show_course_offered_list($coursedept_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
       if(!empty($_GET['click']) )
			{
	
		            $data['click']= $this->input->get('click') ;
			 		
			 		$data['end'] = $this->input->get('end') ;
		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> level";					
			$data['courseDeptDetails'] = $this->mconfigs->getAnyTableContent('courseswithdepts');
			$data['levelDetails'] =$this->mconfigs->getAnyTableContentOrderByColumn('level', 'level');
			$data['deptDetails'] = $this->mconfigs->getAnyTableContent('Department');
			$data['school'] = $this->mconfigs->getAnyTableContent('school');
			$data['courseDetails'] = $this->mconfigs->getAnyTableContent('distinctcourse');
			$data['coursedept_edit'] = $coursedept_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_course_offered_dept", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }
  
    function update_course_offered_details($courseID)
     {
     	
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		
		if($current_user['logged_in'])
		{    
			if(!empty($_GET['click']) )
			{	
		             $click = $this->input->get('click') ;			 		
			 		 $end   = $this->input->get('end') ;		    
			}
			else
			{
			  $data['click'] = "";
		    }

		//	if ((!empty($_POST['courseCode'])) )
		//	  {
         
                $course_details = array('coursecode'=>strtoupper($this->input->post('courseCode')),
			                              'level'=>strtoupper($this->input->post('level')),
			                              'deptcode'=>strtoupper($this->input->post('department')),
			                              'schcode'=>strtoupper($this->input->post('schoolCode'))
                                       );
					$this->mconfigs->updateAnyTableRow('id', $courseID, 'courseswithdepts', $course_details) ; 
		                     redirect('admin/show_course_offered_list/0/?end='.$end.'&click='.$click);
         //    }
        //    else
		//     {
		            redirect('admin/show_course_offered_list/'.$courseID.'/?end='.$end.'&click='.$click);
		//     }
         }   		
		else 
		{
			redirect('start/index');
		}
     }

    function delete_courseoffered($courseID)
     {
     	 $click = $this->input->get('click');
         $end   = $this->input->get('end');
     	 $this->mconfigs->deleteAnyRowFromAnyTable('groupID',$courseID,'courseswithdepts');	
		 redirect('admin/show_course_offered_list/0/?end='.$end.'&click='.$click);
     } 

	 function show_course_list($course_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
        if(!empty($_GET['click']) )
			{	
		             $data['click']= $this->input->get('click') ;			 		
			 		 $data['end'] = $this->input->get('end') ;		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> level";					
			$data['courseDetails'] = $this->mconfigs->getAnyTableContent('distinctcourse');
			$data['levelDetails'] =$this->mconfigs->getAnyTableContentOrderByColumn('level', 'level');
			$data['deptDetails'] = $this->mconfigs->getAnyTableContent('Department');
			
			$data['course_edit'] = $course_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_courses", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }
	 function show_unAllocated_list($unAllocated_edit)
	 {
         
	 	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			
			$data['title'] = $this->app_name." >> Admin Dashboard >> unAllocated";					
			$data['unAllocatedDetails'] = $this->mconfigs->getAnyTableContent('unAllocatedCourseTbl');
			$data['unAllocated_edit'] = $unAllocated_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_unAllocated", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }

	 function insert_course_details()
     {
     	 
            $current_user = $this->session->userdata('current_user');       
		if(!empty($_POST['code']) && !empty($_POST['unit']) && !empty($_POST['semester']) && !empty($_POST['l']) )
				{	
					  $res =  $this->mconfigs->getAnyTableRow('code' ,strtoupper($this->input->post('code')), 'distinctcourse');
				 
				$msg = "-" ;
			     if( $res->num_rows() > 0 )
			     	{   
                      $msg = "Course-already-Exist" ;   
			     	}
			     else
			      {
						$course_details = array('code'=>strtoupper($this->input->post('code')),
												'unit'=>strtoupper($this->input->post('unit')),
												'semester'=>$this->input->post('semester'),
												'l'=>$this->input->post('l'),
												't'=>$this->input->post('t'),
												'p'=>$this->input->post('p')
												 );
					 
						$this->mconfigs->insertIntoAnyTable('distinctcourse', $course_details) ;
						$msg = "insertion-successful" ;  
				   }

					 $click = $this->input->get('click') ;
                     $end = $this->input->get('end') ;
					redirect('admin/show_course_list/0/?end='.$end.'&click='.$click);
		
			 }
		else{
				redirect('admin/show_course_list/0');
			}
     }
function update_course_details($courseID)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
			if (!empty($_POST['code']) && !empty($_POST['unit']) && !empty($_POST['semester']) && !empty($_POST['l']) )
			  {
         
     				$course_details = array('code'=>strtoupper($this->input->post('code')),
												'unit'=>strtoupper($this->input->post('unit')),
												'semester'=>$this->input->post('semester'),
												'l'=>$this->input->post('l'),
												't'=>$this->input->post('t'),
												'p'=>$this->input->post('p')
												 );
					$this->mconfigs->updateAnyTableRow('id', $courseID, 'distinctcourse', $course_details) ; 
		                     redirect('admin/show_course_list/0/?end='.$end.'&click='.$click);
             }
           else
		      {
		           redirect('admin/show_course_list/'.$courseID.'/?end='.$end.'&click='.$click);
		      }
         }   		
		else 
		{
			redirect('start/index');
		}
     }
    function delete_course($courseID)
    {
     	// $click = $this->input->get('click') ;
       //  $end = $this->input->get('end') ;

     	 $this->mconfigs->deleteAnyRowFromAnyTable('id', $courseID,'distinctcourse') ;	
		redirect('admin/show_course_list/0');
    }

 
  
function getSlotNumber()
     {
     	$result=$this->mconfigs->loadSlots();
     	echo $result;
       
     }
     function getDepartmentNumber()
     {
     	$result=$this->mconfigs->loadDepartments();
     
       
     }
//---end	
	
//used
     function show_department_list($dept_edit)
	  {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			if(!empty($_GET['click']) )
			{
	
		            $data['click']= $this->input->get('click') ;
			 		
			 		$data['end'] = $this->input->get('end') ;
		    
			}
			else
			{
			  $data['click'] = "";
		    }

			$data['title'] = $this->app_name." >> Admin Dashboard >> Department";					
			$data['deptDetails'] = $this->mconfigs->getAnyTableContent('Department');
			$data['skoolDetails'] = $this->mconfigs->getAnyTableContent('school');
             $data['dept_edit'] = $dept_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_dept", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	}

//  used   
     function update_department_details($deptID)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
			$click = $this->input->get('click') ;
      
	 		$end = $this->input->get('end') ;
        

			  if ((!empty($_POST['deptName']))  && (!empty($_POST['deptCode']))&& (!empty($_POST['schoolID']))  )
			  {
	    	    	
	    	   	    
					
		           $dept_details = array(
		        				  'schoolID'=>strtoupper($this->input->post('schoolID')),
								  'deptCode'=>strtoupper($this->input->post('deptCode')),
								  'deptName'=>strtoupper($this->input->post('deptName')),
								  'maxStudent'=>strtoupper($this->input->post('maxStudent'))
								  
								   );
							$this->mconfigs->updateAnyTableRow('deptID', $deptID, 'department', $dept_details) ; 
		                     redirect('admin/show_department_list/0/?end='.$end.'&click='.$click);
		           }
		           else
		           {
		           	  redirect('admin/show_department_list/'.$deptID.'/?end='.$end.'&click='.$click);
		        }
         }   
        		
		else 
		{
			redirect('start/index');
		}
     }
  
  //used  
    function insert_department_details()
     {
            $current_user = $this->session->userdata('current_user');
		if((!empty($_POST['deptName']))  && (!empty($_POST['deptCode']))&& (!empty($_POST['schoolID']))  )
				{	
				  
				$res =  $this->mconfigs->getAnyTableRow('deptCode' ,strtoupper($this->input->post('deptCode')), 'department');
			     $msg = "-" ;
			     if( $res->num_rows() > 0 )
			     	{   
                      $msg = "department-already-Exist" ;   
			     	}
			     else
			      {
						$dept_details = array(
        				  'schoolID'=>strtoupper($this->input->post('schoolID')),
						  'deptCode'=>strtoupper($this->input->post('deptCode')),
						  'deptName'=>strtoupper($this->input->post('deptName')),
						  'maxStudent'=>strtoupper($this->input->post('maxStudent'))
						  
						   );
					 
						$this->mconfigs->insertIntoAnyTable('department', $dept_details) ;
						$msg = "insertion-successful" ;  
				   }

					 $click = $this->input->get('click') ;
                     $end = $this->input->get('end') ;
					redirect('admin/show_department_list/0/?end='.$end.'&click='.$click);
		
			 }
		else{
				redirect('admin/show_department_list/0');
			}
     }
// used
    function delete_department($deptID)
     {
     	 $click = $this->input->get('click') ;
         $end = $this->input->get('end') ;

     	 $this->mconfigs->deleteAnyRowFromAnyTable('deptID', $deptID,'department') ;	
		redirect('admin/show_department_list/0/?end='.$end.'&click='.$click);
     }  
     //mini tasks
//used
  
  

    function deleteAllocationMasterList($vid)
     {
     	 $click  = $this->input->get('click') ;
         $end    = $this->input->get('end') ; 
         $Dvalue = $this->input->get('Dvalue') ;
         $Vvalue = $this->input->get('Vvalue') ;


          $this->mconfigs->deleteAnyRowFromAnyTable('vID',$vid,'allocationvenue') ;

          $res = $this->mconfigs->getAnyTableRow('department',$Dvalue, 'allocationvenue');
          
          	 if($res->num_rows() == 0)
          	 {
          	 	    $dept_details = array('name'=>$Dvalue,'type'=>'D' );

				    $this->mconfigs->insertIntoAnyTable('unassignlist', $dept_details) ; 
          	 }
          
           $res2 = $this->mconfigs->getAnyTableRow('venue',$Vvalue, 'allocationvenue');
            if($res2->num_rows() == 0)
          	 {
          	 	    $venue_details = array('name'=>$Vvalue,'type'=>'V' );

				    $this->mconfigs->insertIntoAnyTable('unassignlist', $venue_details) ; 
          	 }
          
           redirect('admin/venueAllocationMasterList/0/?end='.$end.'&click='.$click);
           
     }

     function allocateUnassigneVenueOrDept($type)
     {
          $click = $this->input->get('click') ;
          $end = $this->input->get('end') ;

          
          $total = $this->input->post('totalValue');
         
           switch ($type) 
           {
             	case 'D':
                    for($i = 1; $i <= $total; $i++)
                      {
                          $value  =  explode(',', $this->input->post("venueList".$i));
                          $dept_details = array('department'=>$value[0],'venue'=>$value[1] );

						  $this->mconfigs->insertIntoAnyTable('allocationvenue', $dept_details) ; 
						  $this->mconfigs->deleteAnyRowFromAnyTable('name',$value[1] ,'unassignlist') ;
                      }
                      $this->mconfigs->deleteAnyRowFromAnyTable('type', 'D','unassignlist') ;
           		    break;
            	case 'V':
                    for($i = 1; $i <= $total; $i++)
                      {
                         
                          $value  =   explode(',', $this->input->post("deptCodeList".$i));
                          $dept_details = array('department'=>$value[1],'venue'=>$value[0] );

						  $this->mconfigs->insertIntoAnyTable('allocationvenue', $dept_details) ; 
						  $this->mconfigs->deleteAnyRowFromAnyTable('name', $value[1],'unassignlist') ;
                      }
                      $this->mconfigs->deleteAnyRowFromAnyTable('type', 'V','unassignlist') ;
           		   break;
           	default:
           		# code...
           		break;
           }
           
           redirect('admin/venueAllocationMasterList/0/?end='.$end.'&click='.$click);
            	
       
     } 
    
     function venueAllocationMasterList($allo_edit)
     {

     	$data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{	
   			if(!empty($_GET['click']))
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

			$data['title'] = $this->app_name." >> Admin Dashboard >> Venue Allocation";					
			$allocationList = $this->mconfigs->getAnyTableContent('allocationvenue'); 
            $unassignDeptListArr = $this->mconfigs->getSeivedColumnItemList('unassignlist', 'name','type','D');
            $unassignVenListArr = $this->mconfigs->getSeivedColumnItemList('unassignlist', 'name','type','V');
            	$AlloObj   = new SetAllocation();
      			$DeptList  = $AlloObj->getAllDepartmentDetails();
        		$VenueList = $AlloObj->getAllVenueDetails();

                $data['allocationList']       = $allocationList;
		        
		        $data['unassignDeptListArr'] = $unassignDeptListArr;
		        $data['unassignVenListArr']  = $unassignVenListArr;
		        $data['VenueList']			 = $VenueList;
		  		$data['DeptList']			 = $DeptList;
		  		$data['allo_edit']           = $allo_edit;
 
			    $this->load->view("header",$data);		
			    $this->load->view("super/template_header",$data);		
			    $this->load->view("super/finalAllocationList", $data);
			    $this->load->view("super/template_footer",$data);		
			    $this->load->view("footer",$data);   
		}		
		else 
		{
			redirect('start/index');
		}
     }
    function updateMasterAllocation($vid)
    {
         $data = $this->_set_vars();
		 $current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
         
                $click = $this->input->get('click') ;
                $end = $this->input->get('end') ;
               
                $venue =  $_REQUEST['venueCodeList']  ;
                $dept =  $_REQUEST['deptCodeList'];
                $oldVenueCode = $_REQUEST['oldVenueID'];
                $oldDeptCode  = $_REQUEST['oldDeptID'];  
            

            $venue_details = array( 'department'=>strtoupper($dept), 'venue'=>strtoupper($venue) );
			$this->mconfigs->updateAnyTableRow('vID', $vid, 'allocationvenue', $venue_details) ; 

                     // update leftOver Department List
                     if($dept != $oldDeptCode)
                     {    
                         $sql = $this->mconfigs->getAnyTableRow('name',$dept, 'unassignlist');
                          if($sql->num_rows() > 0)
                          {
                          	  $this->mconfigs->deleteAnyRowFromAnyTable('name', $dept,'unassignlist') ;  
                          }

                         $res = $this->mconfigs->getAnyTableRow('department',$oldDeptCode, 'allocationvenue');
                          if($res->num_rows() == 0)
                            {
                       	        $dept_details = array('name'=>$oldDeptCode,'type'=>'D' );

						        $this->mconfigs->insertIntoAnyTable('unassignlist', $dept_details) ; 
                            }
                         
                         
                     }
                     // update leftover venue list
                     if($venue != $oldVenueCode)
                     {
                     	  $sql2 = $this->mconfigs->getAnyTableRow('name',$venue, 'unassignlist');
                          if($sql2->num_rows() > 0)
                          {
                          	  $this->mconfigs->deleteAnyRowFromAnyTable('name', $venue,'unassignlist') ;  
                          }
                           $res2 = $this->mconfigs->getAnyTableRow('venue' ,$oldVenueCode, 'allocationvenue');
                            if($res2->num_rows() == 0)
                            {
                                $venue_details = array('name'=>$oldVenueCode,'type'=>'V' );

						        $this->mconfigs->insertIntoAnyTable('unassignlist', $venue_details); 
						    }
                     }

               redirect('admin/venueAllocationMasterList/0/?end='.$end.'&click='.$click);
           }		
		else 
		{
			redirect('start/index');
		}

    }
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

     function insert_lecture_time()
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
   function show_maxhourperslot($maxhourperslot_edit)
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
			$data['maxhourperslotDetails'] = $this->mconfigs->getAnyTableContent('maxhourperslot');
			$data['maxhourperslot_edit'] = $maxhourperslot_edit;
			$this->load->view("header",$data);		
			$this->load->view("super/template_header",$data);		
			$this->load->view("super/list_maxhourperslot", $data);
			$this->load->view("super/template_footer",$data);		
			$this->load->view("footer",$data);
		}		
		else 
		{
			redirect('start/index');
		}
	 }
	 function update_maxhourperslot_details($ID)
     {
        $data = $this->_set_vars();
		$current_user = $this->session->userdata('current_user');
		if($current_user['logged_in'])
		{
			$click = $this->input->get('click') ;
      
	 		$end = $this->input->get('end') ;
		    
			
        $maxhourperslot_details = array(
        				  
						  'hour'=>strtoupper($this->input->post('hour')),
						  
						  
						   );
					$this->mconfigs->updateAnyTableRow('ID', $ID, 'maxhourperslot', $maxhourperslot_details) ; 
                     redirect('admin/show_maxhourperslot/0/?end='.$end.'&click='.$click);
           }		
		else 
		{
			redirect('start/index');
		}
     }


}
?>
