<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * @author Mohammad Rashidi
 * @copyright 2010
 */

//--------------------------------------------
// jdate : Persian date() class
//
// Author : AHHP(Amir Hossein Hodjaty Pour) ~ Boplo.ir
// Based on : haghparast@gmail.com Persian Calendar Class
// License : GPL
// 1387/12/24	02:02 am
//--------------------------------------------

class Jcalender
{
	var $format = array(); // Assoc array, container of all date chars
	var $jformat = array(); // Assoc array, container of all jdate chars
	
	function jdate($format, $stamp=null, $GMT=null)
	{
		$GMT = isset($GMT) ? $GMT : date("Z");
		$stamp = isset($stamp) ? ($stamp+$GMT) : (time()+$GMT);
		
		$formatArr = array(
			'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 't', 'L', 'o', 'Y', 'y',
			'a','A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'e', 'I', 'O', 'P', 'T', 'Z', 'c', 'r', 'U'
		);
		$fullFormat = explode("|", date(join("|", $formatArr), $stamp) );
		
		$count = count($formatArr);
		for($i=0; $i<$count; $i++)
			$this->format[$formatArr[$i]] = $fullFormat[$i];
		
		
		
		// Y
		list($this->jformat['Y'], $this->jformat['m'], $this->jformat['d']) = $this->gregorian_to_jalali($this->format['Y'], $this->format['m'], $this->format['d']);
		
		// a
		$this->jformat['a'] = ($this->format['a']=="pm") ? "ب.ظ" : "ق.ظ";
		
		// A
		$this->jformat['A'] = ($this->format['A']=="PM") ? "بعد از ظهر" : "قبل از ظهر";
		
		// B
		$this->jformat['B'] = $this->format['B'];
		
		// D
		switch ( strtolower($this->format['D']) )
		{
			case "sat" : $this->jformat['D'] = "ش"; break;
			case "sun" : $this->jformat['D'] = "ي"; break;
			case "mon" : $this->jformat['D'] = "د"; break;
			case "tue" : $this->jformat['D'] = "س"; break;
			case "wed" : $this->jformat['D'] = "چ"; break;
			case "thu" : $this->jformat['D'] = "پ"; break;
			case "fri" : $this->jformat['D'] = "ج"; break;
		}
		
		// F
		$this->jformat['F'] = $this->ReturnMonthName($this->jformat['m']);
		
		// h
		$this->jformat['h'] = $this->format['h'];
		
		// H
		$this->jformat['H'] = $this->format['H'];
		
		// g
		$this->jformat['g'] = $this->format['g'];
		
		// G
		$this->jformat['G'] = $this->format['G'];
		
		// i
		$this->jformat['i'] = $this->format['i'];
		
		// d
		$this->jformat['d'] = ($this->jformat['d'] < 10) ? "0".$this->jformat['d'] : $this->jformat['d'];
		
		// j
		$this->jformat['j'] = $this->jformat['d'];
		
		// l
		switch ( strtolower($this->format['l']) )
		{
			case "saturday" : $this->jformat['l'] = "شنبه"; break;
			case "sunday" : $this->jformat['l'] = "يکشنبه"; break;
			case "monday" : $this->jformat['l'] = "دوشنبه"; break;
			case "tuesday" : $this->jformat['l'] = "سه شنبه"; break;
			case "wednesday" : $this->jformat['l'] = "چهارشنبه"; break;
			case "thursday" : $this->jformat['l'] = "پنجشنبه"; break;
			case "friday" : $this->jformat['l'] = "جمعه"; break;
		}
		
		// L
		$ka = date("L", (time()-31536000)); // previous Gregorian year
		$this->jformat['L'] = ($ka==1) ? 1 : 0;
		
		// m
		$this->jformat['m'] = ($this->jformat['m'] < 10) ? "0".$this->jformat['m'] : $this->jformat['m'];
		
		// M
		$this->jformat['M'] = $this->jformat['F'];
		
		// n
		$this->jformat['n'] = $this->jformat['m'];
		
		// N
		switch ( strtolower($this->format['l']) )
		{
			case "saturday" : $this->jformat['N'] = 1; break;
			case "sunday" : $this->jformat['N'] = 2; break;
			case "monday" : $this->jformat['N'] = 3; break;
			case "tuesday" : $this->jformat['N'] = 4; break;
			case "wednesday" : $this->jformat['N'] = 5; break;
			case "thursday" : $this->jformat['N'] = 6; break;
			case "friday" : $this->jformat['N'] = 7; break;
		}
		
		// o
		$this->jformat['o'] = $this->jformat['Y'];
		
		// w
		$this->jformat['w'] = $this->jformat['N'] - 1;
		
		// t
		$this->jformat['t'] = ($this->jformat['m'] <= 6) ? 31 : 30;
		$this->jformat['t'] = ($this->jformat['m'] == 12) ? ($this->jformat['L'] == 1 ? 30 : 29) : $this->jformat['t'];
		
		// s
		$this->jformat['s'] = $this->format['s'];
		
		// S
		$this->jformat['S'] = "ام";
		
		// e
		$this->jformat['e'] = $this->format['e'];
		
		// I
		$this->jformat['I'] =$this->format['I'];
		
		// u
		$this->jformat['u'] = $this->format['u'];
		
		// U
		$this->jformat['U'] = $this->format['U'];
		
		// y
		$this->jformat['y'] = $this->jformat['Y']%100;
		
		// Z
		$this->jformat['Z'] = $this->format['Z'];
		
		// z
		if($this->jformat['n'] > 6)
			$this->jformat['z'] = 186 + (($this->jformat['n'] - 6 - 1) * 30) + $this->jformat['j'];
		else
			$this->jformat['z'] = (($this->jformat['n'] - 1) * 31) + $this->jformat['j'];
		
		// W
		$this->jformat['W'] = is_integer($this->jformat['z'] / 7) ? ($this->jformat['z'] / 7) : ($this->jformat['z'] / 7 + 1);
		
		// r
		$positive_z = abs(($this->jformat['Z'])/3600);
		if($positive_z > 1)
		{
			$z_hour = ((int) $positive_z < 10 ? "0" : "") . (int) $positive_z;
			$z_minute = (($positive_z - $z_hour) * 60 < 10 ? "0" : "") . ($positive_z - $z_hour) * 60;
		}
		else
		{
			$z_hour = "00";
			$z_minute = (($positive_z) * 60 < 10 ? "0" : "") . ($positive_z) * 60;
		}
		
		// P
		$this->jformat['P'] = ($this->jformat['Z'] >= 0 ? "+" : "-") . "$z_hour:$z_minute";
		
		// O
		$this->jformat['O'] = ($this->jformat['Z'] >= 0 ? "+" : "-") . $z_hour . $z_minute;
		
		// c
		$this->jformat['c'] = $this->jformat['Y'] ."-". $this->jformat['m'] ."-". $this->jformat['d'] ."-". $this->jformat['H'] ." ". $this->jformat['i'] . ":" . $this->jformat['s']. $this->jformat['P'];
		
		// r
		$this->jformat['r'] = $this->jformat['l'] ." ". $this->jformat['j'] ." ". $this->jformat['F'] ." ". $this->jformat['Y'] ." ". $this->jformat['h'] . ":" . $this->jformat['i'] . ":" . $this->jformat['s'] ." ". $this->jformat['O'];		
		
		// T
		$this->jformat['T'] = $this->format['T'];
		
		foreach($formatArr as $key)
			$format = str_replace($key, $this->jformat[$key], $format);
		
		return $format;
	}
	


	
	function ReturnMonthName($monname)
	{
		switch ($monname)
		{
			case 1:	return "فروردين";	break;
			case 2:	return "ارديبهشت";	break;
			case 3:	return "خرداد";	break;
			case 4:	return "تير";	break;
			case 5:	return "مرداد";	break;
			case 6:	return "شهريور";	break;
			case 7:	return "مهر";	break;
			case 8:	return "آبان";	break;
			case 9:	return "آذر";	break;
			case 10:	return "دى";	break;
			case 11:	return "بهمن";	break;
			case 12:	return "اسفند";	break;
		}
	}

	
	function div($a,$b)
	{
		return (int) ($a / $b);
	}
	
	
// Thanks to Roozbeh Pournader and Mohammad Toosi for their Date Conversion program
	function gregorian_to_jalali ($g_y, $g_m, $g_d)
	{
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$gy = $g_y-1600;
		$gm = $g_m-1;
		$gd = $g_d-1;
		$g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);

		for ($i=0; $i < $gm; ++$i)
			$g_day_no += $g_days_in_month[$i];
		
		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
			$g_day_no++; /* leap and after Feb */
		
		$g_day_no += $gd;
		$j_day_no = $g_day_no-79;
		$j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
		$j_day_no = $j_day_no % 12053;
		$jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
		$j_day_no %= 1461;
		
		if($j_day_no >= 366)
		{
			$jy += $this->div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		}
		
		for($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
			$j_day_no -= $j_days_in_month[$i];
		
		$jm = $i+1;
		
		$jd = $j_day_no+1;
		$ddd= $jy.'/'.$jm.'/'.$jd;
		return array($ddd);
	}

	
	function jalali_to_gregorian($j_y, $j_m, $j_d)
	{
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
			$gy += 100*$this->div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
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
		if(strlen($gm)==1){
		$gm='0'.$gm;
		}
		$gd = $g_day_no+1;
		if(strlen($gd)==1){
		$gd='0'.$gd;
		}
		$ddd=$gy.'-'.$gm.'-'.$gd;
		return array($ddd);
	}
}
?>