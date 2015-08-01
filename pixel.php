<?php

/*

Functions -

Get the following information : 
- country
- referrer
- date
- page 1 or 2

Check if the record for the referrer exists on same date
If yes, update
If no, create

*/


include("connection.php");

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('PST');


function checkifexist($ref,$date)
{

	global $db;
	$db->query("SELECT * FROM data WHERE ref='$ref' AND date='$date'");
	if($db->count()==0){
		return false;
	}
	else{
		return $db->result_array();
	}
}

function ccupdate($data,$cc)
{
	$dd=json_decode($data);
	if($dd->$cc>0){
		$dd->$cc++;
	}
	else
	{
		$dd->$cc=1;
	}

	return json_encode($dd);
}

function updater($data,$ref,$date,$cc,$page)
{

//var_dump($data);

	global $db;

	if($page==1)
	{

if($data[0]['t1']==""){
$data[0]['t1']=1;
$arr[$cc] = 0;
$cc = ccupdate(json_encode($arr),$cc);
}
else {
$data[0]['t1']++;
$cc = ccupdate($data[0]['geo1'],$cc);
}

$t11=$data[0]['t1'];
$cc = mysql_real_escape_string($cc);
$sno = $data[0]['sno'];

		$db->query("UPDATE data SET t1='$t11', geo1='$cc' WHERE sno='$sno'");

	}
	
	if($page==2)
	{

if($data[0]['t2']==""){
$data[0]['t2']=1;
$arr[$cc] = 0;
$cc = ccupdate(json_encode($arr),$cc);
}
else {
$data[0]['t2']++;
$cc = ccupdate($data[0]['geo2'],$cc);
}

$t11=$data[0]['t2'];
$cc = mysql_real_escape_string($cc);
$sno = $data[0]['sno'];

		$db->query("UPDATE data SET t2='$t11', geo2='$cc' WHERE sno='$sno'");

	}

}

function creater($ref,$date,$cc,$page)
{
	global $db;

$arr[$cc] = 0;


$cc = ccupdate(json_encode($arr),$cc);


	if($page==1)
	{
	$db->query("INSERT into data(ref,date,geo1,t1) values('$ref','$date','$cc','1')");
	}
	else
	{
	$db->query("INSERT into data(ref,date,geo2,t2) values('$ref','$date','$cc','1')");
	}
	
	
}



if(isset($_GET['r'])){

// fetching all the required parameters

if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])){
$country_code = $_SERVER["HTTP_CF_IPCOUNTRY"]; // to access in PHP
}
else{
	$country_code ="UNWN";
}
//$country_code = "CA"; // to access in PHP

$referrer = $_GET['r'];
$date = date("Ymd");
$page = '1';

$data=checkifexist($referrer,$date);

if($data!=false)
{
	updater($data,$referrer,$date,$country_code,$page);
}
else
{
	creater($referrer,$date,$country_code,$page);
}

}


  // Create an image, 1x1 pixel in size
  $im=imagecreate(1,1);

  // Set the background colour
  $white=imagecolorallocate($im,255,255,255);

  // Allocate the background colour
  imagesetpixel($im,1,1,$white);

  // Set the image type
  header("content-type:image/jpg");

  // Create a JPEG file from the image
  imagejpeg($im);

  // Free memory associated with the image
  imagedestroy($im);

?>
