<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * Date converter script is to convert date betweeen hijri shamsi and gregorean format
 * The date conversion between hijri to gregorian there is a logical error 
 * which converts 10-12-1390 to 29-02-2011, Febrauary can not  be 29 days
 * so here we have solved by taking the module of hijri year by 4 and if the reminder is 2
 * and hijri month is 12, the reseult will be 01-03-2011 and vice versa to convert again to hijri
 * the same process will continue
 * 
 * @package       CodeIgniter
 * @author        Mehdi Jalal
 * @email         <mehdi.jalal@live.com><mehdi.jalal@netlinks.af>
 * @version       0.01
 */
class Dateconverter
{
	
		//days in month in gregorean and shamsi 
		private $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		private $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		//miladi to shamsi
		function GregorianToJalali($g_y, $g_m, $g_d)
		{
			$g_days_in_month = $this->g_days_in_month;
			$j_days_in_month = $this->j_days_in_month;
			   
		   $gy = $g_y-1600;
		   $gm = $g_m-1;
		   $gd = $g_d-1;

		   $g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);

		   for ($i=0; $i < $gm; ++$i)
			  $g_day_no += $g_days_in_month[$i];
		   if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
			  /* leap and after Feb */
			  ++$g_day_no;
		   $g_day_no += $gd;

		   $j_day_no = $g_day_no-79;

		   $j_np = $this->div($j_day_no, 12053);
		   $j_day_no %= 12053;

		   $jy = 979+33*$j_np+4*$this->div($j_day_no,1461);

		   $j_day_no %= 1461;

		   if ($j_day_no >= 366) {
			  $jy += $this->div($j_day_no-1, 365);
			  $j_day_no = ($j_day_no-1)%365;
		   }

