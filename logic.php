<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   class logic
   {
      function contains($val, $arr)
       {
        
           foreach( $arr as $a )
            {
               if($a == $val)
               {
                 return true;
               }
            }
            return false;
       }
       function countValue($val, $arr)
       {
           $counter = 0;
           foreach( $arr as $a )
            {
               if($a == $val)
               {
                   $counter++ ;
               }
            }
            return $counter;
       }
     
    function siever( $valueList ) // remove duplicate entry
    {
        $count = 0;
        $sievedList = array();
        foreach ($valueList as $value)
         {
            if(!$this->contains( $value, $sievedList))
               {
                  $sievedList[$count++] = $value;  
               }
         }
         return $sievedList ;
    }
    function randomise($arrayOfValue)
    {
        $index = rand(0, sizeof($arrayOfValue)-1);
      //  echo $index."<br/>";
        $value = $arrayOfValue[$index];
        if(is_array($value))
          return randomise($value);
        else
           return $value;
    }

    public function stringBuilder($deptArr)
     {
          $stringBuilder = "";
          for($i = 0; $i < count($deptArr); $i++) 
          {
               if($i == count($deptArr) - 1)
                    $stringBuilder .= $deptArr[$i] ;
               else
                   $stringBuilder .= $deptArr[$i].',' ;
          }
          return $stringBuilder;
     }

   }


   class VenueParameter
   {
     public $VenueID;
     public $Name;  
     public $OwnerType;
     public $Capacity;
     public $VenueOwner;
     public $Status = false;
   }  
   class Day
   {
      public $DayID;
      public $Days;
   }
   class FreePeriod
   {
      public $Days;
      public $StartPeriod;
      public $Duration;
   }
   class Period
   {
     public $StartTime;
     public $EndTime;
   }
  class Merger
  {
    public $ID;
    public $Day;
    public $Period;
    public $Venue;
    public $VenueCapacity;
    public $OwnerType;
    public $VenueOwner;
    public $Status = 0;
    public $Course;
  }
 class VirtualAllocationTable
   {
       public $CourseCode;
       public $Day;
       public $Period;
       public $Venue;
   }
   
  class TableBoard extends logic
  {
       public function __construct()
       {
          $this->DBobject = new mconfigs();
         
       }
       public function getDays()
       {
         $DayObjArr  = array();
         $lecturedays = $this->DBobject->getAnyTableContent('lecturedays');
          if($lecturedays->num_rows() > 0)
             {
                 foreach ($lecturedays->result() as $row)
                  {
                      $ObjDay        = new Day();
                      $ObjDay->DayID = $row->id;
                      $ObjDay->Days   = $row->days;
                      $DayObjArr[]   = $ObjDay;
                  }
             }
              return  $DayObjArr;
       }
      
       public function getFreePeriod()
       {  
           $ObjFPDarr   = array();
           $freeperiod  = $this->DBobject->getAnyTableContent('freeperiod');
           $ObjFPD      = new FreePeriod();
           if($freeperiod->num_rows() > 0)
            {
               foreach ($freeperiod->result() as $row)
                    {     
                        $ObjFPD              = new FreePeriod();                                  
                        $ObjFPD->Days        = $row->days;
                        $ObjFPD->StartPeriod = $row->startperiod;
                        $ObjFPD->Duration    = $row->duration; 
                        $ObjFPDarr[]         = $ObjFPD ;                      
                    }       
            }
            return $ObjFPDarr;
       }
       public function getPeriod()
       {
           $period = $this->DBobject->getAnyTableContent('period');
           $ObjPD  = new Period();
           if($period->num_rows() > 0)
            {
               foreach ($period->result() as $row)
                    {                                       
                        $ObjPD->StartTime  = $row->start;
                        $ObjPD->EndTime    = $row->end;                       
                    }       
            }
            return $ObjPD;
       }
       private function AmPm($value)
        {
              if($value < 12 && $value > 6)
                  return $value."am";
                elseif($value == 12)
                  return $value."pm";
                elseif($value >= 12)
                  return ($value - 12)."pm" ;

        }
      public function generatePeriodSet($periodLength,Period $ObjPD, array $ObjFPDarr, array $DayObjArr)
      {     
            $id = 1;
            $ObjMergerArr  = array();
            foreach ($DayObjArr as $Eachday)
             { 
                     $firstLoop = 1; 
                     $start = $ObjPD->StartTime[0].$ObjPD->StartTime[1];
                     $end   = $ObjPD->EndTime[0].$ObjPD->EndTime[1];
                          if(strtolower($ObjPD->EndTime[2]) =="p" &&  (int)$end < 12  )
                            {   $end =((int)$end + 12);   }

                  $ObjFPD  = new FreePeriod();
                  $ObjFPD  =  $this->SpotFreeperiod($ObjFPDarr, $Eachday->Days); // get freepriod details for a particular day
                 
                  for($i = (int)$start; $i <= (int)$end; $i+=$periodLength)
                  {                     
                       
                       if($ObjFPD != null) 
                        {  
                          //  $ObjMerger    = new Merger();
                            $period =  (int)$ObjFPD->StartPeriod[0].$ObjFPD->StartPeriod[1]; 
                            $lapse  =  (int)$ObjFPD->Duration;
                            if($i ==(int)$period +12 ) // capture exact period
                              { 
                                 $i += ($lapse - $periodLength); 
                                
                              }
                             elseif(($i+1) == (int)$period+12  && ($periodLength ==2)) //capture in-between period
                              {                                       
                                     if($i+$periodLength <= (int)$end)                                    
                                      { 
                                         $ObjMerger         = new Merger();
                                         $ObjMerger->ID     = $id++;
                                         $ObjMerger->Day    = $Eachday->Days;
                                         $ObjMerger->Period = $this->AmPm($i).'-'.$this->AmPm($i+1);  // 8pm-10pm                                     
                                         $ObjMergerArr[]    = $ObjMerger;
                                         if($lapse ==2 )
                                         { $i+= 1; } // skip free period hour 

                                      }                  
                               }
                              else
                              {
                                        if($i+$periodLength <= (int)$end)
                                         { 
                                            $ObjMerger    = new Merger();
                                            $ObjMerger->ID     = $id++;
                                             $ObjMerger->Day    = $Eachday->Days;
                                            $ObjMerger->Period = $this->AmPm($i).'-'.$this->AmPm($i+$periodLength);
                                            $ObjMergerArr[]    = $ObjMerger;
                                         } // 8pm-10pm
                                       elseif (($i+$periodLength -1) == (int)$end && ($periodLength ==2))
                                        {
                                          $ObjMerger    = new Merger();
                                          $ObjMerger->ID     = $id++;
                                          $ObjMerger->Day    = $Eachday->Days;
                                          $ObjMerger->Period = $this->AmPm($i).'-'.$this->AmPm($i+$periodLength - 1);
                                          $ObjMergerArr[]    = $ObjMerger; 
                                            $i+=1;
                                        }  
                                       
                              }
                        } 
                       else
                       {  
                           
                           if($i+$periodLength <= (int)$end)
                             {  
                                $ObjMerger    = new Merger();
                                $ObjMerger->ID     = $id++;
                                $ObjMerger->Day    = $Eachday->Days;
                                $ObjMerger->Period = $this->AmPm($i).'-'.$this->AmPm($i+$periodLength);
                                $ObjMergerArr[]    = $ObjMerger; 

                              }  // first period format: 8pm-10pm
                            elseif (($i+$periodLength -1) == (int)$end && ($periodLength ==2))
                              {
                                 $ObjMerger    = new Merger();
                                $ObjMerger->ID     = $id++;
                                $ObjMerger->Day    = $Eachday->Days;
                                $ObjMerger->Period = $this->AmPm($i).'-'.$this->AmPm($i+$periodLength - 1);
                                $ObjMergerArr[]    = $ObjMerger; 
                                  $i+=1;
                              }                                                                   
                       }                                            
                  }  // close for loop

             }  // close foreach
             return $ObjMergerArr;   
      }
      private function SpotFreeperiod(array $ObjFPDarr, $Eachday)//argument: freeperiod-object array, day
      {
              $ObjFPD = new FreePeriod();
              $ObjFPD = null;
               foreach ($ObjFPDarr as $FPD)  // loop thru array of free periods
               {
                 
                    if(strtolower($FPD->Days) == strtolower($Eachday)) // check for a day
                    {
                       
                      $ObjFPD = $FPD;         // get the object of that day freeperiod
                       break;                  // terminate the loop
                    }
               }
               return $ObjFPD;
      }
   
    public function getVenue()
     {
          
          $venueDetails =  $this->DBobject->getAnyTableContent('venue');  
          $ArrayOfVenueObject = array(); 
           if($venueDetails->num_rows() > 0)
            {
                $i = 0;
                 foreach ($venueDetails->result() as $row) 
                  {
                     $ObjectOfVenue                = new VenueParameter();
                     $ObjectOfVenue->VenueID       = $row->venueid;
                     $ObjectOfVenue->Name          = $row->name;
                     $ObjectOfVenue->Capacity      = $row->capacity;
                     $ownedBy                      = explode(',', $row->ownedBy) ;

                     if($ownedBy[1] == 'D')
                     {
                        $ObjectOfVenue->VenueOwner   = $this->DBobject->getAnyColumnByID('department','deptCode','deptID', $ownedBy[0]);                
                     }
                     elseif($ownedBy[1] == 'S')
                     {
                       $ObjectOfVenue->VenueOwner   = $this->DBobject->getAnyColumnByID('school','schoolCode','schoolID', $ownedBy[0]);                        
                     }
                     elseif($ownedBy[1] == 'M' )
                     {
                        $ObjectOfVenue->VenueOwner   = 'Medium';
                     }
                     elseif($ownedBy[1] == 'L' )
                     {
                        $ObjectOfVenue->VenueOwner   = 'Large';
                     }
                     $ObjectOfVenue->OwnerType     = $ownedBy[1];
                     $ObjectOfVenue->Status        = false;

                     $ArrayOfVenueObject[$i]       = $ObjectOfVenue;
                     $i++;
                  }
     
           }
       
      return $ArrayOfVenueObject ;
     }
  
     public function attachedVenueToMerger(array $MergerArr, array $VenueObj)
     {
              $newMergerArr = array();
              foreach ($MergerArr as $ObjRow )
              {
                  foreach ($VenueObj as $Vobj)
                  {
                    $ObjMerger                 = new Merger();
                    $ObjMerger->ID             = $ObjRow->ID;
                    $ObjMerger->Day            = $ObjRow->Day;
                    $ObjMerger->Period         = $ObjRow->Period;
                    $ObjMerger->Venue          = $Vobj->Name;
                    $ObjMerger->VenueCapacity  = $Vobj->Capacity;
                    $ObjMerger->OwnerType      = $Vobj->OwnerType;
                    $ObjMerger->VenueOwner     = $Vobj->VenueOwner;
                    $newMergerArr[]            = $ObjMerger;                    
                  }                                     
              }         
          return $newMergerArr;   
     }
      
      private function getCourseWithDept()
      {
          $courseWtDeptDetails =  $this->DBobject->getAnyTableContent('courseswithdepts');  
          $ArrayOfcourseWtDeptObject = array(); 
           if($courseWtDeptDetails->num_rows() > 0)
            {
                
                 foreach ($courseWtDeptDetails->result() as $row) 
                  {
                     $ArrayOfcourseWtDeptObject[] = $row;
                  }
            }

          return  $ArrayOfcourseWtDeptObject;
      }
      public function attachCourseToMerger(array $MergerArr, array $CourseObjArr)  // MAIN ALLOCATION METHOD
      {
          
           $newMergerArr  = array();
            shuffle($MergerArr);
           $newMergerArr  = $this->getPreallocatedCoursesAndConstraint($MergerArr,$CourseObjArr);
           foreach ($CourseObjArr as $Cobj) 
           {    
                 if($Cobj->Status == 0 && $Cobj->L > 0)
                 {
                     foreach ($newMergerArr as $MgObj) 
                     {
                          if($MgObj->Status == 0)
                          {                                                      
                             if($this->checkVenueTypeMatchForCourse($MgObj,$Cobj) ==  1)
                               {
                                  $MgObj->Course   = $Cobj->CourseCode;
                                  $MgObj->Status   = 1; 
                                  $Cobj->Status    = 1;
                                  break;
                              }                             
                         }
                                                           
                     }
                 }
          }
          
           return  $MergerArr;
    }
    public function extractDepartmentFromCourseCode($courseCode)
       {
          return  ($courseCode[0].$courseCode[1].$courseCode[2]);
       }
     private function checkVenueTypeMatchForCourse($MergerRow, $CourseParameterRow)
      {
            
               $dept = $this->extractDepartmentFromCourseCode($CourseParameterRow->CourseCode);  
            if(($MergerRow->OwnerType =='D') && ($CourseParameterRow->TotalNosOfOferedDept == 1) && ($MergerRow->VenueOwner == $dept)) 
                     return 1;
           elseif(($MergerRow->OwnerType =='S') &&  ($CourseParameterRow->TotalNosOfOferedDept < 5) && ($MergerRow->VenueOwner == $CourseParameterRow->School)) 
                   return  1;
              elseif(($MergerRow->OwnerType =='M') &&  ($CourseParameterRow->TotalNosOfOferedDept  < 6 ))
                   return 1;
              elseif(($MergerRow->OwnerType =='L') &&  ($CourseParameterRow->TotalNosOfOferedDept > 6))
                   return 1;
               else
                  return 0;             
              
      }

      private function checkVenueTypeMatchForCourseOnConstraint($MergerRow, $DBrow, array $CourseObjArr)
      {
            $dept      = $this->extractDepartmentFromCourseCode($DBrow->course);
            $arrCondtn = array('coursecode' =>$DBrow->course ,'deptcode'=>$dept);
           $skol       =  $this->DBobject->getAnyTableSingleValueUsingArrayCondtn('courseswithdepts', $arrCondtn, 'schcode');  
           
             foreach ($CourseObjArr as  $row)
              {
                 if($row->CourseCode == $DBrow->course)
                 {
                   if(($MergerRow->OwnerType =='D') && ($row->TotalNosOfOferedDept == 1) && ($MergerRow->VenueOwner == $dept)) 
                          return 1;
                    elseif( ($MergerRow->OwnerType =='S') &&  ($row->TotalNosOfOferedDept < 5) && ($MergerRow->VenueOwner == $skol)) 
                          return  1;
                    elseif(($MergerRow->OwnerType =='M') &&  ($row->TotalNosOfOferedDept  < 6))
                         return 1;
                    elseif(($MergerRow->OwnerType =='L') &&  ($row->TotalNosOfOferedDept > 6))
                         return 1;
                    else
                        return 0;
                 }
              } 
      }
      private function evaluateTimesOfCourseAppearance()
      {

      }
      private function checkLevelDeptClashOnPeriod()
      {

      }
      public function getPreallocatedCoursesAndConstraint(array $MergerArr,array $CourseObjArr)
      {
              $courseAllo = $this->DBobject->getAnyTableContent('courseallocationtbl'); 
              if($courseAllo->num_rows() > 0)
              {
                 foreach($courseAllo->result() as $row)
                  {
                       $this->getForcedAlloCourse($MergerArr, $row);  
                  }
              }
              $const = $this->DBobject->getAnyTableContent('constrainttbl'); 
              if($const->num_rows() > 0)
              {
                 foreach($const->result() as $row)
                  {
                       $this->getConstraintCourse($MergerArr, $row,$CourseObjArr);  
                  }
              }  
           
              return $MergerArr;
      }
      
       private function getForcedAlloCourse(array $MergerArr, $DBrow)
       {
            foreach ($MergerArr as  $MgObj)
            {
               if($MgObj->Day == $DBrow->day && $MgObj->Period == $DBrow->period && $MgObj->Venue == $DBrow->venue)
               {
                  $MgObj->Course = $DBrow->course;
                  $MgObj->Status = true; 
               }
            }
          return $MergerArr;
       }
      private function getConstraintCourse(array $MergerArr, $DBrow, array $CourseObjArr)
      {
          foreach ($MergerArr as  $MgObj)
           {
                  if( strtolower($MgObj->Day) == strtolower($DBrow->day) && $DBrow->code =='D')
                     { 
                       if($this->checkVenueTypeMatchForCourseOnConstraint($MgObj, $DBrow, $CourseObjArr) == 1)
                        { 
                            $MgObj->Course = $DBrow->course; 
                            $MgObj->Status = 1;
                            break; 
                       }
                     }
              elseif($MgObj->Period == $DBrow->period && $DBrow->code =='P')
                    { 
                       if($this->checkVenueTypeMatchForCourseOnConstraint($MgObj, $DBrow, $CourseObjArr) == 1)
                         {
                           $MgObj->Course   = $DBrow->course;
                           $MgObj->Status = 1;
                           break; 
                        }
                    }
              elseif($MgObj->Venue  == $DBrow->venue && $DBrow->code  =='V')
                    { 
                        $MgObj->Course   = $DBrow->course;
                        $MgObj->Status = 1; 
                        break; 
                    }
               elseif(strtolower($MgObj->Day)   == strtolower($DBrow->day) && $MgObj->Period   == $DBrow->period && $DBrow->code =='DP')
                    { 
                       if($this->checkVenueTypeMatchForCourseOnConstraint($MgObj, $DBrow, $CourseObjArr) ==1)
                        {
                          $MgObj->Course   = $DBrow->course;
                          $MgObj->Status = 1;
                          break;
                        }
                    }
               elseif(strtolower($MgObj->Day)   == strtolower($DBrow->day) && $MgObj->Venue    == $DBrow->venue && $DBrow->code  =='DV')
                    { 
                        $MgObj->Course   = $DBrow->course; 
                        $MgObj->Status = 1;
                        break; 
                    }
              elseif($MgObj->Venue  == $DBrow->venue && $MgObj->Period == $DBrow->period && $DBrow->code =='VP')
                    { 
                        $MgObj->Course   = $DBrow->course;
                        $MgObj->Status = 1;
                        break;  
                    }
           }
           return $MergerArr;
      }
   

    

  }
 

 class CourseParameter extends TableBoard
   {
     public $CourseID;
     public $CourseCode;
     public $ListOfOferredDepartment = array();
     public $ListOfCorespondingLevel = array();
     public $TotalNosOfOferedDept;
     public $AllocateTimes = 0;
     public $L;
     public $School;
     public $Status = 0;
    // public $P;
     
     public function getCourse()
     {  
        $courseIDarr       = array();
        $courseGroupDBObj  = array();
        $arrayOfDept       = array( );  
        $semester          = $this->DBobject->getSemester();       
        $sql               = $this->DBobject->getAnyTableRow('semester' ,$semester,'distinctcourse'); 
        $coursegroup       = $this->DBobject->getAnyTableContent('coursegroup');
        if($coursegroup->num_rows() > 0)
          { 
            foreach($coursegroup->result() as $row) 
            {  
                $courseIDarr[] =  $row->cID;
                $courseGroupDBObj[]    = $row ;
            }  
          }
        $counter = 0;
        if($sql->num_rows() > 0)
        {
             foreach($sql->result() as $row)
              {   
              //   while ($counter < 50)
              //   {
                    if($this->contains($row->id, $courseIDarr))
                    { 
                        foreach ($courseGroupDBObj as  $DBrow)
                         {
                                if($DBrow->cID  == $row->id)
                                 {   
                                    $CBoardObj  =  new $this();
                                    $CBoardObj->CourseCode               =  $DBrow->courseGroupName;
                                    $arrayOfDept                         =  explode(',', $DBrow->department);
                                    $CBoardObj->TotalNosOfOferedDept     =  count($arrayOfDept);
                                  echo  $CBoardObj->L                        =  $row->l;
                                    $CBoardObj->ListOfOferredDepartment  =  $arrayOfDept;  
                                    $CBoardObj->ListOfCorespondingLevel  =  $this->getCourseGroupLevel($row->code, $arrayOfDept) ;
                                    $CourseObj[]                         =  $CBoardObj;
                                 } 
                         }
                                            
                    }
                    else
                    {
                        $CBoardObj  = new $this();
                        $CBoardObj->CourseCode                = $row->code;
                        $CBoardObj->TotalNosOfOferedDept      = $this->DBobject->getTotalDeptOfferedCourse($row->code)->num_rows();
                        $CBoardObj->L                         = $row->l;
                        $CBoardObj->ListOfOferredDepartment   = $this->DBobject->getSeivedColumnItemList('courseswithdepts', 'deptcode','coursecode',$row->code);
                        $CBoardObj->ListOfCorespondingLevel   = $this->DBobject->getSeivedColumnItemList('courseswithdepts', 'level','coursecode',$row->code);
                        $dept                                 = $this->extractDepartmentFromCourseCode($row->code);
                        $arrCondtn                            = array('coursecode' =>$row->code ,'deptcode'=>$dept);
                        $CBoardObj->School                    =  $this->DBobject->getAnyTableSingleValueUsingArrayCondtn('courseswithdepts', $arrCondtn, 'schcode');  

                        $CourseObj[]                          =  $CBoardObj;
                   }
              //     $counter++;
          //    } // end-while
              }

        }
        
        $CourseArr = $this->sortCourseInfo($CourseObj);
         return $CourseArr ;
     }
   
      private function getCourseGroupLevel($code, array $deptArr)
      {
               $levelArr = array();
               $lvl =''; 
                     foreach ($deptArr as  $dept)
                      {
                         $where = array('coursecode'=>$code,'deptcode'=> $dept);
                         $query = $this->DBobject->getAnyTableRowWithArrayValue($where, 'courseswithdepts');
                          foreach ($query->result() as $row) 
                          {
                            $lvl = $row->level;
                          }
                           $levelArr[] = $lvl;
                      }
              return $levelArr;                 
      }
      private function sortCourseInfo(array $CourseArr)
       {
                for( $j= 0; $j< count($CourseArr); $j++ )
                 {
                      for($k= 0; $k < count($CourseArr)-1; $k++)
                      {
                         if( $CourseArr[$k]->TotalNosOfOferedDept < $CourseArr[$k+1]->TotalNosOfOferedDept ) 
                           {
                              $tempObj         = $CourseArr[$k];
                              $CourseArr[$k]   = $CourseArr[$k+1];
                              $CourseArr[$k+1] = $tempObj;
                           }
                      }
                 } 
                 return $CourseArr ;
       }
    
     
   } 
 

