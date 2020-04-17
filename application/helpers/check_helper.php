<?php
/**
 * Date Check Helper
 * @author Mehdi Jalal <mehdi.jalal@live.com><mehdi@netlinks.af>
 * @version 0.01
 * @date    03/June/2013
 */

/**
 * Convert it from Jalali to gre and vs
 */

if(!function_exists('change_datei'))
{
    function change_datei($date)
    {
        $CI =& get_instance();
        if($CI->m_auth->get_language()=='en')
        {
            
            $x_date = explode("/", $date);
         
            $new_job_date = datecheck($x_date[2],$x_date[0],$x_date[1],$CI->m_auth->get_language());
    
        }
        else 
        {
          
            $x_date = explode("/", $date);
            $new_job_date = datecheck($x_date[2],$x_date[1],$x_date[0],$CI->m_auth->get_language());
            
        }
        return $new_job_date;
    }
}
/**
 * Convert it from Jalali to gre and vs
 */

if(!function_exists('change_date_d'))
{
    function change_date_d($date)
    {
        $CI =& get_instance();
        if($CI->m_auth->get_language()=='dr')
        {
            $x_date = explode("-", $date);
            
            $new_job_date = datecheck($x_date[2],$x_date[1],$x_date[0],$CI->m_auth->get_language());
        }
        else 
        {
            $x_date = explode("-", $date);
            
            $new_job_date = datecheck($x_date[2],$x_date[0],$x_date[1],$CI->m_auth->get_language());
            
        }
        return $new_job_date;
    }
}

/**
 * Converting dates
 */
if(!function_exists('change_dateu'))
{
    function change_dateu($date)
    {
        $CI =& get_instance();
        if($CI->m_auth->get_language()=='dr')
        {
            $x_date = explode("-", $date);
          
            $new_job_date = datecheck($x_date[2],$x_date[1],$x_date[0],$CI->m_auth->get_language());
        }
        else 
        {
            $x_date = explode("-", $date);
          
            $new_job_date = datecheck($x_date[2],$x_date[0],$x_date[1],$CI->m_auth->get_language());
            
        }
        return $new_job_date;
    }
}
/**
 * Check dates
 */
if(!function_exists('datecheck'))
{
	function datecheck($year,$month,$day,$lang)
	{
		$CI =& get_instance();
		$CI->load->library('dateconverter'); // load library
		
		if($day=='00')
		 {
			$day = '01';
		 }
		 else
		 {
		   $day = $day;  
		 } 
		 
		 //If language is Dari or Pashto then date is converted to gregorian format firstly 
		 if($lang=='dr' || $lang=='pa')
		 {
			 if($month!='00' && $year!='0000' && intval($month)!=0 && intval($year)!=0 && $month!='' && $year!='')
			 {
				 $theDate = array
				 (
					'date_day'        => $day,
					'date_month'      => $month,
					'date_year'       => $year
				 );
				 $compDate = $CI->dateconverter->ToGregorian($theDate['date_year'],$theDate['date_month'],$theDate['date_day']);
			 }
			 else
			 {
				 if(($month=='00' OR $year=='0000') OR ($month=='' OR $year==''))
				 {
					 $theDate = array
					 (
						'date_day'        => '00',
						'date_month'      => '00',
						'date_year'       => '0000' 
					);
					$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
				 }
			 }
		 }
		 
		 if($lang=='en')
		 {
			 if($month=='00' && $year=='0000')
			 {
				 $theDate = array
				 (
					'date_day'        => '00',
					'date_month'      => '00',
					'date_year'       => '0000', 
				);
				$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
			 }
			 else if($month=='00' OR $year=='0000' OR ($month=='' OR $year==''))
			 {
					 $theDate = array
					 (
						'date_day'        => '00',
						'date_month'      => '00',
						'date_year'       => '0000' 
					);
					$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
			 }
			 else
			 {
				 $theDate = array
				 (
					'date_day'        => $day,
					'date_month'      => $month,
					'date_year'       => $year
				 );
				 
				 $compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
			 }
		 }
		 return $compDate;
	}
}

if(!function_exists('dateconvert'))
{
	function dateconvert($thedate,$lang)
	{
		if($lang=='en')
		{
			return $thedate;
		}
		else
		{
			$thedate = explode("-",$thedate); 
			$year    = $thedate[0];
			$month   = $thedate[1];
			$day     = $thedate[2];
		}
		
		$CI =& get_instance();
		$CI->load->library('dateconverter'); // load library
		
		if($day=='00')
		 {
			$day = '01';
		 }
		 else
		 {
		   $day = $day;  
		 } 
		 
		 if($lang=='dr' || $lang=='pa')
		 {
			 if($month=='00' && $year=='0000')
			 {
				 $theDate = array
				 (
					'date_day'        => '00',
					'date_month'      => '00',
					'date_year'       => '0000' 
				);
				$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
			 }
			 else
			 {
				 $theDate = array
				 (
					'date_day'        => $day,
					'date_month'      => $month,
					'date_year'       => $year
				 );
				 
				 $compDate = $CI->dateconverter->ToShamsi($theDate['date_year'],$theDate['date_month'],$theDate['date_day']);
			 }
		 }
		 return $compDate;
	}
}

