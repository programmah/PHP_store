<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mconfigs extends CI_Model 
{    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
    function getAnyTableContent($table)
     {
        $query = $this->db->get($table);
      
        return $query;
     }
     function getAnyTableRow($idColumnName ,$rowID, $table)
     {
         
        if(!empty($rowID))
        {
            $query = $this->db->get_where($table, array($idColumnName=>$rowID));
            return $query;
        }

     }
    function getAnyTableRowWithArrayValue(array $columnAndValue, $table)
     { 
            $query = $this->db->get_where($table, $columnAndValue);
            return $query;
     }
      function getAnyTableContentOrderByColumn($table, $orderColumn)
    {
       $sql = 'select * from '.$table.'  order by '.$orderColumn;
       $query = $this->db->query($sql); 
       return $query;
    }
    function getSemester()
   {
       $semester ="";
       $sql = 'select semester from semestertbl';
       $query = $this->db->query($sql);
       if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $semester = $row->semester;
            }
        }
        return  $semester;

   }
   function getSeivedColumnItemList($table, $columnName,$IDcolumnName,$idValue)
   {
       $colList = array();
       $query = $this->db->get_where($table,array($IDcolumnName=>$idValue));
        if($query->num_rows()> 0)
        {
              foreach ($query->result() as  $col)
               {
                    $colList[] = $col->$columnName;   
               }

        } 
        return $colList;
 } 

  function getAnyTableSingleValueUsingArrayCondtn($table, array $condtnColAndValue, $getColumn)
     {      
           // $limit = 1;
            $col = "";
            $query = $this->db->get_where($table, $condtnColAndValue);
            if($query->num_rows() > 0)
                {
                  foreach ($query->result() as $row)
                  {
                       $col = $row->$getColumn;
                  }
               }
        
       return $col;
     }
  function getAnyColumnByID($table,$getColumn,$value_colName, $value)
   {
    $limit = 1;
    $col = "";
    $query = $this->db->get_where($table,array($value_colName=>$value),$limit);
     //   return $query->row_array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $col = $row->$getColumn;
            }
        }
        
       return $col;
 } 
   function getSlotByDate($date)
  {
    $slot ="";
    $sql = 'select numOfSlot from examdates where dayDates =?'; 
     $query = $this->db->query($sql,$date);
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $slot = $row->numOfSlot;
            }
        }
        return $slot ; 
  }
   function getTotalDeptOfferedCourse($course)
   {
     $sql = 'select * from courseswithdepts where coursecode ="'.$course.'"';
     $query = $this->db->query($sql); 
     // $totalDept = $query->num_rows();
      return $query;
   }

     function insertIntoAnyTable($table, $content)
     {
       $success = $this->db->insert($table, $content);
       return $success;

     }

     function updateAnyTableRow($idColumnName, $rowID, $table, $content)
     {
         $this->db->where($idColumnName, $rowID);
         $this->db->update($table, $content); 
     
     }

 function deleteAnyRowFromAnyTable($idColumnName, $rowID, $table)
    {
           $this->db->delete($table, array($idColumnName =>  $rowID));
               
    }
  function clearTableContent($tableName)
  {
      $this->db->empty_table($tableName);
  }
  function getSchoolByID($sid)
  {
    $sCode ="";
    $sql = 'select skoolcode from skool where schoolID=?';
       $query = $this->db->query($sql,$sid);
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $sCode = $row->skoolcode;
            }

        }
        
       return $sCode;
 }
 function getMaxSlot()
  {
    $slot ="";
    $sql = 'select max(numOfslot) as max from examdates' ;
       $query = $this->db->query($sql);
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $slot = $row->max;
            }

        }
        
       return $slot;
 }
  function sievedTwoTableByColumns($Fetchtable, $FetchtableColumn, $OtherTable, $OtherTableColumn,$fetchColumn)
  {
     $sql = 'select '.$Fetchtable.'.'.$fetchColumn.' from '.$Fetchtable.' , '.$OtherTable.' where "'.$Fetchtable.'.'.$FetchtableColumn.'" != "'.$OtherTable.'.'.$OtherTableColumn.'"';
      $query = $this->db->query($sql);
      return $query;
  }
 function getSchoolIDByCode($scode)
  {
    $sid= 0;
    $sql = 'select sid from skool where skoolcode="?"';
       $query = $this->db->query($sql,$scode);
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $sid = $row->skoolcode;
            }
        }
        
       return $sid;
 }
  
 
 function getAnyColumnItemList($table, $columnName)
 {
     $colList = array();
     $query = $this->db->get($table);
    if($query->num_rows()> 0)
    {
          foreach ($query->result() as  $col)
           {
                $colList[] = $col->$columnName;   
           }

    } 
     if( count($colList) == 1 )
        return $colList[0];
    else
       return $colList;
 } 
 

 function checkDuplicateDate($id, $date)
 {
   $IDdate = array($id, $date);
   $sql = 'select dayDates from examdates where dayid <>? and dayDates=?' ;
   $query = $this->db->query($sql, $IDdate);
   return $query;
 }
