<?php
	$myname = "Kalev Kask";
	$currenttime = date("d.m.Y H:i:s");
	$timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</p> \n";
	$semesterbegin = new DateTime("2021-1-25");
	$semesterend = new DateTime("2021-6-30");
	$semesterduration = $semesterbegin->diff($semesterend);
	$semesterdurationdays = $semesterduration->format("%r%a");
	$semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";
	$today = new DateTime("now");
	$fromsemesterbegin = $semesterbegin->diff($today);
	$fromsemesterbegindays = $fromsemesterbegin->format("%r%a");
	
	if ($today < $semesterbegin){
		$semesterprogress = "\n <p>Semester pole veel alanud.</p> \n";
	} elseif ($today > $semesterend) {
		$semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
	} else {
		$semesterprogress = "\n"  .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'"></meter>.</p>' ."\n";
	}
		
	
	//loeme piltide kataloogi sisu
	$picsdir = "/home/kalev.kask/public_html/vr2021/vr/pics/";
	$allfiles = array_slice(scandir($picsdir), 2);
	//echo $allfiles[5];
	//var_dump($allfiles);
	$allowedphototypes = ["image/jpeg", "image/png"];
	$picfiles = [];
	
	//for($x = 0; $x <10;$++){
		//tegevus
	//}
	foreach($allfiles as $file){
		$fileinfo = getimagesize($picsdir .$file);
		//var_dump($fileinfo);
		if(isset($fileinfo["mime"])){
			if(in_array($fileinfo["mime"], $allowedphototypes)){
				array_push($picfiles, $file);
			}
		}
	}
	
	$photocount = count($picfiles);
	$photonum = mt_rand(0, $photocount-1);
	$randomphoto = $picfiles[$photonum];
	
	$treepicarray = [];
	$i = 1;
	while ($i <= 3) {
		$photonum2 = mt_rand(0, $photocount-1);
		$randomphoto2 = $picfiles[$photonum2];
		if (!in_array($randomphoto2, $treepicarray)) {
			array_push($treepicarray, $randomphoto2);
			$i++;
		}
	}
	
	$weekdays = array( "Pühapäev", "Esmaspäev", "Teisipäev", "Kolmapäev", "Neljapäev", "Reede", "Laupäev",);
	$dayEST = $weekdays[date_format($today, 'w')];
	
	

	
	
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
		echo $timehtml. " Täna on ".$dayEST;
		echo $semesterdurhtml;
		echo $semesterprogress;
	?>
	<p><b>Juhuslik pilt kogu haapsalu pildivaramust</b></p>
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$randomphoto \n"; ?>" alt="vaade Haapsalus">
	<!--https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/IMG_0177.JPG-->
	<br>
	<p><b>3 juhusliku pilti kogu haapsalu pildivaramust</b></p>
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[0] \n"; ?>" alt="juhuslik pilt 1">
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[1] \n"; ?>" alt="juhuslik pilt 2">
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[2] \n"; ?>" alt="juhuslik pilt 3">
	
</body>
</html>