//Convert persian date to shamsi short version
if(!function_exists('sdateconvert'))
{
	function sdateconvert($thedate,$lang)
	{
		if($lang='en')
		{
			$thedate = explode("-",$thedate);
			$year    = $thedate[0];
			$month   = $thedate[1];
			$day     = $thedate[2];
		}
		else
		{
			$thedate = explode("-",$thedate); 
			$year    = $thedate[2];
			$month   = $thedate[1];
			$day     = $thedate[0];
		}
		
		$CI =& get_instance();
		$CI->load->library('dateconverter'); // load library
		
		if($day=='00')
		 {
			$day = '01';
		 }
		 else
		 {
		   $day = $day;  
		 } 
		 
		 //If language is Dari or Pashto then date is converted to gregorian format firstly 
		 if($lang=='dr' || $lang=='pa')
		 {
			 if($month!='00' && $year!='0000')
			 {
				 $theDate = array
				 (
					'date_day'        => $day,
					'date_month'      => $month,
					'date_year'       => $year
				 );
				 $compDate = $CI->dateconverter->ToGregorian($theDate['date_year'],$theDate['date_month'],$theDate['date_day']);
			 }
			 else
			 {
				 if($month=='00' && $year=='0000')
				 {
					 $theDate = array
					 (
						'date_day'        => '00',
						'date_month'      => '00',
						'date_year'       => '0000' 
					);
					$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
				 }
				 else
				 {
					 $theDate = array
					 (
						'date_day'        => $day,
						'date_month'      => $month,
						'date_year'       => $year
					 );
					 
					 $compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
				 }
			 }
		 }
		 
		 if($lang=='en')
		 {
			 if($month=='00' && $year=='0000')
			 {
				 $theDate = array
				 (
					'date_day'        => '00',
					'date_month'      => '00',
					'date_year'       => '0000', 
				);
				$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
			 }
			 else
			 {
				 $theDate = array
				 (
					'date_day'        => $day,
					'date_month'      => $month,
					'date_year'       => $year
				 );
				 
				 //$compDate = $theDate['date_year']."-".$theDate['date_month']."-".$theDate['date_day'];
				 $compDate = $CI->dateconverter->ToShamsi_short($theDate['date_year'],$theDate['date_month'],$theDate['date_day']);
			 }
		 }
		 return $compDate;
	}
}

if(!function_exists('datecheckyear'))
{   
	function datecheckyear($year,$lang)
	{
		$CI =& get_instance();
		$CI->load->library('dateconverter'); // load library
		
		 
		 //If language is Dari or Pashto then date is converted to gregorian format firstly 
		 if($lang=='dr' || $lang=='pa')
		 {
			 if( $year!='0000')
			 {
				 $theDate = array
				 (
					'date_years'       => $year
				 );
				 $compDate = $CI->dateconverter->ToGregorianyear($theDate['date_years']);
			 }
			 else
			 {
				 if( $year=='0000')
				 {
					 $theDate = array
					 (
						'date_years'       => '0000' 
					);
					$compDate = $theDate['date_years'];
				 }
				 else
				 {
					 $theDate = array
					 (
						'date_years'       => $year
					 );
					 
					 $compDate = $theDate['date_years'];
				 }
			 }
		 }
		 
		 if($lang=='en')
		 {
			 if( $year=='0000')
			 {
				 $theDate = array
				 (
					'date_years'       => '0000', 
				);
				$compDate = $theDate['date_years'];
			 }
			 else
			 {
				 $theDate = array
				 (
					'date_years'       => $year
				 );
				 
				 $compDate = $theDate['date_years'];
			 }
		 }
		 return $compDate;
	}
}

if(!function_exists('dateprovider'))
{
	function dateprovider($date,$lang='en')
	{ 
	  if($date =='0000-00-00')
	  {
		 return $date;
	  }
	  else
	  {  
		   
		   $CI =& get_instance();
		   $CI->load->library('dateconverter'); // load library
		                         
		   if($lang=='en')
		   {
			  return  $date;
		   }
		   else
		   {
				$date = explode("-",$date);
			   //return converted date
			   $date_converted = $CI->dateconverter->ToShamsi($date[0],$date[1],$date[2]);
			   return  $date_converted;
		   }
		   
		   
	  }
	}

}


if(!function_exists('dateprovider2'))
{
	function dateprovider2($date,$lang='en')
	{ 
	  if($date =='0000-00-00')
	  {
		 return $date;
	  }
	  else
	  {  
		   
		   $CI =& get_instance();
		   $CI->load->library('dateconverter'); // load library
		   
		   if($lang=='en')
		   {
			  return  $date;
		   }
		   else
		   {
				$date = explode("-",$date);
			   //return converted date
			   $date_converted = $CI->dateconverter->ToShamsi_short($date[0],$date[1],$date[2]);
			   return  $date_converted;
		   }
		   
		   
	  }
	}

}


/**
 * Search Date
 */