// ...........................................................................
  function getLtheatreDetailsForLectrTymeAllocation( $dayID,$theatreID)
  {
       $query = $this->db->get_where('timetable', array('td_id'=>$dayID, 'tl_id'=>$theatreID));
            return $query;
  }
  function getTimeTableDetails( $dayID,$semester)
  {
       $query = $this->db->get_where('timetable', array('td_id'=>$dayID, 'semester'=>$semester));
            return $query;
  }

  function getTheatreCategoryDescrptnByID($lid)
  {
     $limit = 1;
    $result = array(2);
    $query = $this->db->get_where('ltheatre',array('lid'=>$lid),$limit);
     //   return $query->row_array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $result[0] = $row->category;
                 $result[1] = $row->description;
            }
        }
        
       return $result;
 }

 function courseCategoryDept($cid)
 {
    $limit = 1;
    $result = array(2);
    $query = $this->db->get_where('courses',array('cid'=>$cid),$limit);
     //   return $query->row_array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $result[0] = $row->category;
                 $result[1] = $row->dept;
            }
        }
        
       return $result;
 }
  
 function getSkoolIdByDept($dept)
 {
   $limit = 1;
    $skoolID ="";
    $query = $this->db->get_where('department',array('dept'=>$dept),$limit);
     //   return $query->row_array();
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $skoolID = $row->sid;
                 
            }
        }
        
       return $skoolID;

 }
 
 function checkIfCoursecatHasNotBinAssignd4Dweek($category ,$semester)
 {
    $status = 0 ; // denote all courses in the category has been assigned or none in that category has been created yet
    $query = $this->db->get_where('courses',array('category'=>$category, 'semester'=> $semester));
      if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
               $query2 = $this->db->get_where('timetable',array('semester'=>$semester,'tc_id'=>$row->cid));
                if($query2->num_rows() <= 0)  // implies not found
                {
                   $status = 1 ;   // there is still a course in the category not yet assigned
                   break;
                }
                 
            }
        }

       return $status ; 
 }
 
 function checkIfCourseHasBinSchedule4DdayB4($cid, $did, $semester)
 {
   $query = $this->db->get_where('timetable',array('semester'=>$semester,'tc_id'=>$cid, 'td_id'=>$did));
   return $query;
 }
 
 function getTimeTableRowNameByID($id,$idColumnName,$table, $returnColumn)
  {
    $query = $this->db->get_where($table,array($idColumnName=>$id));
    $limit = 1;
    $columnName ="";
    
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $columnName = $row->$returnColumn;
                 
            }
        }
        
       return $columnName;   
 
  }
   function updatePreferences($valueArr)
   {
     $colArr = array('population','department','school','100level','400level','500level','priority','totalcourseperday','restriction');
       for ($i=0; $i < count($colArr) ; $i++) 
        { 
           $content = array( 'value'=>$valueArr[$i] ) ;
          $this->db->where('name',$colArr[$i] );
          $this->db->update('preferences', $content); 
        
        }
   }

