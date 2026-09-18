

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ICUgest</title>
	<link href="https://fonts.googleapis.com/css?family=Libre+Baskerville" rel="stylesheet">
	<!-- <link href="w3.css" rel="stylesheet" >  -->
	<style>
		/*	table td {
			border: 1px solid red;
		} */
		body {
			font-family: arial, sans-serif;
		}
		hr {
			border-top: 1px solid #000000;
			margin-top: 0.2em;
    		margin-bottom: 0.2em;
		}
		.ald {
			text-align:right;
		}
		.alc {
			text-align:center;
		}

		table {
			border-collapse: collapse;
			border-spacing: 0;
			margin: 0 auto;
		}

		/* valign */
		.val { vertical-align: top; }
		.valm { vertical-align: middle; }


		img {
			border: 0px;
			margin: 0px;
		}
		input[type=number] {
   			width:  80px;
   			-moz-appearance: textfield;
		}
		input[type=submit] {
    		width: 80px;
			padding: 0;
			line-height: 39px;
		}


		select {
    		width: 230px;
		}
		.title {
			font-family: 'Libre Baskerville';
			font-size: 110%;
			font-weight: bold;
		}
		.title2 {
			font-size: 105%;
			font-weight: bold;
		}

		.list-align {
			text-align: justify;
			width: 100%;
			display: none;
			position: relative;
			margin: 0;
		}
		.list {
    		width: 320px;
    		/* height: 1061px; */
    		display: inline-block;
    		line-height: 1;
    		position: relative;
		}
		.listesterno {
    		width: 595px;
    		/* height: 1061px; */
    		display: inline-block;
    		line-height: 1;
    		position: relative;
		}
		.bg-image {
    		width: 100%;
    		height: 100%;
		}
		.list-inner {
			padding-left: 20px; /*for background */
			padding-top: 20px; /*for background */
			padding-bottom: 65px; /*for background */
			font-size: 10;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			/* display: inline-block; */
		}
		.list-inneresterno {
			padding-left: 40px; /*for background */
			padding-top: 40px; /*for background */
			padding-bottom: 65px; /*for background */
			font-size: 10;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			margin-top: 105px;
			/* display: inline-block; */
		}
	</style>

</head>
<body>
	<div class="list-align" style="display: block;" >

<?php


		require_once __DIR__ . '/db2.inc.php';


		include('../phpqrcode2/lib/full/qrlib.php');



		$Mysql="SELECT * FROM oggetti";
		$Result=mysqli_query($db, $Mysql);
		while ($res=mysqli_fetch_array($Result)) {


			$text=(string)$res['barcode'];
			$tt=$text;

			$id=$res['IDoggetto'];

			$tipo=$res['fissomobile'];


			$src ='../ICUgest/assets/cart_'.$tipo.'_new.jpg';


			//QRcode::png($text);
			$tempDir =  "/web/htdocs/www.roma-by-night.it/home/ICUgest/tmp/";
			$filename=$tempDir."QR".$tt.".png";

			// QRcode::png($text, $filename, QR_ECLEVEL_H);

			//QRcode::png($text, $filename, QR_ECLEVEL_Q);

			$saveToFile = false;
			$saveToFile=$tempDir."QR".$tt.".svg";
    	$imageWidth = 90; // px
			//$width      = false; // auto calculated
    	$size       = false;
    	$margin     = 1; 
			QRcode::svg($text, 'id-of-svg', $saveToFile, QR_ECLEVEL_Q, $imageWidth, $size, $margin  );

			$fileexternal = "https://www.roma-by-night.it/ICUgest/tmp/QR".$tt.".svg";

?>

			<div class="list" style="border: 1px solid #000;">

				<img src='<?=$src?>' class="bg-image" id="pg1">

				<div class="list-inner" style="padding-top: 10px;">
				<!--  <span style="font-size: 6pt; margin-left: 120px;"> <?=$id?> </span><br> <img src='../tmp/QR<?=$tt?>.png' style="width: 80px;height: 80px;margin-left: 34px;padding-top: 2px;"> -->
				<span style="font-size: 6pt; margin-left: 70px;">Cartellino <?=$id?> </span><br> <img src='<?=$fileexternal?>' style="margin-left: 50px;padding-top: 2px;">

				</div>
			</div>
<?php
			}
?>

	</div>
</body>