if(!function_exists('searchdate'))
{
   function searchdate($sd='00',$sm='00',$sy='0000',$ed='00',$em='00',$ey='0000',$datefield,$lang='en')
   {
	   //add prifix ziro for month options
	  if(strlen($sm)<2) 
	  {
		  $sm = "0".$sm;
	  }
	  if(strlen($em)<2) 
	  {
		  $em = "0".$em;
	  }
	  
	  $sql_casedate = 1; 
	  $CI =& get_instance();
	  $CI->load->library('dateconverter'); // load library 
	  if($datefield != "")
	  {
		   
		 
		  //provide date comparisan search
		 //d-m-y AND d-m-y 
		if($sd != '00' && $sm != '00' && $sy != '0000'
		  && $ed != '00' && $em != '00' && $ey != '0000')
		{
			 
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,$sm,$sd); 
			   $endDate = $CI->dateconverter->ToGregorian($ey,$em,$ed); 
			}
			else
			{
			   $startDate =$sy."-".$sm."-".$sd;
			   $endDate   =$ey."-".$em."-".$ed;
			}
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{
			  $sql_casedate = $datefield." BETWEEN '".$startDate."' AND '".$endDate."'";
			}
			else if($startDate > $endDate)
			{
			   $sql_casedate = $datefield." BETWEEN '".$endDate."' AND '".$startDate."'";
			}
			else if($startDate == $endDate)
			{
			   $sql_casedate = $datefield."='".$startDate."'";
			}
		} 
		
		//m-y AND m-y
		else if($sd == '00' && $sm != '00' && $sy != '0000'
		  && $ed == '00' && $em != '00' && $ey != '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,$sm,'01'); 
			   $endDate = $CI->dateconverter->ToGregorian($ey,$em,'01'); 
			}
			else
			{
			   $startDate =$sy."-".$sm."-"."01";
			   $endDate =$ey."-".$em."-"."01";
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			$edate = explode("-",$endDate);
			
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{ 
				//since user select just month and year
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%m') BETWEEN '".$sdate[0]."-".$sdate[1]."' AND '".$edate[0]."-".$edate[1]."'";
			}
			else if($startDate > $endDate)
			{
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%m') BETWEEN '".$edate[0]."-".$edate[1]."' AND '".$sdate[0]."-".$sdate[1]."'";
			}
			else if($startDate == $endDate)
			{
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%m') ='".$sdate[0]."-".$sdate[1]."'";
			}
			
		}
		
		 //y AND y
		else if($sd == '00' && $sm == '00' && $sy != '0000'
		  && $ed == '00' && $em == '00' && $ey != '0000')
		{
			 //convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $sy+621; 
			   $endDate = $ey + 621;
			}
			else
			{
			   $startDate =$sy;
			   $endDate =$ey;
			}
			
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{ 
				//since user select just month and year
				$sql_casedate = "YEAR(".$datefield.") BETWEEN '".$startDate."' AND '".$endDate."'";
			}
			else if($startDate > $endDate)
			{
				$sql_casedate = "YEAR(".$datefield.") BETWEEN '".$endDate."' AND '".$startDate."'";
			}
			else if($startDate == $endDate)
			{
				$sql_casedate = "YEAR(".$datefield.") ='".$startDate."'";
			}
		}
		
		//d-m AND d-m
		if($sd != '00' && $sm != '00' && $sy == '0000'
		  && $ed != '00' && $em != '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian('1389',$sm,$sd); 
			   $endDate = $CI->dateconverter->ToGregorian('1389',$em,$ed); 
			}
			else
			{
			   $startDate ="2010"."-".$sm."-".$sd;
			   $endDate ="2010"."-".$em."-".$ed;
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			$edate = explode("-",$endDate);
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{
				//since user select just month and year
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%m-%d') BETWEEN '".$sdate[1]."-".$sdate[2]."' AND '".$edate[1]."-".$edate[2]."'";
			}
			else if($startDate > $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%m-%d') BETWEEN '".$edate[1]."-".$edate[2]."' AND '".$sdate[1]."-".$sdate[2]."'";
			}
			else if($startDate == $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%m-%d') ='".$edate[1]."-".$edate[2]."'";
			}
			
			
		}
		
		//d-Y AND d-Y
		if($sd != '00' && $sm == '00' && $sy != '0000'
		  && $ed != '00' && $em == '00' && $ey != '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,'02',$sd); 
			   $endDate = $CI->dateconverter->ToGregorian($ey,'02',$ed); 
			}
			else
			{
			   $startDate =$sy."-".'04'."-".$sd;
			   $endDate =$sy."-".'04'."-".$sd;
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			$edate = explode("-",$endDate);
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{
				//since user select just month and year
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%d') BETWEEN '".$sdate[0]."-".$sdate[2]."' AND '".$edate[0]."-".$edate[2]."'";
			}
			else if($startDate > $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%d') BETWEEN '".$edate[0]."-".$edate[2]."' AND '".$sdate[0]."-".$sdate[2]."'";
			}
			else if($startDate == $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%d') ='".$edate[0]."-".$edate[2]."'";
			}
			
			
		}  
		
		//d AND d
		if($sd != '00' && $sm == '00' && $sy == '0000'
		  && $ed != '00' && $em == '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
				
			   $startDate = $CI->dateconverter->ToGregorian('1389','02',$sd); 
			   $endDate = $CI->dateconverter->ToGregorian('1389','02',$ed); 
			}
			else
			{
			   $startDate ="2009"."-".'04'."-".$sd;
			   $endDate ="2009"."-".'04'."-".$ed;
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			$edate = explode("-",$endDate);
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{
				//since user select just month and year
				$sql_casedate = "DATE_FORMAT(".$datefield.",'%d') BETWEEN '".$sdate[2]."' AND '".$edate[2]."'";
			}
			else if($startDate > $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%d') BETWEEN '".$edate[2]."' AND '".$sdate[2]."'";
			}
			else if($startDate == $endDate)
			{
			   $sql_casedate = "DATE_FORMAT(".$datefield.",'%d') ='".$edate[2]."'";
			}
			
			
		}
		
		//m AND m
		if($sd == '00' && $sm != '00' && $sy == '0000'
		  && $ed == '00' && $em != '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $sm+2; 
			   $endDate = $em+2;
			   //check if the date is not bigger than 12 december
			   if($startDate > 12)
			   {
				  $startDate = $startDate % 12;
			   }
			   
			   if($endDate > 12)
			   {
				  $endDate = $endDate % 12;
			   }
			   
			}
			else
			{
				$startDate = $sm; 
				$endDate = $em;
			}
			
			//check if the start date is smaller than end date
			if($startDate < $endDate)
			{
				//since user select just month and year
				$sql_casedate = "MONTH(".$datefield.") BETWEEN '".$startDate."' AND '".$endDate."'";
			}
			else if($startDate > $endDate)
			{
			   $sql_casedate = "MONTH(".$datefield.") BETWEEN '".$endDate."' AND '".$startDate."'";
			}
			else if($startDate == $endDate)
			{
			   $sql_casedate = "MONTH(".$datefield.") ='".$startDate."'";
			}
			//echo $sql_casedate;
			
		}
		
		//d-m-y
		if($sd != '00' && $sm != '00' && $sy != '0000'
		&& $ed == '00' && $em == '00' && $ed == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,$sm,$sd); 
			}
			else
			{
			   $startDate =$sy."-".$sm."-".$sd;
			}
			//if the user selected first date
			$sql_casedate = $datefield."='".$startDate."'";    
			
		}
		
		//m-y
		else if($sd == '00' && $sm != '00' && $sy != '0000'
		&& $ed == '00' && $em == '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,$sm,'01'); 
			}
			else
			{
			   $startDate =$sy."-".$sm."-"."01";
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%m') ='".$sdate[0]."-".$sdate[1]."'";
	
		}
		//y
		else if($sd == '00' && $sm == '00' && $sy != '0000'
		&& $ed == '00' && $em == '00' && $ey == '0000')
		{
			 //convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $sy+621; 
			}
			else
			{
			   $startDate =$sy;
			}
			$sql_casedate = "YEAR(".$datefield.") ='".$startDate."'";
		}
		
		//d-m
		if($sd != '00' && $sm != '00' && $sy == '0000'
		&& $ed == '00' && $em == '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian('1389',$sm,$sd); 
			}
			else
			{
			   $startDate ="2010"."-".$sm."-".$sd;
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			//date format
			$sql_casedate = "DATE_FORMAT(".$datefield.",'%m-%d') ='".$sdate[1]."-".$sdate[2]."'";
			
			
			
		}
		
		//d-Y
		if($sd != '00' && $sm == '00' && $sy != '0000'
		&& $ed == '00' && $em == '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian($sy,'02',$sd); 
			}
			else
			{
			   $startDate =$sy."-".'04'."-".$sd;
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
			
			$sql_casedate = "DATE_FORMAT(".$datefield.",'%Y-%d') ='".$sdate[0]."-".$sdate[2]."'";
			
			
			
		}
		
		//d
		if($sd != '00' && $sm == '00' && $sy == '0000'
		&& $ed == '00' && $em == '00' && $ey == '0000') 
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
			   $startDate = $CI->dateconverter->ToGregorian('1389','02',$sd); 
			  
			}
			else
			{
			   $startDate ="2009"."-".'04'."-".$sd;
			 
			}
			
			//explode date to year, month and day
			$sdate = explode("-",$startDate);
		
			$sql_casedate = "DATE_FORMAT(".$datefield.",'%d') ='".$sdate[2]."'";
		}
		
		//m
		if($sd == '00' && $sm != '00' && $sy == '0000'
		  && $ed == '00' && $em == '00' && $ey == '0000')
		{
			
			//convert start date and end date to miladi if language is dari or pashto
			if($lang =='dr' OR $lang == 'pa')
			{
				
			   $sdate = $CI->dateconverter->ToGregorian('1385',$sm,'4');   
			   $sdate = explode("-",$sdate);
			   $startDate  = $sdate[1];
			}
			else
			{
				$startDate = $sm; 
				
			}
			$sql_casedate = "MONTH(".$datefield.") BETWEEN '".$startDate."' AND '".($startDate+1)."'";
			
			
		}
		 return  $sql_casedate; 
	  } 
	  else
	  {
		return $sql_casedate;
	  }
   }
}