//Load Number of slot based on the selected date
//----------------------------------------------
function loadSlots()
{
    $dsDate=$this->input->post('dsDate');
  
   $queryStr="SELECT * FROM examdates WHERE dayDates=?";
   $result = $this->db->query($queryStr,array($dsDate));
   if ($result->num_rows()>0)
   {
    foreach($result->result() as $row) 
        {
      
         return $row->numOfSlot;


        }
     }

  }
  function loadDepartments()
{
    $dsSchool=$this->input->post('sch');
 
   $queryStr="SELECT * FROM department WHERE schoolID = ?";
   $schoolID =  $this->mconfigs->getAnyColumnByID('school','schoolCode','schoolID', $dsSchool);
                   
   $result = $this->db->query($queryStr,array($schoolID));

   if ($result->num_rows()>0)
   {
    foreach($result->result() as $row) 
        {
      
         return $row->deptCode;


        }
     }

  }

//// added codes by tunde  ==========================
///===========================================================

   function getAnyTableContentDistinct()
   {
      $query = $this->db->query("SELECT distinct dayDate FROM courseallocationtbl");
      return $query;
   }


   function getCourseTableDistinct()
   {
      $coursebycode = array();
      $coursep = array();
      $coursebylevel = array();

     //$query = $this->db->query("SELECT * FROM course where semester = '1st' group by code");
      $query = $this->db->query("SELECT distinct code FROM distinctcourse where semester = 1 and l != 0 and p <= 6");
      if($query->num_rows() > 0)
       {
            $coursenn = $query->num_rows();
            
            foreach ($query->result() as $row)
            {
               $coursedist[] = $row->code;
               $coursebylec[] = $row->l;
            }
            for ($i=1; $i < $coursenn ; $i++) 
            { 
                $query2 = $this->db->query("SELECT * FROM distinctcourse where code = '$coursedist[$i]' and semester = 1");
                if($query2->num_rows() > 0)
                {
                    $j = 0;
                    
                    $coursep[$i] = $query2->num_rows();
                   // print_r($coursep);
                    foreach ($query2->result() as $row)
                    {
                      ++$j;
                      $coursebylect[$i] = $row->l;
                      $coursebycode[$i] = $row->code;
                      $coursebyl[$i][$j] = $row->l;
                      $coursebyp[$i][$j] = $row->p;
                      $coursebylevel[$i][$j] = $row->level;
                      $coursebydept[$i][$j] = $row->dept;
                      
                    }
                  
                }
                    
            }
             
                
        }
       
        $_SESSION['coursennn'] = $coursenn;
        $_SESSION['coursepp'] = $coursep;
        $_SESSION['coursebycodee'] = $coursebycode;
        $_SESSION['coursebyll'] = $coursebyl;
        $_SESSION['coursebypp'] = $coursebyp;
        $_SESSION['coursebylevell'] = $coursebylevel;
        $_SESSION['coursebydeptt'] = $coursebydept;

        $_SESSION['coursebyllec'] = $coursebylec;
        $_SESSION['coursebyllect'] = $coursebylect;
        
        //print_r($_SESSION['coursebydeptt']);
       // print_r($_SESSION['coursebycodee']);

        return $query;
   }


  function allocationvenuesummary()
  {
    $this->db->empty_table('allocationvenuesummary');
    $qstrng = $this->db->query("SELECT distinct venue FROM allocationvenue");
    if ($qstrng->num_rows() > 0)
    {
      $venuecount = $qstrng->num_rows();
      $row = $qstrng->row();
      foreach ($qstrng->result() as $row) 
      {
        $venuek[] = $row->venue;
      }
    }
    $kk = 0;
    $deptcount[$kk] = 0;
    do 
    {
      $deptcountc = 0;
      $qstrng4 = $this->db->query("SELECT * FROM allocationvenue where venue = '$venuek[$kk]'");
      if ($qstrng4->num_rows() > 0)
      {
        $deptcountc = $qstrng4->num_rows();
        $department = array();
        $noofdept[$kk] = $deptcountc;
        foreach ($qstrng4->result() as $row) 
        {
          $department[] = $row->department;
          $venueCapacity[] = $row->venueCapacity;
        }
      }
      $deptc = "";
      for ($i=0; $i < $deptcountc; $i++) 
      { 
        $deptc = $deptc.$department[$i].';';
      }

      $this->db->set('venue', $venuek[$kk]);
      $this->db->set('noofdept', $noofdept[$kk]);
      $this->db->set('department', $deptc);
      $this->db->set('venueCapacity', $venueCapacity[$kk]);
      $this->db->insert('allocationvenuesummary');
      
      $kk = $kk + 1;
     
    }
    while($kk <= $venuecount - 1);
   
    $qstrng = $this->db->query("SELECT * FROM allocationvenuesummary");
    return $qstrng;
  }

   function getDateListBySlot($slot)
  {
    $dateList = array();
    $sql = 'select dayDates from examdates where numOfSlot >="?"'; 
     $query = $this->db->query($sql,$slot);
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                 $dateList[] = $row->dayDates;
            }
        }
        return $dateList ;
  }

 function insertBatchArrayIntoAnyTable($table , $arrayOfAssocArray)
     {
        $success = $this->db->insert_batch($table, $arrayOfAssocArray);
        return $success;
     }
  function courseAllocationPreviewBtbl($ddate)
  {
      $this->db->empty_table('courseallocationtblbydate');
      //$qstrng = $this->db->query("SELECT distinct slot FROM courseallocationtbl where date = '$ddate'");
      $qstrng = $this->db->query("SELECT distinct slot,dayDate FROM courseallocationtbl where dayDate = '$ddate' ");
      if ($qstrng->num_rows() > 0)
      {
          $venuecount = $qstrng->num_rows();
          //$row = $qstrng->row();
          foreach ($qstrng->result() as $row) 
          {
              // $ddate[] = $row->date;
              $slot[] = $row->slot;
          }

          //Traversing $slot array
          //----------------------
          $setCountTag=1;
          foreach ($slot as $getSlot) 
          {

              $qstrng = $this->db->query("SELECT * FROM courseallocationtbl where dayDate = '$ddate' AND slot=$getSlot  ORDER BY course ASC");
              if ($qstrng->num_rows() > 0)
              {
            
                  //Get all the courses found
                  //--------------------------
                  $course=array();

                  foreach ($qstrng->result() as $row) 
                  {
                    $course[] = $row->course;
                  }
                  
                  
                  $setCount=0;    
                  foreach ($course as $getCourse)
                  {
                        $setCount++;
                        $slotField="slot".$getSlot;
                        //Insertion operation is performed for the first selected slot
                        //------------------------------------------------------------
                        if ($setCountTag==1)
                        {                              
                              $this->db->set($slotField, $getCourse);  
                              $this->db->set('indextag', $setCount);  
                              $this->db->set('dayDate', $ddate); 
                              $this->db->insert('courseallocationtblbydate');
                        }    
                        else
                        {
                            //Check for existence of indextag equal current setCount Value
                            //-------------------------------------------------------------
                            $qstrng = $this->db->query("SELECT indextag FROM courseallocationtblbydate where indextag = $setCount");
                            if ($qstrng->num_rows() > 0)
                            {
                                $qstrng = $this->db->query("UPDATE courseallocationtblbydate SET $slotField='$getCourse' WHERE indextag = $setCount");
                            }
                            else
                            {                                
                                $this->db->set($slotField, $getCourse);  
                                $this->db->set('indextag', $setCount); 
                                $this->db->set('dayDate', $ddate);  
                                $this->db->insert('courseallocationtblbydate');                              
                            }
                        }
                  }
                  //End of inner foreach 
              }
              $setCountTag=2;
          }
          //End of outer foreach
      }
        $qstrng = $this->db->query("SELECT * FROM courseallocationtblbydate order by indextag asc");
        return $qstrng;
     // echo('DONE');
     // die;
   
}
//Load Number of slot based on the selected date
//----------------------------------------------


}
?>
