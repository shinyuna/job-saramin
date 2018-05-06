<?php
	if(isset($_GET['job_val'])){
		$job_val =  $_GET['job_val'];
	}else{
		echo "val 안받아짐";
		return;
	}
	if(isset($_GET['page'])){
		$page =  $_GET['page'];
	}else{
		echo "page 안받아짐";
		return;
	}
	$url = 'http://api.saramin.co.kr/job-search?keywords='.$job_val.'&start='.$page.'&count=10';
	$ch = cURL_init();

	cURL_setopt($ch, CURLOPT_URL, $url);
	cURL_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$response = cURL_exec($ch);
	cURL_close($ch); 

	$object = simplexml_load_string($response);

	$category = 'job-category';
	$experience = 'experience-level';
	$education = 'required-education-level';
	$jtype = 'job-type';
	$count = 0;
	$total = $object->jobs['total'];
	$temp[0] = '';$temp[1] = '';$temp[2] = '';$temp[3] = '';$temp[4] = '';$temp[5] = '';$temp[6] = '';$temp[7] = '';$temp[8] = '';$temp[9] = '';

	foreach($object->jobs->job as $data){
		$lo = $data->position->location;
		$lo = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}][a-zA-Z][a-zA-Z](;)+/i", "", $lo);
        $lo = explode(",",$lo);
		$lo = $lo[0];
        $lo = explode("전체",$lo);
        $lo = $lo[0];
        $company = explode("본점",$data->company->name);
        $company = $company[0];
		$temp[$count]= array("cnt"=>"".$count."","location"=>"".$lo."","url"=>"".$data->url."","total" => "".$total."","company"=> "".$company."","title"=>"". $data->position->title."","experience"=>"".$data->position->$experience."","education"=>"".$data->position->$education."","jtype"=>"".$data->position->$jtype."","category"=>"".$data->position->$category."");
		$count++;
	}

// var_dump($temp[3]);
	for ($i=0; $i < 10; $i++) { 
		if($temp[$i]==''){
			$temp[$i]=array("error"=>"0");
		}
	}

	$data = array($temp[0],$temp[1],$temp[2],$temp[3],$temp[4],$temp[5],$temp[6],$temp[7],$temp[8],$temp[9]);
	$data = json_encode($data);
	echo $data;

	// $total_page = $object->jobs['total'];
	

	// $data = json_encode($object);
	// echo $data;