//field condtion keyword function
if(!function_exists('searchfield'))
{
	function searchfield($dbfield,$condition,$keyword)
	{
		  $sql_field = 1;
		  if($condition == 'ilike')
		  {
			  //check if its like condtion
			  //$sql_field = $dbfield." LIKE '%".$keyword."%'";
              $sql_field = "REPLACE(REPLACE(".$dbfield.",' ',''),'\n','') LIKE '%".str_replace(" ","",$keyword)."%'";  
		  }
		  else if($condition  == 'notilike')
		  {
			  //check if it is not like condtion
              $sql_field = "REPLACE(REPLACE(".$dbfield.",' ',''),'\n','') NOT LIKE '%".str_replace(" ","",$keyword)."%'"; 
			  //$sql_field = $dbfield." NOT LIKE '%".$keyword."%'";
		  }
		  else if($condition == '=')
		  {
			  //check if its equal condtion
			  $sql_field = "REPLACE(REPLACE(".$dbfield.",' ',''),'\n','')='".str_replace(" ","",$keyword)."'"; 
		  }
		  else if($condition == 'notequal')
		  {
			  //check if its not equal condtion
			  $sql_field = "REPLACE(REPLACE(".$dbfield.",' ',''),'\n','') <> '".str_replace(" ","",$keyword)."'"; 
		  }
		  else if($condition == 'bigger')
		  {
			  //check if its bigger > condtion 
			  $sql_field = $dbfield." >'".$keyword."'";   
		  }
		  else if($condition =='biggerequal')
		  {
			  //check if its biggerthan or equal >=
			  $sql_field = $dbfield." >='".$keyword."'"; 
		  }
		  else if($condition == 'less')
		  {
			  //check if its less < condtion
			  $sql_field = $dbfield." <'".$keyword."'"; 
		  }
		  else if($condition == 'lessequal')
		  {
			  //check if its lessthan and equal condtion <=
			  $sql_field = $dbfield." <='".$keyword."'"; 
		  }
		  else
		  {
			  $sql_field = 1;
		  }
		  //return generated sql result
		  return   $sql_field;
	}
}