///******* new code ends here ****////
class ConstraintClass extends logic
{
    public $ConstObj = array();
    private $ItemList = array();
    public $CourseOnConstraint = array();
   public function __construct( )
    {
      //  $this->ConstObj = $ConstObj; 
        $this->DBobject = new mconfigs();
    }  
   
   public function getAllConstraintItem()
   {
       
      $query = $this->DBobject->getAnyTableContentOrderByColumn('constrainttbl', 'rank'); 
      if($query->num_rows() > 0)
      {
         foreach ($query->result() as $row)
          {              
             $this->ItemList[] =  $row->item;
              if($row->code != "GD" ) // excluding group courses
              {
                  $this->CourseOnConstraint[] = $row->item;  
              }
          }  
      } 
       
   }
    public function SetConstraintObject()
    {
           $distinctList =  $this->siever($this->ItemList) ;
           foreach ($distinctList  as $item) 
           {
                 $query       = $this->DBobject->getAnyTableRow('item' ,$item, 'constrainttbl');
                 $paraObject  =  new ConstraintParameter();
                foreach ($query->result() as  $row)
                 {
                     
                      if($row->code == "DS")  // date and slot
                       {
                          $paraObject->ID     = $row->id; 
                          $paraObject->Item[] = $row->item;
                          $paraObject->Constraint[] = $row->constraint;
                          $paraObject->Code   = $row->code;
                          $paraObject->Status = false;
                       }
                       elseif($row->code == "GD" )  // group
                       {
                          $paraObject->ID     = $row->id;  
                          $paraObject->Item   = $row->item;
                          $paraObject->Constraint[] = $row->constraint;
                          $paraObject->Code   = $row->code;
                          $paraObject->Status = false;
                          $sql = $this->DBobject->getAnyTableRow('groupName', $paraObject->Item, 'group');
                      
                          foreach ($sql->result() as  $res)  // getting level belonging to the group
                           {
                              if(!$this->contains($res->level,$paraObject->Level))
                                 {
                                    $paraObject->Level[]   = $res->level;
                                    
                                 }
                           
                           }
                       }
                       else   // slot or date
                       {
                          $paraObject->ID = $row->id; 
                          $paraObject->Item = $row->item;
                          $paraObject->Constraint[] = $row->constraint;
                          $paraObject->Code = $row->code;
                          $paraObject->Status = false;
                       }
                                    
                } 
                   $this->ConstObj[] = $paraObject;
           }

      }
     public function getGroupLevelList(array $levelArr)  // returns array of array
     {  
         $courseListObj = array();
        $semester = $this->DBobject->getAnyColumnItemList('semesterTbl', 'semester');
          foreach ($levelArr as  $level) 
          {
              $columnValue = array('level' =>$level ,'semester'=>$semester ); 
              $query = $this->DBobject->getAnyTableRowWithArrayValue($columnValue, 'course'); 
           
              $courseListArr =  array();
             foreach ($query->result() as $row)
              {
                  if(!$this->contains($row->code, $this->CourseOnConstraint))
                    {
                        $courseListArr[] = $row->code;
                    } 
              }
            
                $distinctList    =  $this->siever($courseListArr);
                $courseListObj = array_merge($courseListObj, $distinctList) ;
          }
        
           return  $courseListObj;
     }
      
