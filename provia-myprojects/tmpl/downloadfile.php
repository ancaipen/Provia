<?php

if(!isset($_REQUEST['f']))
{
	return;
}

if(trim($_REQUEST['f']) == "")
{
	return;
}

$filepath = $_REQUEST['f'];
$filepath = str_replace('https://provia.proviaserver-v2.com','',$filepath);
$filepath = str_replace('https://www.provia.com','',$filepath);
$filenames = explode('/', $filepath);
$filenames_length = count($filenames);

if($filenames_length > 0)
{
	$filename = $filenames[$filenames_length - 1];
	$full_filepath = '/home/proviav2/public_html/provia.com'.$filepath;

	//echo $filename;

	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	readfile($full_filepath); 
	exit;
}

?>