/*
*access: public
* parrams: 
* 1.start age dropdown value 
* 2. end age dropdown value
* 3. dobage field name from database table
* 4. dob field name from db 
* 5. year field name from db
* 6. age field name from db
* 7. casedate field name from datbase
* 
* Note: if you want to just send the default non selection, just set dropdown
* value to 0 (zero)
*/

if(!function_exists('searchage'))
{
	
  function searchage($sage,$eage,$dobage,$dobfield,$yearfield,$agefield,$casedate)
  {
	  
	  //define sql var to return
	  $sql_agedate = 1;  
	  //if both of them zero send 1
	  if($sage=='00' && $eage=='00')
	  {
		  $sql_agedate = 1;  
	  }
	  else if($sage!='00' && $eage=='00')
	  {
		  //if they are equal
		  $startageyear = "YEAR(".$casedate.")-".$sage.""; 
		  $sql_agedate = " IF(".$dobage."=2,(".$yearfield."-".$agefield.")=".$startageyear."";
		  $sql_agedate .= " , YEAR(".$dobfield.") = '".$startageyear."')";
	
	  }
	  else if($sage=='00' && $eage!='00')
	  {
		  //if they are equal
		  $startageyear = "YEAR(".$casedate.")-".$eage.""; 
		  $sql_agedate = " IF(".$dobage."=2,(".$yearfield."-".$agefield.") =".$startageyear."";
		  $sql_agedate .= " , YEAR(".$dobfield.") = ".$startageyear.")";
	
	  } 
	  else if($sage < $eage)
	  {
		 //start and end age from case date 
		 $startageyear = "YEAR(".$casedate.")-".$eage.""; 
		 $endageyear = "YEAR(".$casedate.")-".$sage.""; 
		 
		 $sql_agedate = " IF(".$dobage."=2,(".$yearfield."-".$agefield.") BETWEEN ".$startageyear." AND ".$endageyear;
		 $sql_agedate .= " , YEAR(".$dobfield.") BETWEEN ".$startageyear." AND ".$endageyear.")";
	  }
	  else if($sage > $eage)
	  {
		  //if end age is smaller than start age
		  $startageyear = "YEAR(".$casedate.")-".$sage.""; 
		  $endageyear = "YEAR(".$casedate.")-".$eage.""; 
		 
		  $sql_agedate = " IF(".$dobage."=2,(".$yearfield."-".$agefield.") BETWEEN ".$startageyear." AND ".$endageyear;
		  $sql_agedate .= " , YEAR(".$dobfield.") BETWEEN ".$startageyear." AND ".$endageyear.")";
	
	  }
	  else if($sage == $eage)
	  {
		  //if they are equal
		  $startageyear = "YEAR(".$casedate.")-".$sage.""; 
		  $sql_agedate = " IF(".$dobage."=2,(".$yearfield."-".$agefield.") =".$startageyear."";
		  $sql_agedate .= " , YEAR(".$dobfield.") = ".$startageyear.")";
	
	  }
	  //return age sql
	  return $sql_agedate;
	  
  }

}

