<?php
	$myname = "Kalev Kask";
	$currenttime = date("d.m.Y H:i:s");
	$timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</php> \n";
	$semesterbegin = new DateTime("2021-1-25");
	$semesterend= new DateTime("2021-06-30");
	$semesterduration = $semesterbegin->diff($semesterend);
	$semesterdurationdays = semesterduration->format("%r%a");
	$semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";
	$today = new DateTime("now");
	$semesterbegin = $semesterbegin->diff($today);
	$fromsemesterbegindays = $fromsemesterbegin->format("%r%a");
	
	if($fromsemesterbegindays <= semesterduration){
		$semesterprgoress ="\n" . '<p>Semester edeneb: <meter min="0" max "' . $semesterdurationdays . '" value="' .$fromsemesterbegindays . '"></meter>.</p>' . "\n";
		//<meter min"0" max"=156" value="35"></meter>
	}else{
		$semesterprgoress = "\n <p> Semester on lõppenud.<\p> \n";
	}
	
	//loeme piltide kataloogi sisu
	$picsdir = "../pics/";
	@allfiles = array_slice(scandir($picsdir), 2);
	//echo   $allfiles[5];
	//var_dump($allfiles);
	$allowedphototypes = ["image/jpeg", "image/png"];
	$picfiles = [];
	
	foreach($allfiles as $file){
		$fileinfo = getimagesize($picdir .$file);
		if(isset($fileinfo["mime"])){
			if(in_array($fileinfo["mime"], $allowedphototypes)){
				array_push($picfiles, $file);
			}			
		}
	}	
	
	$photocount = count($picfiles);
	$photonum = rand(0, photocount-1);
	randomphoto = $picfiles[$photonum];
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>
	<?php
		echo $myname;
	?>
	</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<?php
		echo $timehtml;
		echo $semesterdurhtml;
	?>
	<img src="<?php echo $picdir . $randomphoto; ?>" alt="vaade Haapsalus">
</body>
</html>