      public function getConstraintList()
      {
          return $this->ConstObj;
      }

}
 class AllocateConstriantCoures extends logic
 {
     public function __construct()
      {
           $this->DBobject = new mconfigs();
          // $this->DBobject->clearTableContent('courseallocationtbl') ;
      }

      public function allocateCourseOnSlotOnly($course, $slot)
      {
            $date =  $this->getRandomDateBySlot($slot);
           $query = $this->DBobject->getTotalDeptOfferedCourse($course);
           $totalDept = $query->num_rows();
          // echo $totalDept."<br/>";
            $details = array('dayDate'=> $date, 'slot'=>$slot,'course'=>$course,'totalDept'=>$totalDept);
            $this->DBobject->insertIntoAnyTable('courseallocationtbl', $details);
      }
      public function allocationCourseOnDateOnly($course, $date)
      {
            $slot =  $this->getRandomSlotByDate($date);
           
            $query = $this->DBobject->getTotalDeptOfferedCourse($course);
           $totalDept = $query->num_rows();
            $details = array('dayDate'=> $date, 'slot'=>$slot,'course'=>$course,'totalDept'=>$totalDept);
            $this->DBobject->insertIntoAnyTable('courseallocationtbl', $details);
      }
      public function allocateCourseOnDateAndSlot($course,$date,$slot)
      {
            $query = $this->DBobject->getTotalDeptOfferedCourse($course);
            $totalDept = $query->num_rows();
            $details = array('dayDate'=> $date, 'slot'=>$slot,'course'=>$course,'totalDept'=>$totalDept);
            $this->DBobject->insertIntoAnyTable('courseallocationtbl', $details);
      }
     public function allocateGroupCourse(array $ArrOfcourse, array $dateArr)
     {
           $batchOfAssignedCourses = array();
           $batchOfUnassignedCourses = array();
           $slotArr = array();
           foreach($dateArr as $date)
              {
                $slotArr[] = $this->DBobject->getSlotByDate($date);
                //$this->getRandomSlotByDate($date);
              }
              $Obj = new  AllocateGroupCourses( $ArrOfcourse,$dateArr, $slotArr ); 
               $Linear = $Obj->getLinearList() ;
               $Obj->IPOgatherCourseInfomation();
               $Obj->sortCourseInfo();
               $Obj->MasterJudgeAllocator();
              $Virtual = $Obj->getVirtualTable();
              
               foreach ($Virtual as $value)
                {
                     if(  $value->AllocationStatus == true )
                     {
                         $deptString = $this->formatDepartmentList($value->DeptLevelObj);
                         $details = array('dayDate'=> $value->Date, 'slot'=>$value->Slot,'course'=>$value->Course,'totalDept'=> $value->TotalOfferedDept,'listOfDept'=>$deptString);
                         $batchOfAssignedCourses [] = $details;
                    }
                    elseif( $value->AllocationStatus == false)
                    {     
                          $deptString = $this->formatDepartmentList($value->DeptLevelObj);

                         $details = array('course'=>$value->Course,'totalDept'=> $value->TotalOfferedDept,'listOfDept'=>$deptString);
                         $batchOfUnassignedCourses[] = $details;
                    }
               }
              
           $this->DBobject->clearTableContent('unAllocatedCourseTbl') ;
           $this->DBobject->clearTableContent('courseallocationtbl') ;
         $this->DBobject->insertBatchArrayIntoAnyTable('courseallocationtbl' , $batchOfAssignedCourses);
         if( count( $batchOfUnassignedCourses) > 0)  // ensuring value exist before insertion
         { 
            $this->DBobject->clearTableContent('unAllocatedCourseTbl') ;
            $this->DBobject->insertBatchArrayIntoAnyTable('unAllocatedCourseTbl' , $batchOfUnassignedCourses);
         }
     }
    public function formatDepartmentList($deptLevelObj)
     {
          $stringBuilder = "";
          foreach ($deptLevelObj as $Obj) 
          {
                $stringBuilder .= $Obj->Department.',' ;
          }
          return $stringBuilder;
     }
     public function getRandomDateBySlot($slot)
     {
        $dateList = $this->DBobject->getDateListBySlot($slot); 
      //   echo count($dateList)."date<br/>";
         $date =   $this->randomise($dateList);  // from logic class

        return $date ;
     }
     public function getRandomSlotByDate($date)
     {
          $slot = $this->DBobject->getSlotByDate($date);
    
          $slotList = array();
          for($i = 1; $i<= $slot; $i++)
          {
              $slotList[] = $i;
          }
         $randomSlot = $this->randomise($slotList);  // from logic class 
         return $randomSlot;
     }

 } 
   class DeptLevel
   {
       public $Department;
       public $Level;
   }
   class DateSlot
   {
        public $Date;
        public $NumberOfSlot = array();  
   }
   class LinearDateSlot
   {
        public $Date;
        public $Slot;  
   }
   class VirtualCourseAllocationTable
   {
         public $DeptLevelObj = array() ;
         public $Course;
         public $TotalOfferedDept;
         public $Slot;
         public $Date;
         public $AllocationStatus = false;
         
   }
   class AllocateGroupCourses extends logic
   {
        public $Course = array();
        public $DateSlotObj  = array();
        public $VirtualTable = array();
        public $LinearList = array();
       public function __construct(array $Course, array $Date, array $Slot)
       {
           $this->Course = $Course;
           $max_slot = max($Slot) ;
           for($i=0 ; $i< count($Date) ; $i++)
           {
                $dateSlot       = new DateSlot();
                $dateSlot->Date = $Date[$i];
                for( $j= 1; $j<= $Slot[$i] ;  $j++)  // generating slot range
                 {
                     $dateSlot->NumberOfSlot[] = $j ; 
                 }
              
               $this->DateSlotObj[] =  $dateSlot; 
           }

           
           $this->DBobject = new mconfigs();
           $this->levelDistance = $this->DBobject->getAnyColumnByID('leveldistance','distance','ID', 1);
           $this->levelPerSlot = $this->DBobject->getAnyColumnByID('leveldistance','numOfLevel','ID', 1);

           $maxSlot = $this->DBobject->getMaxSlot();
           $count = 0;
           for($k = 1; $k<= $max_slot; $k++ )   // creating 2x2 linear array for date slot eg 12-05-2014: 1 , 13-04-2014:2
           {
                 foreach ($this->DateSlotObj as $ds) 
                  {
               //    echo count($ds->Slot);
                     if(count($ds->NumberOfSlot ) >= $k && $ds->NumberOfSlot[$k-1] == $k)
                     {
                         $newDateSlot  = new LinearDateSlot();
                         $newDateSlot->Date = $ds->Date;
                         $newDateSlot->Slot = $ds->NumberOfSlot[$k-1];
                         $this->LinearList[] = $newDateSlot;
                        
                     }
                 }
                 $count++;
               
           }
       }
       public function getLinearList()
       {
          return $this->LinearList;
       }
       public function sortCourseInfo()
       {
            // $tempObj = $this->VirtualTable[0]
                for( $j= 0; $j< count($this->VirtualTable); $j++ )
                 {
                      for($k= 0; $k < count($this->VirtualTable)-1; $k++)
                      {
                           if( $this->VirtualTable[$k]->TotalOfferedDept < $this->VirtualTable[$k+1]->TotalOfferedDept ) 
                           {
                              $tempObj = $this->VirtualTable[$k];
                              $this->VirtualTable[$k] = $this->VirtualTable[$k+1];
                              $this->VirtualTable[$k+1] = $tempObj;
                           }
                      }
                 } 
       }
       public function IPOgatherCourseInfomation()
       {
            $OBJECT1 = array();
            $OBJECT2 = array();
             $query = $this->DBobject->getAnyTableContent('courseallocationtbl');
             if($query->num_rows() > 0)
             {
                 foreach ($query->result() as $row)
                  {
                      $TableObj        = new VirtualCourseAllocationTable();   // storing allocated courses
                      $TableObj->Slot  = $row->slot;
                      $TableObj->Date  = $row->dayDate;
                      $queryObj        =  $this->DBobject->getTotalDeptOfferedCourse($row->course);// directly from model
                     
                      foreach ($queryObj->result() as $rows)
                        {
                             $deptLevel = new DeptLevel();
                             $deptLevel->Department    = $rows->dept;
                             $deptLevel->Level         = $rows->level;
                             $TableObj->DeptLevelObj[] = $deptLevel;
                        }
                      $TableObj->Course           = $row->course;
                      $TableObj->TotalOfferedDept = $row->totalDept; 
                      $TableObj->AllocationStatus =  true;
                      $OBJECT1[]                  = $TableObj;
                  }
             }
             
             foreach ($this->Course as $course)
              {
                $TableObj2 = new VirtualCourseAllocationTable();   // storing course yet to be allocated
                 
                 $queryObj2          =  $this->DBobject->getTotalDeptOfferedCourse($course);  // directly from Model
                if($queryObj2->num_rows() > 0)
                  {
                     $TableObj2->Slot  = "";
                     $TableObj2->Date  = "";
                     foreach ($queryObj2->result() as $rows)
                        {
                           $deptLevel2 = new DeptLevel();
                           $deptLevel2->Department    = $rows->dept;
                           $deptLevel2->Level         = $rows->level;
                           $TableObj2->DeptLevelObj[] = $deptLevel2;
                        }
                 $TableObj2->Course = $course; 
                 $TableObj2->TotalOfferedDept =  $queryObj2->num_rows(); 
                 $TableObj2->AllocationStatus = false; 
                 $OBJECT2[]                   =  $TableObj2; 
               }
              }

            $this->VirtualTable = array_merge($OBJECT2, $OBJECT1);

       }
       public function getVirtualTable()
       {
          return $this->VirtualTable;
       }
       public function pickAfreeCourseSuspect()
       {

             foreach ($this->VirtualTable as $Vtable )
              {
                   if($Vtable->AllocationStatus == false)
                   {
                      return $Vtable;
                   }      
              }
              return  null;    
       }
       public function ConfirmFreeDateSlot( $date_slot_row) // comfirm if a slot is free for a particular day
        {
          
           foreach ($this->VirtualTable as $Vtable )
              {
                  if( $Vtable->Date == $date_slot_row->Date && $Vtable->Slot== $date_slot_row->Slot )
                    { return false; }
              }
              return true ;
        }

        public function appealCourt()
        {
               
        }
       public function  MasterJudgeAllocator()
        {
          $List = array();  $i=0;
          $time = strtotime('2014-02-28');
          $date =  date('Y-m-d',$time) ;
           for($k = 1; $k<=30 ; $k++)
           {
              foreach ($this->LinearList as $date_slot_row)
               {
                 
                  $VtableObj = $this->pickAfreeCourseSuspect(); 
                 if( $this->ConfirmFreeDateSlot( $date_slot_row) )  // allocate free slot in a particlar date(day)
                  {     
                      
                      //   $VtableObj->Course;
                           $VtableObj->Date = $date_slot_row->Date;
                          $VtableObj->Slot = $date_slot_row->Slot;
                              $VtableObj->AllocationStatus = true;
                      $i++;
                  
                  }
                  else
                  {
                       if( $VtableObj != null )
                       {
                           if( $this->PoliceInvestigate($VtableObj, $date_slot_row ) ) 
                            {
                              // $VtableObj->Course;
                                  $VtableObj->Date = $date_slot_row->Date;
                                  $VtableObj->Slot = $date_slot_row->Slot;
                                 $VtableObj->AllocationStatus = true;  
                                  $i++;  
                            }
                       }
                  }   
             }
          }
            
        }

       public function PoliceInvestigate($vtableObj , $date_slot ) // ensure if all constraint are met
       {
            if($this->InvestigateLevelDistance($vtableObj, $date_slot) && $this->InvestigateLevelPerSlot($vtableObj, $date_slot) &&
             $this->InvestigateExistenceOfLevelInFoundSlot($vtableObj,$date_slot) && $this->InvestigateMaxNumberOfALevelPerDay($vtableObj, $date_slot,1) )
                {
                   return true;
                }
              else
              {
                 return false;
              }
       }
       private function Equals($vtObject, $ItemDeptLvl)    // callee method to InvestigateLevelDistance
       {   
               foreach ($vtObject->DeptLevelObj as $ObjRow) 
                 {
                     if($ObjRow->Department == $ItemDeptLvl->Department )
                       {
                           $levelDistance =  (int)$ObjRow->Level - (int)$ItemDeptLvl->Level;
                           return  abs($levelDistance);
                       }
                 } 
           
           return 5;
       }
       public function InvestigateLevelDistance($vtObject, $date_slot)  // caller method no Equals
       {          
                   $arrayOfDeptLevelObj = array(); 
                   foreach ($this->VirtualTable as  $VTrowObj) 
                   {
                        
                      if($VTrowObj->Date == $date_slot->Date && $VTrowObj->Slot == $date_slot->Slot)
                        {    

                             foreach ($VTrowObj->DeptLevelObj as $Item)
                               {
                                   $distance = $this->Equals($vtObject,$Item) ;
                                   if( $distance < $this->levelDistance && $distance != 5)
                                         return false;
                               }
                        }
                      
                   } 

                   return true;    
       }

       private function DepartmentCounter($vtObject,$allOfferedDeptList )
       {
          foreach ($vtObject->DeptLevelObj as $ObjRow) 
           {
                 
             $found =  $this->countValue($ObjRow->Department, $allOfferedDeptList );        
               if($found > $this->levelPerSlot)
                {  return  false; }
          
           } 
           return true;
       }
       public function InvestigateLevelPerSlot($vtObject, $date_slot)
       {
            $allOfferedDeptList = array();
               foreach($this->VirtualTable as  $VTrowObj )
               {
                   if($VTrowObj->Date == $date_slot->Date && $VTrowObj->Slot == $date_slot->Slot)
                       {
                          foreach($VTrowObj->DeptLevelObj as $Item)
                               {
                                     $allOfferedDeptList[] = $Item->Department;                               
                               }

                       }

               }
              if( $this->DepartmentCounter($vtObject, $allOfferedDeptList) )
                  return true;  
              else
                   return false;
       }
        private function CheckLevelInSlot($vtObject,$Item)
        {
               foreach ($vtObject->DeptLevelObj as $ObjRow) 
                {
                    if($ObjRow->Department == $Item->Department && $ObjRow->Level == $Item->Level )
                    {
                       return false;
                    }
                }
                return true;
        }
       public function InvestigateExistenceOfLevelInFoundSlot($vtObject,$date_slot)  // rank 1
       {
              foreach($this->VirtualTable as  $VTrowObj )
               {
                   if($VTrowObj->Date == $date_slot->Date && $VTrowObj->Slot == $date_slot->Slot)
                       {
                           foreach($VTrowObj->DeptLevelObj as $Item)
                               {
                                  if(!$this->CheckLevelInSlot($vtObject, $Item) )
                                    {  return false;  }
                               }        
                       }
               }  

               return true;    
       }

       public function InvestigateMaxNumberOfALevelPerDay($vtObject, $date_slot, $MaxLevelPerDay)
       {
              $arrayObj = array();
              $numberFound = 0;
             foreach($this->VirtualTable as  $VTrowObj )
               {
                   if($VTrowObj->Date == $date_slot->Date)
                       {
                          foreach($VTrowObj->DeptLevelObj as $Item)
                               {
                                  $arrayObj[] = $Item;     
                               }
                       }
              }  
             foreach ($vtObject->DeptLevelObj as $singleDeptLvl) 
             {
                  foreach ($arrayObj as $DeptLvl) 
                   {
                        if($singleDeptLvl->Department == $DeptLvl->Department && $singleDeptLvl->Level == $DeptLvl->Level)
                        {
                            $numberFound ++;
                        }
                    } 
                   if($numberFound > $MaxLevelPerDay){  return false; }

             }
             return true;
       }

  /*   public function InvestigateOtherSuspectsInvolvedInDatDate($date, $level , $course, $dept)// rank 2 // this entails depts and their levels offering that particular course 
       {

       }   */
       public function getCoursesAllocatedInASlot($date, $slot)
       {

       }
       function getDepartment($courseCode)
       {
          return  ($courseCode[0].$courseCode[1].$courseCode[2]);

       }
     
      function getLevel($courseCode)
      {
         if($courseCode[3] =="")
          return "000" ;
        else
        return  $courseCode[3]."00";   

      }
     function getCourseSemester($courseCode)
     {
         if($courseCode[5] !="")
         {
            if( ((int)$courseCode[5] % 2) == 0)
             {
               $semester = "SECOND";
             }
            else
             {
                $semester = "FIRST";
             }
        }
        else
        {
          $semester = "NONE";
        }
          return $semester;

     }
   } 

 
   class AssignVenueToDepartment extends logic 
   {
       public $DeptTableObject = array();
       public $VenueTableObject = array();
       private $DBobject;
       public $AlloVenDeptObjTable = array();
       private $counter;
       public $CapacityOfFreeDept;
        public function __construct( array $DeptTableObject, array $VenueTableObject)
        {
           $this->DeptTableObject  =  $DeptTableObject;
           $this->VenueTableObject = $VenueTableObject; 
           $this->DBobject         = new mconfigs();
        //   $this->AlloVenDeptObjTable  = $AlloVenDeptObjTable; 
           $this->counter = 0; 
        }

        public function extractVenueOwnedByDept()
        {
            
            
            $tempDeptIDarray = array();
            $i = 0;
            $tempDeptArrayObject = array();
            foreach ($this->VenueTableObject as $venueTableRow ) 
            {
                     if($venueTableRow->OwnerType =="D")
                     {
                         $alloVenDeptObj = new AllocatedVenueDepatmentList();
                         $venueTableRow->Status = true;
                         $alloVenDeptObj->Department  = $this->DBobject->getAnyColumnByID('department','deptCode','deptID', $venueTableRow->OwnerID) ;
                         $alloVenDeptObj->Venue = $venueTableRow->Name;
                         $venueTableRow->Capacity  -= $this->DBobject->getAnyColumnByID('department','maxStudent','deptID', $venueTableRow->OwnerID) ;
                         $alloVenDeptObj->Capacity = $venueTableRow->Capacity;
                         $alloVenDeptObj->ID = $this->counter + 1 ;
                         $this->AlloVenDeptObjTable[$this->counter] = $alloVenDeptObj;
                         $tempDeptIDarray[$i] = $venueTableRow->OwnerID;
                         $this->counter++;
                         $i++;

                     
                     }
                     
            }

            foreach ($this->DeptTableObject as $deptTableRow) 
            {
                 if($this->contains($deptTableRow->DeptID, $tempDeptIDarray)) 
                 {
                     $deptTableRow->Status = true; 
                 } 
            }

         //   $this->VenueTableObject = array_merge(array_diff($this->VenueTableObject, $tempDeptArrayObject );
        }
        public function getLength( )
        {
           return  count($this->AlloVenDeptObjTable) ;
        }
        public function QueryDeptTableObject($schoolID, $venueCapacity)
        {   
          
            $dept = "";
              foreach ($this->DeptTableObject as $deptTableRow)
               {   

                   if((int)$deptTableRow->SchoolID == $schoolID && $deptTableRow->Status == false  )
                      {
                          $spaceDifference =  (int)$venueCapacity -  (int)$deptTableRow->MaxStudent;
                           if( $spaceDifference < 15 ) // checking for best
                           {
                              $deptTableRow->Status = true;
                              $dept = $deptTableRow->DeptCode ;
                              $this->CapacityOfFreeDept = $deptTableRow->MaxStudent;
                              break;
                           }
                      }
               }
             return $dept;  
        }
        public function extractVenueOwnedBySchool()
        {
              foreach ($this->VenueTableObject as $venueTableRow  ) 
                {
                     if($venueTableRow->OwnerType =="S" && $venueTableRow->Status == false )
                     {                
                         $dept = $this->QueryDeptTableObject($venueTableRow->OwnerID, $venueTableRow->Capacity) ;
                         if($dept !="")
                         {
                            $alloVenDeptObj = new AllocatedVenueDepatmentList();
                            $venueTableRow->Status = true;
                            $alloVenDeptObj->Department = $dept;
                           $alloVenDeptObj->Venue = $venueTableRow->Name;
                            $venueTableRow->Capacity -= $this->CapacityOfFreeDept;
                            $alloVenDeptObj->Capacity = $venueTableRow->Capacity;
                            $alloVenDeptObj->ID = $this->counter + 1 ;
                            $this->AlloVenDeptObjTable[$this->counter] = $alloVenDeptObj;
                            $this->counter++;
                         }
                     }
                     
                     
                }
        }

       public function extractGeneralVenue()
        {

          foreach ($this->VenueTableObject as $venueTableRow  ) 
                {
                     if($venueTableRow->OwnerType =="G" && $venueTableRow->Status == false)
                     {   
                         
                         
                         $dept = $this->getFreeDepartment($venueTableRow->Capacity);
                         if($dept !="")
                           {
                              $alloVenDeptObj = new AllocatedVenueDepatmentList();
                             // 
                              $venueTableRow->Capacity -= $this->CapacityOfFreeDept;
                              if((int) $venueTableRow->Capacity < 100)
                              {
                                 $venueTableRow->Status = true;
                                 
                              }
                              $alloVenDeptObj->Capacity = $venueTableRow->Capacity;
                              $alloVenDeptObj->Department = $dept ;
                              $alloVenDeptObj->Venue = $venueTableRow->Name;
                              $alloVenDeptObj->ID = $this->counter + 1 ;
                              $this->AlloVenDeptObjTable[$this->counter] = $alloVenDeptObj;

                              $this->counter++;
                          }
                     }
                     
                     
                }
        }
        public function AssignLeftOverDeptToVenue()
        {
               $totalDept =  $this->getTotalUnassignedDept($this->DeptTableObject);
               $totalVenue= $this->getTotalUnassignedVenue($this->VenueTableObject);
                $k = 1;
               while((int)$totalDept > 0 && (int)$totalVenue > 0 )
                { 
                   // echo $k++;
                  foreach($this->VenueTableObject as $venueTableRow) 
                   {
                     if( $venueTableRow->Status == false)
                      { 
                         $dept = $this->getFreeDepartment($venueTableRow->Capacity);
                         if($dept !="")
                         {
                         //  if($venueTableRow->Capacity >= $this->CapacityOfFreeDept)
                        //   {
                              $alloVenDeptObj = new AllocatedVenueDepatmentList();
                              
                              $venueTableRow->Capacity -= $this->CapacityOfFreeDept; 
                              $alloVenDeptObj->Capacity = $venueTableRow->Capacity;
                              $alloVenDeptObj->Department = $dept ;
                              $alloVenDeptObj->Venue = $venueTableRow->Name;
                              $alloVenDeptObj->ID = $this->counter + 1 ;
                            //  echo $venueTableRow->Name." false<br/>";
                              if((int)$venueTableRow->Capacity < 80)
                               { 
                                  $venueTableRow->Status = true;
                            //    echo $venueTableRow->Name."<br/>";
                                                            
                               }
                                
                                 $this->AlloVenDeptObjTable[$this->counter] = $alloVenDeptObj;

                                 $this->counter++;

                         //  }
                           }
                        }

                     }
              //    if( $this->trackIfVenueCapacityIsLessThanThreshold())
              //      {  break;  }

             $totalDept =  $this->getTotalUnassignedDept($this->DeptTableObject);
              
            $totalVenue= $this->getTotalUnassignedVenue($this->VenueTableObject);
              }
             
        }
         function trackIfVenueCapacityIsLessThanThreshold()
         {
             $totalLeftVenue = 0;   
             foreach ( $this->VenueTableObject as $venueTableRow)
             {
                 if($venueTableRow->Status == false && (int)$venueTableRow->Capacity < 90  )
                   {  $totalLeftVenue++ ;}
             }
            
             if((int) $totalLeftVenue == count($this->VenueTableObject ) )
                return true ;
              else
                return false;
         }

         public function getTotalUnassignedDept(array $DeptTableObject)
         {
            $totalLeftDept = 0;   
           foreach ( $DeptTableObject as $freeDept)
            {
                 if($freeDept->Status == false )
                   { $totalLeftDept++ ;}
            }
            return $totalLeftDept;
         }

         public function getTotalUnassignedVenue(array $VenueTableObject)
         {
            $totalLeftVenue = 0;   
           foreach ( $VenueTableObject as $venueTableRow)
            {
                 if($venueTableRow->Status == false )
                   { //echo $venueTableRow->Name;
                    $totalLeftVenue++ ;}
            }
            
            return $totalLeftVenue;
         }

         public function getFreeDepartment( $venueCapacity)
         { 
            $dept ="";
           foreach ( $this->DeptTableObject as $freeDept)
            {
                 if($freeDept->Status == false && $freeDept->MaxStudent <= $venueCapacity )
                 {
                    $dept =  $freeDept->DeptCode;
                    $freeDept->Status = true;
                    $this->CapacityOfFreeDept = $freeDept->MaxStudent;
                     break; 
                 }
            } 
             return $dept ;
         }

         
         public function getLeftVenueList( array $VenueTableObject)
         {
            $left = array();
            $k =0;
           foreach ( $VenueTableObject as $freeVenue)
            {
                 
                 if($freeVenue->Status == false )
                 { 
                   $vp = new VenueParameter() ;
                   $vp->Name      = $freeVenue->Name;
                   $vp->OwnerType = $freeVenue->OwnerType;
                   $pv->VenueID   = $freeVenue->VenueID;
                   $left[$k] = $vp;  
                   $k++;  
                 }
            }
            return $left;
         }
         public function getLeftDepartmentList(array $DeptTableObject)
         {
           $left = array() ;
           $k = 0;
           foreach ( $DeptTableObject as $freeDept)
            {
                   
                 if($freeDept->Status == false )
                 {
                    $dp = new DeptmentParameter() ;
                   $dp->DeptCode   = $freeDept->DeptCode;
                   $dp->SchoolCode = $freeDept->SchoolCode; 
                   $dp->DeptID     = $freeDept->DeptID; 
                   $left[$k] = $dp; 
                   $k++; 
                 }
            }
           return $left;
         }
         public function getAllocationObject()
         {
           return  $this->AlloVenDeptObjTable;
         }

        

   }
     

   class SetAllocation
   {
      private $Db ;
    
     public function __construct( )
     {
         $this->Db = new mconfigs();
        
     }
     
   
     function getAllDepartmentDetails()
     {
         
        $deptDetails =  $this->Db->getAnyTableContent('department'); 
        $ArrayOfDept = array();
        $ArrayOfDeptObject = array();
        if($deptDetails->num_rows() > 0)
           {
            
          //  $ObjectOfDept->SchoolID = 2;
             $i = 0;
             foreach ($deptDetails->result() as $row) 
             {
                $ObjectOfDept = new DeptmentParameter();
                $ObjectOfDept->DeptID   = $row->deptID;
                $ObjectOfDept->SchoolID = $row->schoolID;
                $ObjectOfDept->SchoolCode = $this->Db->getAnyColumnByID('school','schoolCode','schoolID', $row->schoolID);
                $ObjectOfDept->DeptCode = $row->deptCode;
                $ObjectOfDept->DeptName = $row->deptName;
                $ObjectOfDept->MaxStudent = $row->maxStudent;
                $ObjectOfDept->Status = false;

                $ArrayOfDeptObject[$i] =  $ObjectOfDept;
                $i++;
             }
           }
       return $ArrayOfDeptObject;
     }

     function getAllVenueDetails()
     {
          
          $venueDetails =  $this->Db->getAnyTableContent('venue');  
          $ArrayOfVenueObject = array(); 
           if($venueDetails->num_rows() > 0)
            {
                $i = 0;
                 foreach ($venueDetails->result() as $row) 
                  {
                     $ObjectOfVenue = new VenueParameter();
                     $ObjectOfVenue->VenueID = $row->venueid;
                     $ObjectOfVenue->Name    = $row->name;
                     $ObjectOfVenue->Capacity= $row->capacity;
                     $ownedBy = explode(',', $row->ownedBy) ;
                     $ObjectOfVenue->OwnerID = $ownedBy[0];
                     $ObjectOfVenue->OwnerType = $ownedBy[1];
                     $ObjectOfVenue->Status  = false;

                     $ArrayOfVenueObject[$i] = $ObjectOfVenue;
                     $i++;
                  }
     
           }
       
      return $ArrayOfVenueObject ;
     }



   }
     class PoliceAnyInsertion 
     {
         public $ColumnNames = array();
         public $tableName;
        public function __construct($tableName,array $ColumnNames)
          {
           //   $this->Db = new mconfigs();
           //   $this->table = 
         }

     }
   class TimeCheck
   {
         function TimeCheck($sTime, $eTime )
         {
             $this->startTime = $sTime;
             $this->endTime = $eTime;

         }
        function splitTime()
        {
           $stymeArr = explode(':', $this->startTime);
           $this->Shour = $stymeArr[0];
           $this->Speriod = $stymeArr[1];
           
           $etymeArr = explode(':', $this->endTime);
           $this->Ehour = $etymeArr[0];
           $this->Eperiod = $etymeArr[1] ;

        }
       

       function confirmCorrectTimeFrame()
       {
          if(( $this->Shour < $this->Ehour) && ($this->Speriod =="00AM" && $this->Eperiod=="00AM") )  // morning
               return true;
            elseif($this->Speriod =="00AM" && $this->Eperiod=="00PM" ) // morning to evening or afternoon
              return true;
           elseif(( $this->Shour < $this->Ehour) && ($this->Speriod =="00PM" && $this->Eperiod=="00PM")&& ($this->Ehour !="12") )  //  afternoon to evening or afternoon
              return true;
          elseif( $this->Shour =="12" &&  $this->Eperiod=="00PM" && $this->Ehour != 12 )   //  afternoon to evening or afternoon
              return true;
 
            else
              return false;
         
       }
     function getSTH()
         {
             return $this->convertHourToInt($this->Shour);
         }
    function getStP()
        {
            return $this->Speriod ;
        }
     function getETH()
         {
             return $this->convertHourToInt($this->Ehour);
         }
     function getEtP()
        {
             return  $this->Eperiod  ;
        }
        


       function generateLectTymeList($case)
       {
              $S=  $this->convertHourToInt($this->Shour);
              $E=  $this->convertHourToInt($this->Ehour);
             if($case=="E")   // E implies endtime list 
              { 
                $S = $S + 1 ;
                $E = $E + 1;
              }
              elseif($case =="H") // H implies time header list
               {
                $E = $E + 1 ;
               }
               else{
                  //  $E = $E - 1;
               }
                 
             
           $TymeArr = array();
            if($this->Speriod =="00AM" && $this->Eperiod =="00AM" )// am to am 
            { 
               $count =0;
               for($i = $S; $i<$E ; $i++ )
               {
                   $TymeArr[$count++] = $i.":00AM" ;
               }
            }
            elseif ($this->Speriod =="00AM" && ($this->Eperiod =="00PM" || $this->Ehour=="12")  ) // am to pm 
            {
                $count =0;
                 if( $S >= $E )
                  { 
                      for($i = $S; $i<=12 ; $i++ )
                      {
                          if($i == 12 )
                           {  
                              $TymeArr[$count++] = $i.":00PM" ;
                           
                          }
                          else
                          {
                            $TymeArr[$count++] = $i.":00AM" ;
                          }
                      }
                     
                      for($i = 1; $i< $E ; $i++ )
                        {
                          $TymeArr[$count++] = $i.":00PM" ;
                        }

               
                 }
                 else
                 {
                     for($i = $S; $i<=12 ; $i++ )
                      {
                         if($i == 12 )
                           {  
                              $TymeArr[$count++] = $i.":00PM" ;
                           
                          }
                        else
                        {
                            $TymeArr[$count++] = $i.":00AM" ;
                        }
                     }
                 }

               
          }
          
       elseif ($this->Speriod =="00PM" && $this->Eperiod =="00PM")  // pm to pm
         { 
              $count = 0;
              if($S == 12)
              {
                $TymeArr[$count++] = "12:00PM" ;
                for($i = 1; $i<$E ; $i++ )
                {
                   $TymeArr[$count++] = $i.":00PM" ;
                }
              }
              else
              {
                  for($i = $S; $i<$E ; $i++ )
                  {
                     $TymeArr[$count++] = $i.":00PM" ;
                  }
              }
                
        }  
                  
       return $TymeArr;
   }
       function convertHourToInt($hour)
       {
            switch($hour)
            {
              case '1':
                $intHr = 1;
                break;
              case '2':
                $intHr = 2;
                break;
              case '3':
                $intHr = 3;
                break;
              case '4':
                $intHr = 4;
                break;
              case '5':
                $intHr = 5;
                break;
              case '6':
                $intHr = 6;
                break;
              case '7':
                $intHr = 7;
                break;
              case '8':
                $intHr = 8;
                break;
              case '9':
                $intHr = 9;
                break;
              case '10':
                $intHr = 10;
                break;
              case '11':
                $intHr = 11;
                break;
              case '12':
                $intHr = 12;
                break;
              default:
              break;


            }
            return $intHr ; 

       }
     
   }
   class SingleCourseDetails extends ExtractDepartmentalDetails
   {
      

   }

 class ExtractDepartmentalDetails 
  {
      
   /*   function ExtractDepartmentalDetails($courseArr)
      {
         $this->CourseArr = $courseArr ;  
   
      }
*/
       function char_a_courseCode($courseCode)
       {
          return  ($courseCode[0].$courseCode[1].$courseCode[2]);

       }
     
      function getLevel($courseCode)
      {
         if($courseCode[3] =="")
          return "000" ;
        else
        return  $courseCode[3]."00";   

      }
     function getCourseSemester($courseCode)
     {
         if($courseCode[5] !="")
         {
            if( ((int)$courseCode[5] % 2) == 0)
        	   {
        	     $semester = "SECOND";
             }
            else
        	   {
        		    $semester = "FIRST";
        	   }
        }
        else
        {
          $semester = "NONE";
        }
        	return $semester;

     }
      function getDepartmentList($CourseArr)
      {

      	 $this->courseCodeList = array("GNRL");
         for($i= 0 ;$i < count($CourseArr); $i++ )
         {
            $dept = $this->char_a_courseCode($CourseArr[$i]) ;
            $found = $this->siever($dept , $this->courseCodeList );
            if($found == 0)
            {
              $this->courseCodeList[]	= $dept; 
            }


         } 
         return $this->courseCodeList;
      }
     function siever($courseCode, $courseCodeList)
     {
          for($i = 0; $i < count($courseCodeList); $i++ )
          {
          	if($courseCodeList[$i] == $courseCode)
          	{
          		return 1;
          	}
          }  
           return 0 ;
     }
     

  }


?>