/*
*case summary 
*/
if(!function_exists('casesummary'))
{
	 function casesummary($caseURN)
	 {
		  //load header file
		  $CI =& get_instance(); 
		  
		  //load language file
		  $lang    = $CI->mng_auth->get_language();
		  if($lang=='dr')
		  {
			  $language = "dari"; 
		  }
		  else if($lang=='pa')
		  {
			  $language = "pashto"; 
		  }
		  else
		  {
			  $language = "english"; 
		  }
		  $CI->lang->load('home', $language);
		  
		  //provide other details
		  $data = array();
		  //get authentication library
		  $CI->load->library('mng_auth');
		  $CI->load->model('case/case_model');
		  $CaseObj = $CI->case_model->GetOneRecord("apirs_case","casenumber,userid,department,registerdate,transfer_status,classified,add_location,edit_location","case_urn",$caseURN);
		  if($CaseObj)
		  {
			 $ctrImg = ""; 
			 $dtrImg = ""; 
			 $caseTrUsers = "";
			 //if the case is transfered (provide an icon of transfer)
			 if($CaseObj->row()->transfer_status == 1)
			 {
				 //transfer icon
				 $ctrImg = "  <img src=\"".base_url()."images/transfer_case.png\" border=\"0\" title=\"".$CI->lang->line('home_transfered')."\" />";
				 //get all users where transfered
				 $TrnsUsers = $CI->case_model->CheckedTransferUsers($caseURN);
				 if(count($TrnsUsers)>0)
				 {
					 
					//check if the user is supervisor or fullaccess view/add 
					if(($CI->mng_auth->check_my_roles("r7") == TRUE) OR ($CI->mng_auth->check_my_roles("r10") == TRUE) OR ($CI->mng_auth->check_my_roles("r11") == TRUE) OR ($CI->mng_auth->check_my_roles("r4") == TRUE))
					{
						//get user list 
						for($i=0; $i<count($TrnsUsers); $i++)
						{
						   $caseTrUsers .= $CI->mng_auth->GetCaseOwner($TrnsUsers[$i]).", "; 
						}
						$caseTrUsers = substr($caseTrUsers,0,strlen($caseTrUsers)-2);
					}
					else
					{
						//check if my user in the array
						if(in_array($CI->mng_auth->get_user_id(),$TrnsUsers) == TRUE)
						{
						   $caseTrUsers = $CI->mng_auth->GetCaseOwner($CI->mng_auth->get_user_id()); 
						}
						else if($CaseObj->row()->classified == 'x')
						{
							   //get user list 
							for($i=0; $i<count($TrnsUsers); $i++)
							{
							   $caseTrUsers .= $CI->mng_auth->GetCaseOwner($TrnsUsers[$i]).", "; 
							}
							$caseTrUsers = substr($caseTrUsers,0,strlen($caseTrUsers)-2);
						}
					}
					$data['caseowner'] = $caseTrUsers;
				 }
				 else
				 {
					 $data['caseowner']  = $CI->mng_auth->GetCaseOwner($CaseObj->row()->userid); 
				 }
				 
				 //get transfered department name
				 $CaseTrDepObj = $CI->case_model->GetOneRecord("apirs_case_transfer","department,type","case_urn",$caseURN);
				 if($CaseTrDepObj)
				 {
					 //if there is any department
					 $data['depname']    = $CI->mng_auth->GetDepartmentName($CaseTrDepObj->row()->department); 
					 if($CaseTrDepObj->row()->type == 1)
					 {
						 //transfer icon
						 $dtrImg = "  <img src=\"".base_url()."images/transfer_case.png\" border=\"0\" title=\"".$CI->lang->line('home_transfered')."\" />";
					 }
			
				 }
				 else
				 {
					$data['depname']    = $CI->mng_auth->GetDepartmentName($CaseObj->row()->department); 
				 }
                
			 }
			 else
			 {
				 
                 //get case owner
				 $data['caseowner']  = $CI->mng_auth->GetCaseOwner($CaseObj->row()->userid); 
				 $data['depname']    = $CI->mng_auth->GetDepartmentName($CaseObj->row()->department); 
                 
			 } 
             //===== check if user has share role  ====
             //list the users shared to this case

             $sharedUsers = $CI->mng_auth->getAllSharedUsers($caseURN);
             if($sharedUsers)
             {
                 
                 
                 $CI->load->model('province/province_model');
                 
                 $todeps  = "";
                 $tozones = "";
                 $users = array();
                 
                 foreach($sharedUsers->result() AS $item)
                 {
                     //check share type ===
                     if($item->share_type == 0)
                     {
                        //=== get usernames ====
                        //=== check if there is any comma seperated
                        if(substr_count($item->userid,",")>0)
                        {
                            $users = explode(",",$item->userid);
                        }
                        else
                        {
                            $users[] = $item->userid;
                        }
                          
                     }
                     else if($item->share_type == 2)
                     {
                           //get department names
                           $deps = array();
                           if(substr_count($item->department,",")>0)
                           {
                                $deps = explode(",",$item->department);
                           }
                           else
                           {
                                $deps[] = $item->department;
                           }
                           
                           //== get deparmtment names
                           $todeps .= str_replace(",",", ",$CI->user_model->GetDepartmentNames($deps,$CI->mng_auth->get_language())); 
                           
                     }
                     else if($item->share_type == 1)
                     {
                         //=== get zone details
                         $prv = array();
                         if(substr_count($item->province,",")>0)
                         {
                            $prv = explode(",",$item->province);
                         }
                         else
                         {
                            $prv[] = $item->province;
                         }
                         $tozones .= str_replace(",",", ",$CI->province_model->GetZonesByPrCodes($prv)); 
  
                     }
                     break;
                     
                 }
                 // send to view file
                 $data['sharedusers'] = $sharedUsers;
                 $data['users']       = $CI->mng_auth->GetUsernames($users);
                 $data['deps']        = $todeps;
                 $data['zones']       = $tozones;
             }
             else
             {
                 $data['sharedusers'] ="";
             }
             
             $data['casenumber']    = $CaseObj->row()->casenumber; 
             $data['add_location']  = $CaseObj->row()->add_location; 
			 $data['edit_location'] = $CaseObj->row()->edit_location;
             $data['add_location']  = $CI->mng_auth->get_province_name($CaseObj->row()->add_location);
             //echo $CaseObj->row()->add_location; exit;  
			 $date               = explode(" ",$CaseObj->row()->registerdate);  
			 $data['date']       = dateprovider($date[0],$lang);  
			 $data['time']       = $date[1];  
			 $data['timage']     = $ctrImg;  
			 $data['dtimage']     = $dtrImg;  
			 return $CI->load->view('case/case_summary',$data,TRUE);
		  }
	
	 }
}