		   for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
			  $j_day_no -= $j_days_in_month[$i];
		   }
		   $jm = $i+1;
		   $jd = $j_day_no+1;
			return array($jy, $jm, $jd);
		}
		//hijri shamsi to miladi
		function JalaliToGregorian($year,$month,$day)
		{
			$gDaysInMonth = $this->g_days_in_month;
			$jDaysInMonth = $this->j_days_in_month;
			$jy=$year-979;
			$jm=$month-1;
			$jd=$day-1;
			$jDayNo=365*$jy + $this->div($jy,33)*8 + $this->div((($jy%33)+3),4);
				for ($i=0; $i < $jm; ++$i)  
				$jDayNo += $jDaysInMonth[$i];
			$jDayNo +=$jd;
			$gDayNo=$jDayNo + 79;
			//146097=365*400 +400/4 - 400/100 +400/400
			$gy=1600+400*$this->div($gDayNo,146097);
			$gDayNo = $gDayNo%146097;
			$leap=1;
			if($gDayNo >= 36525)
			{
				$gDayNo =$gDayNo-1;
				//36524 = 365*100 + 100/4 - 100/100
				$gy +=100* $this->div($gDayNo,36524);
				$gDayNo=$gDayNo % 36524;

				if($gDayNo>=365)
				$gDayNo = $gDayNo+1;
				else
				$leap=0;
			}
			//1461 = 365*4 + 4/4
			$gy += 4*$this->div($gDayNo,1461);
			$gDayNo %=1461;
			if($gDayNo>=366)
			{
				$leap=0;
				$gDayNo=$gDayNo-1;
				$gy += $this->div($gDayNo,365);
				$gDayNo=$gDayNo %365;
			}
			$i=0;
			$tmp=0;
			while ($gDayNo>= ($gDaysInMonth[$i]+$tmp))
			{
				if($i==1 && $leap==1)
				$tmp=1;
				else
				$tmp=0;

				$gDayNo -= $gDaysInMonth[$i]+$tmp;
				$i=$i+1;
			}
			$gm=$i+1;
			$gd=$gDayNo+1;
			return array($gy, $gm, $gd);
		}

		function div($a, $b) {
		   return (int) ($a / $b);
		}
	
		function grgIsLeap ($Year)
		{
			return (($Year%4) == 0 && (($Year%100) != 0 || ($Year%400) == 0));
		}
		 
		function hshIsLeap ($Year)
		{
			$Year = ($Year - 474) % 128;
			$Year = (($Year >= 30) ? 0 : 29) + $Year;
			$Year = $Year -round($Year/33) - 1;
			return (($Year % 4) == 0);
		}
		
		function date_div($a,$b) 
		{
			return (int) ($a / $b);
		}
		
		function jalali_to_gregorian($j_y, $j_m, $j_d) 
		{ 
			
			
			$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
			$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

			$jy = $j_y-979; 
			$jm = $j_m-1; 
			$jd = $j_d-1; 

			$j_day_no = 365*$jy + $this->date_div($jy, 33)*8 + $this->date_div($jy%33+3, 4); 
			for ($i=0; $i < $jm; ++$i) 
			  $j_day_no += $j_days_in_month[$i]; 

		   $j_day_no += $jd; 

		   $g_day_no = $j_day_no+79; 

		   $gy = 1600 + 400*$this->date_div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */ 
		   $g_day_no = $g_day_no % 146097; 

		   $leap = true; 
		   if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */ 
		   { 
			  $g_day_no--; 
			  $gy += 100*$this->date_div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */ 
			  $g_day_no = $g_day_no % 36524; 

			  if ($g_day_no >= 365) 
				 $g_day_no++; 
			  else 
				 $leap = false; 
		   } 

		   $gy += 4*$this->date_div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */ 
		   $g_day_no %= 1461; 

		   if ($g_day_no >= 366) { 
			  $leap = false; 

			  $g_day_no--; 
			  $gy += $this->date_div($g_day_no, 365); 
			  $g_day_no = $g_day_no % 365; 
		   } 

		   for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++) 
			  $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap); 
		   $gm = $i+1; 
		   $gd = $g_day_no+1; 
		   if ($j_m == 12 && $gm==2 && $gd==29)
			{
			   $gd = 1;
			   $gm= 3;
			}
			
			if(strlen($gd)==1)
			{
				 $gd="0".$gd;
			}
			if(strlen($gm)==1)
			{
				 $gm="0".$gm;
			}
			return  $gy."-".$gm."-".$gd;  
		}
		//convert hijri to gregoian  2011-03-09
		function ToGregorian ($hshYear,$hshMonth,$hshDay)
		{
			$miladiDate = array();  
			$miladiDate = $this->jalali_to_gregorian2($hshYear."-".$hshMonth."-".$hshDay);
			
			return  $miladiDate;
			/*
			$gd  = $miladiDate[2];
			$gm  = $miladiDate[1];
			$gy  = $miladiDate[0];
			if(strlen($gd)==1)
			{
				 $gd="0".$gd;
			}
			if(strlen($gm)==1)
			{
				 $gm="0".$gm;
			}
			//check if the year with module 4 is 2 and month 12 and day is 10
			if($hshYear % 4 == 2 && $hshMonth==12 &&  $hshDay==10)
			{
			   return  $gy."-02-29";
			}
			else
			{
				return  $gy."-".$gm."-".$gd;  
			}
			*/
			//return $this->jalali_to_gregorian($hshYear,$hshMonth,$hshDay);
			/*exit;
		
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));
		 
			$grgYear = $hshYear+621;
			$grgMonth;
			$grgDay;
		 
			$hshLeap=$this->hshIsLeap($hshYear);
			$grgLeap=$this->grgIsLeap($grgYear);
			$hshElapsed=$hshSumOfDays [$hshLeap ? 1:0][$hshMonth-1]+$hshDay;
			$grgElapsed;
		 
			if ($hshMonth > 10 || ($hshMonth == 10 && $hshElapsed > 286+($grgLeap ? 1:0)))
			{
				$grgYear=$grgYear+1;
				$grgElapsed = $hshElapsed - (285 + ($grgLeap ? 1:0));
				$grgLeap = $this->grgIsLeap ($grgYear+1);
			}
			else
			{
				$hshLeap =$this->hshIsLeap ($hshYear-1);
				$grgElapsed = $hshElapsed + 79 + ($hshLeap ? 1:0) - ($this->grgIsLeap($grgYear-1) ? 1:0);
			}
		 
			for ($i=1; $i <= 12; $i++)
			{
				if ($grgSumOfDays [$grgLeap ? 1:0][$i] >= $grgElapsed)
				{
					$grgMonth = $i;
					$grgDay = $grgElapsed - $grgSumOfDays [$grgLeap ? 1:0][$i-1];
					break;
				}
			}
			if ($hshMonth == 12 && $grgMonth==2 && $grgDay==29)
			{
			   $grgDay = 1;
			   $grgMonth= 3;
			}
			
			if(strlen($grgDay)==1)
			{
				 $grgDay="0".$grgDay;
			}
			if(strlen($grgMonth)==1)
			{
				 $grgMonth="0".$grgMonth;
			}
			return  $grgYear."-".$grgMonth."-".$grgDay;   */
		}
		
		function ToGregorianyear ($hshYear)
		{
			
		
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));
		 
			$grgYear = $hshYear+621;
		 
			$hshLeap=$this->hshIsLeap($hshYear);
			$grgLeap=$this->grgIsLeap($grgYear);
			$hshElapsed=$hshSumOfDays [$hshLeap ? 1:0];
			$grgElapsed;
		
			return  $grgYear;
		}
		//convert gregorean to hijri shamsi format
		//2 Hamal 1390
		function ToShamsi($grgYear,$grgMonth,$grgDay)
		{
			
			$shamsiDate = array();
			$shamsiDate = $this->GregorianToJalali($grgYear,$grgMonth,$grgDay);
			$year  = $shamsiDate[0];
			$month = $shamsiDate[1];
			$day   = $shamsiDate[2];
			if($year % 4 == 2 && $grgMonth==3 && $grgDay==1)
			{
			   $day = $day-1;
			}
			if(strlen($day)==1)
			{
				 $day="0".$day;
			}
			if(strlen($month)==1)
			{
				 $month="0".$month;
			}
			return  $day." ".$this->monthname_shamsi($month)." ".$year; 
			
			/*
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));

			$hshYear = $grgYear-621;
			$hshMonth;
			$hshDay;
			
			$grgLeap=$this->grgIsLeap ($grgYear);
			$hshLeap=$this->hshIsLeap ($hshYear-1);
			$hshElapsed;
			
			$grgElapsed = $grgSumOfDays[($grgLeap ? 1:0)][$grgMonth-1]+$grgDay;
			 $XmasToNorooz = ($hshLeap && $grgLeap) ? 80 : 79;

			if ($grgElapsed <= $XmasToNorooz)
			{
				$hshElapsed = $grgElapsed+286;
				$hshYear--;
				if ($hshLeap && !$grgLeap)
					$hshElapsed++;
			}
			else
			{
				$hshElapsed = $grgElapsed - $XmasToNorooz;
				$hshLeap = $this->hshIsLeap ($hshYear);
			}
			 
			for ($i=1; $i <= 12 ; $i++)
			{
				if ($hshSumOfDays [($hshLeap ? 1:0)][$i] >= $hshElapsed)
				{
					$hshMonth = $i;
					$hshDay = $hshElapsed - $hshSumOfDays [($hshLeap ? 1:0)][$i-1];
					break;
				}
			}
			if($hshYear % 4 == 2 && $grgMonth==3 && $grgDay==1)
			{
			   $hshDay = $hshDay-1;
			}
			
			 return  $hshDay.' '.$this->monthname_shamsi($hshMonth). " ".$hshYear;
			*/ 
		}
		
		function ToShamsiYear($grgYear)
		{
			
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));

			$hshYear = $grgYear-621;
			$hshMonth;
			$hshDay;
			
			$grgLeap=$this->grgIsLeap ($grgYear);
			$hshLeap=$this->hshIsLeap ($hshYear-1);
			$hshElapsed;
			
			$grgElapsed = $grgSumOfDays[($grgLeap ? 1:0)];
			 $XmasToNorooz = ($hshLeap && $grgLeap) ? 80 : 79;

			if ($grgElapsed <= $XmasToNorooz)
			{
				$hshElapsed = $grgElapsed+286;
				$hshYear--;
				if ($hshLeap && !$grgLeap)
					$hshElapsed++;
			}
			else
			{
				//$hshElapsed = $grgElapsed - $XmasToNorooz;
				$hshLeap = $this->hshIsLeap ($hshYear);
			}
			 
			 return $hshYear;
			 
		}
		
		function dayname_shamsi($gday)
		{
		 $gname_day=Array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		 $sname_day=Array('دوشنبه','سه شنبه','چهارشنبه','پنج شنبه','جمعه','شنبه','يکشنبه');    
		 for($i=0;$i<sizeof($gname_day);$i++)
		 {
			if($gname_day[$i]==$gday)
			{
			  return $sname_day[$i];
			  break;
			}
		 }
		}
		
		function monthname_shamsi($month)
		{
		 $smonthname_day=Array('حمل','ثور','جوزا','سرطان','اسد','سنبله','ميزان','عقرب','قوس','جدي','دلو','حوت');    
		 switch($month)
		 {
		  case '1':{return $smonthname_day[0];}break;
		  case '2':{return $smonthname_day[1];}break; 
		  case '3':{return $smonthname_day[2];}break; 
		  case '4':{return $smonthname_day[3];}break; 
		  case '5':{return $smonthname_day[4];}break; 
		  case '6':{return $smonthname_day[5];}break; 
		  case '7':{return $smonthname_day[6];}break; 
		  case '8':{return $smonthname_day[7];}break; 
		  case '9':{return $smonthname_day[8];}break; 
		  case '10':{return $smonthname_day[9];}break; 
		  case '11':{return $smonthname_day[10];}break; 
		  case '12':{return $smonthname_day[11];}break; 
		 
		 }
		}
		//to short shamsi format 1390-02-20
		function ToShamsi_short($grgYear,$grgMonth,$grgDay)
		{
			$shamsiDate = array();
			$shamsiDate = $this->GregorianToJalali($grgYear,$grgMonth,$grgDay);
			$year  = $shamsiDate[0];
			$month = $shamsiDate[1];
			$day   = $shamsiDate[2];
			if($year % 4 == 2 && $grgMonth==3 && $grgDay==1)
			{
			   $day = $day-1;
			}
			if(strlen($day)==1)
			{
				 $day="0".$day;
			}
			if(strlen($month)==1)
			{
				 $month="0".$month;
			}
			return  $year."-".$month. "-".$day; 
			/*
			
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));

			$hshYear = $grgYear-621;
			$hshMonth;
			$hshDay;
			
			$grgLeap=$this->grgIsLeap ($grgYear);
			$hshLeap=$this->hshIsLeap ($hshYear-1);
			$hshElapsed;
			
			$grgElapsed = $grgSumOfDays[($grgLeap ? 1:0)][$grgMonth-1]+$grgDay;
			 $XmasToNorooz = ($hshLeap && $grgLeap) ? 80 : 79;

			if ($grgElapsed <= $XmasToNorooz)
			{
				$hshElapsed = $grgElapsed+286;
				$hshYear--;
				if ($hshLeap && !$grgLeap)
					$hshElapsed++;
			}
			else
			{
				$hshElapsed = $grgElapsed - $XmasToNorooz;
				$hshLeap = $this->hshIsLeap ($hshYear);
			}
			 
			for ($i=1; $i <= 12 ; $i++)
			{
				if ($hshSumOfDays [($hshLeap ? 1:0)][$i] >= $hshElapsed)
				{
					$hshMonth = $i;
					$hshDay = $hshElapsed - $hshSumOfDays [($hshLeap ? 1:0)][$i-1];
					break;
				}
			}
			if($hshYear % 4 == 2 && $grgMonth==3 && $grgDay==1)
			{
			   $hshDay = $hshDay-1;
			}
			
			if(strlen($hshDay)==1)
			{
				 $hshDay="0".$hshDay;
			}
			if(strlen($hshMonth)==1)
			{
				 $hshMonth="0".$hshMonth;
			}
			 return  $hshDay.'-'.$hshMonth. "-".$hshYear;
			*/ 
		}
		
		function ToShamsi_shortYear($grgYear)
		{
			
			$grgSumOfDays=Array(Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),Array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
			$hshSumOfDays=Array(Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), Array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));

			$hshYear = $grgYear-621;
			
			$grgLeap=$this->grgIsLeap ($grgYear);
			$hshLeap=$this->hshIsLeap ($hshYear-1);
			$hshElapsed;
			
			$grgElapsed = $grgSumOfDays[($grgLeap ? 1:0)];
			 $XmasToNorooz = ($hshLeap && $grgLeap) ? 80 : 79;

			if ($grgElapsed <= $XmasToNorooz)
			{
				$hshElapsed = $grgElapsed+286;
				$hshYear--;
				if ($hshLeap && !$grgLeap)
					$hshElapsed++;
			}
			else
			{
			   // $hshElapsed = $grgElapsed - $XmasToNorooz;
				$hshLeap = $this->hshIsLeap ($hshYear);
			}
			 
			 return $hshYear;
			 
		}

		//============ali date converter modified======
		function jalali_to_gregorian2($jalali_date)
		{
			$date_array = explode("-",$jalali_date);
			if(count($date_array) > 2)
			{
				$j_y = $date_array[0];
				$j_m = $date_array[1];
				$j_d = $date_array[2];    
			}
			else
			{
				$j_y = 0;
				$j_m = 0;
				$j_d = 0;                
			}
		
			$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
			$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

			$jy = $j_y-979;
			$jm = $j_m-1;
			$jd = $j_d-1;

			$j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4);
			for ($i=0; $i < $jm; ++$i)
				$j_day_no += $j_days_in_month[$i];
		
			$j_day_no += $jd;
			$g_day_no = $j_day_no+79;

			$gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
			$g_day_no = $g_day_no % 146097;

			$leap = true;

			if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
			{
				$g_day_no--;
				$gy += 100*$this->div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
				$g_day_no = $g_day_no % 36524;

				if ($g_day_no >= 365)
					$g_day_no++;
				else
					$leap = false;

			}

			$gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
			$g_day_no %= 1461;

			if ($g_day_no >= 366) 
			{
				$leap = false;
				$g_day_no--;
				$gy += $this->div($g_day_no, 365);
				$g_day_no = $g_day_no % 365;

			}

			for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
				$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
		
			$gm = $i+1;
			$gd = $g_day_no+1;


			return implode("-",array($gy, str_pad($gm, 2, "0", STR_PAD_LEFT), str_pad($gd, 2, "0", STR_PAD_LEFT)));

		}
		
		
}
?>