/*
* report summary
*/
if(!function_exists('reportsummary'))
{
	function reportsummary($caseURN, $action="add",$reportURN=0,$form_name = "add_form",$repLink = "#",$divid="")
	{	
		  //load CI core libs
		  $CI =& get_instance(); 
		  
		  //load language file
		  $lang    = $CI->mng_auth->get_language();
		  if($lang=='dr')
		  {
			  $language = "dari"; 
		  }
		  else if($lang=='pa')
		  {
			  $language = "pashto"; 
		  }
		  else
		  {
			  $language = "english"; 
		  }
		  $CI->lang->load('home', $language);
		  //get authentication library
		  $CI->load->library('mng_auth');
		  $CI->load->library('clean_encrypt');
		  $CI->load->model('report/report_model');
		  $repObj = $CI->report_model->GetReportsByCaseURN($caseURN);
		  if($repObj)
		  {
			 //provide other details
			 $data = array();
			 
			 //if there is any report id
			 $repDrop   = "";
			 $reporturn = "";
             $selectedLast = "";
             $counter = 0;
			 if($action == "add" OR $action == "view")
			 {
			 
				
				if($action == "add")
				{
					$default = $CI->lang->line('home_selectreport'); 
				}
				else if($action == "view")
				{
				   $default = $CI->lang->line('home_allrep'); 
				}
				//check if there is one report than provide selected option
				if($repObj->num_rows() == 1)
				{
					$en_reports = $CI->clean_encrypt->encode($repObj->row()->report_urn.'='.$repObj->row()->report_urn); 
					$repDrop="<option value=\"".$en_reports."\" selected=\"selected\">".$repObj->row()->report_urn."</option>";
					$reportURN = $en_reports;
				}
				else
				{
					 //provide selection
					 //$repDrop   = "<option value=\"\" selected=\"selected\">".$default."</option>";
					 foreach($repObj->result() AS $row)
					 {
						 $en_reports = $CI->clean_encrypt->encode($row->report_urn.'='.$row->report_urn); 
                         if($en_reports == $reportURN && strlen($reportURN) > 0)
						 {
							 $repDrop.="<option value=\"".$en_reports."\" selected=\"selected\">".$row->report_urn."</option>";
						 }
						 else
						 {
                             $repDrop.="<option value=\"".$en_reports."\">".$row->report_urn."</option>";  
						 }
					 }
				}
			 }
			 else if($action == "edit")
			 {
				$repDrop="<option value=\"".$reportURN."\" selected=\"selected\">".$reportURN."</option>";
			 }
			 else
			 {
				 $reporturn = $reportURN;
			 }
			 
			 //encrypted case urn
			 $en_caseid = $CI->clean_encrypt->encode($caseURN.'='.$caseURN); 
			 
			 $data['option']     = $repDrop; 
			 $data['repObj']     = $repObj; 
			 $data['action']     = $action; 
			 $data['reportURN']  = $reporturn; 
			 $data['formname']   = $form_name; 
			 $data['link']       = $repLink; 
			 $data['caseurn']    = $caseURN; 
			 $data['enc_caseid'] = $en_caseid; 
             $data['divid']      = $divid; 
             if($repObj)
             {
                 if($repObj->num_rows()>0)
                 {
                    $data['add_prlocation']   = $repObj->row()->add_prlocation;  
                 }
                 else
                 {
                    $data['add_prlocation'] = "";
                 }
             }
             else
             {
                   $data['add_prlocation'] = "";
             }
             
	         //$data['addlocation']= $addlocation; 
			 
			 //get department list
			 //get available departments to data entry
			 $AllDepObj = $CI->mng_auth->GetDepartmentsByRole(array('r1','r7','r11','r29'),TRUE);
			 //department option List
			 $depOptList = "";
			 if($AllDepObj)
			 {
				//if there is one department get department id and provide set select
				if($AllDepObj->num_rows()==1)
				{
					//SET DEPARTMENT ID TO GET DEPARTMENT
					foreach($AllDepObj->result() AS $item)
					{
						$depOptList.="<option value=\"".$item->id."\" ".set_select("n_department",$item->id,TRUE).">".$item->name."</option>";
					}
				}
				else
				{
					$depOptList = "<option value=\"\"  selected=\"selected\">".$CI->lang->line('home_selectdep')."</option>";
					//provide multiple department option and to select
					foreach($AllDepObj->result() AS $item)
					{
						$depOptList.="<option value=\"".$item->id."\" ".set_select("n_department",$item->id,FALSE).">".$item->name."</option>";
					}
				}
			 }
			 $data['departments']     = $depOptList;
			 $data['depObj']          = $AllDepObj;
             
			 
			 //load the view file
			 return $CI->load->view('case/report_summary',$data,TRUE);
		  }
	}
} 
/*
*  reportsummary_shared
*/
if(!function_exists('reportsummary_shared'))
{
    function reportsummary_shared($caseURN, $action="add",$reportURN=0,$form_name = "add_form",$repLink = "#",$divid="")
    {    
          //load CI core libs
          $CI =& get_instance(); 
          
          //load language file
          $lang    = $CI->mng_auth->get_language();
          if($lang=='dr')
          {
              $language = "dari"; 
          }
          else if($lang=='pa')
          {
              $language = "pashto"; 
          }
          else
          {
              $language = "english"; 
          }
          $CI->lang->load('home', $language);
          //get authentication library
          $CI->load->library('mng_auth');
          $CI->load->library('clean_encrypt');
          $CI->load->model('report/report_model');
        
          $repObj = $CI->report_model->GetSharedReportsByCaseURN($caseURN);
          //print_r($repObj->row_array());exit;
          if($repObj)
          {
             //provide other details
             $data = array();
             
             //if there is any report id
             $repDrop   = "";
             $reporturn = "";
             $selectedLast = "";
             $counter = 0;
             if($action == "add" OR $action == "view")
             {
             
                
                if($action == "add")
                {
                    $default = $CI->lang->line('home_selectreport'); 
                }
                else if($action == "view")
                {
                   $default = $CI->lang->line('home_allrep'); 
                }
                //check if there is one report than provide selected option
                if($repObj->num_rows() == 1)
                {
                    $en_reports = $CI->clean_encrypt->encode($repObj->row()->report_urn.'='.$repObj->row()->report_urn); 
                    $repDrop="<option value=\"".$en_reports."\" selected=\"selected\">".$repObj->row()->report_urn."</option>";
                    $reportURN = $en_reports;
                }
                else
                {
                     //provide selection
                     //$repDrop   = "<option value=\"\" selected=\"selected\">".$default."</option>";
                     foreach($repObj->result() AS $row)
                     {
                         
                         $en_reports = $CI->clean_encrypt->encode($row->report_urn.'='.$row->report_urn); 
                         if($en_reports == $reportURN && strlen($reportURN) > 0)
                         {
                             $repDrop.="<option value=\"".$en_reports."\" selected=\"selected\">".$row->report_urn."</option>";
                         }
                         else
                         {
                             $repDrop.="<option value=\"".$en_reports."\">".$row->report_urn."</option>";  
                         }
                         
                     } 
                }
             }
             else if($action == "edit")
             {
                $repDrop="<option value=\"".$reportURN."\" selected=\"selected\">".$reportURN."</option>";
             }
             else
             {
                 $reporturn = $reportURN;
             }
             
             //encrypted case urn
             $en_caseid = $CI->clean_encrypt->encode($caseURN.'='.$caseURN); 
             
             $data['option']     = $repDrop; 
             $data['repObj']     = $repObj; 
             $data['action']     = $action; 
             $data['reportURN']  = $reporturn; 
             $data['formname']   = $form_name; 
             $data['link']       = $repLink; 
             $data['caseurn']    = $caseURN; 
             $data['enc_caseid'] = $en_caseid; 
             $data['divid']      = $divid; 
             //$data['add_prlocation']   = $repObj->row()->add_prlocation; 
             //$data['addlocation']= $addlocation; 
             
             //get department list
             //get available departments to data entry
             $AllDepObj = $CI->mng_auth->GetDepartmentsByRole(array('r1','r7','r11','r29'),TRUE);
             //department option List
             $depOptList = "";
             if($AllDepObj)
             {
                //if there is one department get department id and provide set select
                if($AllDepObj->num_rows()==1)
                {
                    //SET DEPARTMENT ID TO GET DEPARTMENT
                    foreach($AllDepObj->result() AS $item)
                    {
                        $depOptList.="<option value=\"".$item->id."\" ".set_select("n_department",$item->id,TRUE).">".$item->name."</option>";
                    }
                }
                else
                {
                    $depOptList = "<option value=\"\"  selected=\"selected\">".$CI->lang->line('home_selectdep')."</option>";
                    //provide multiple department option and to select
                    foreach($AllDepObj->result() AS $item)
                    {
                        $depOptList.="<option value=\"".$item->id."\" ".set_select("n_department",$item->id,FALSE).">".$item->name."</option>";
                    }
                }
             }
             $data['departments']     = $depOptList;
             $data['depObj']          = $AllDepObj;
             
             
             //load the view file
             return $CI->load->view('shareview/report_summary',$data,TRUE);
          }
          
          
          
    }
}



?>
