<?php include './header.php';

function hex2rgb($hex) {
   #$hex = str_replace("#", "", $hex);
   $hex=ltrim($hex, "#");
   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   return "$r,$g,$b";
}

function docmds($cmdls,$log,$alg,$ntf,$i,$proj){
     global $umail,$uid, $user_dirname,$tms;
     $proj_dirname= $user_dirname."/".$proj;
     $jb = $proj_dirname."/".$alg.".".$tms.$i.".sh";
     $cml=$cm = array();
     //$jn = $umail.date("YmdHis");
    foreach($cmdls as $cmdl){
     
     array_push($cml,$cmdl);
     array_push($cm, current(explode(' ',trim($cmdl))));
    }
    
    $bch=<<<EOT
#!/bin/bash
#
#$ -wd $proj_dirname
#$ -j y
#$ -S /bin/bash
#$ -o $log \n
EOT;
if($ntf){
$bch.="
#$ -m abe
#$ -M $umail
";
}

$jn=implode(".",$cm)."\n";
$bch.="#$ -N ".$uid.$jn."\n";
$bch.=". /etc/environment\n";
 $f = fopen($jb, "w") or die("Unable to open file!");
 
 fwrite($f,$bch."\n".implode("\n",$cml));
 fclose($f);
 $l=fopen($log,"a");
 fwrite($l,$jn);
 fclose($l);
 exec("qsub $jb",$out);
 echo $jb;
 print_r($out);
 return $out;
}

function getgnmdb($gnom,$alg){
   global $hg19BT2idx,$hg19BT2idxER,$hg19STAidx,$hg19STAidxER,$hg19HS2idx,$hg19HS2idxER;
   if($alg=='bowtie2'){ 
 	switch($gnom){
   	       case "human":$indx=$hg19BT2idx;break;
	       case "humanER":$indx=$hg19BT2idxER;break;
		  // case "mouse":$indx=$hg19BT2idx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19BT2idx;break;
		}
	}elseif($alg=='hisat2'){
	 switch($gnom){
   	       case "human":$indx=$hg19HS2idx;break;
	       case "humanER":$indx=$hg19HS2idxER;break;
		  // case "mouse":$indx=$hg19BT2idx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19HS2idx;break;
		}

	}elseif($alg=='star'){
	switch($gnom){
   	       case "human":$indx=$hg19STAidx;break;
	       case "humanER":$indx=$hg19STAidxER;break;
		  // case "mouse":$indx=$hg19BT2idx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19STAidx;break;
		}
	
	}
//	echo "<br>$alg--->$indx<br>";
	return $indx;
}

function chkseq($qs){
    $gf=$bf=array();
    $qs=preg_replace('/\s+/', '',$qs);
    $fs=explode(",",$qs);
    foreach($fs as $q){
    	if(!file_exists($q)){
		if($q!=""){
			array_push($bf,$q);
			#echo "@@@@@@@@@@@@@@@@@ $q @";
		}
	}else{
	array_push($gf,$q);
	}
    }
    return [$gf,implode(",",$bf)];
}


#####################################
   //echo  "===".$user_dirname;
   $mesg=$steps=$mis=$indx="";
   $okey=0;
   $tms=date("YmdHis");
   $dataDir=$_POST['dataDir'];
   $sampleSht=$_POST['sampleSht'];
if(strlen($current_user->user_login)!=5){
		  echo "<h1>Please logout and use your user ID to login!</h1>";
}elseif(!file_exists($home_www)) {
		  echo "<h1>Please create a folder ".$home_www." and make it owned by www-data.</h1>";
}elseif(!file_exists($dataDir)) {
		  echo "<h1>Not found '".$dataDir."'. Please check it.</h1>";
}elseif(!file_exists($sampleSht)) {
		  echo "<h1>Not found '".$sampleSht."'. Please check it.</h1>";
}else{
   $okey=1;
   $ntf=isset($_POST['notify']);
   $nln=isset($_POST['nolane']);
   $proj=$_POST['proj'];
   $misMch=$_POST['misMch'];
   $jnam="bcl2fastq";
   $log=array();
   $bcl2fastq="bcl2fastq";
   $bcl2fastqmd="";
   if(1){
	if(!file_exists($user_dirname)){ 
		symlink($home_www,$user_dirname);
	}
	if(!file_exists($user_dirname/$proj)){
	       //echo "<h1> Make ".$user_dirname."/".$proj."</h1>";
		mkdir($user_dirname."/".$proj, 0775, true);
	}
	$proj_dirname=$user_dirname."/".$proj;
	$_SESSION["curr_dir"]= $proj_dirname;
        $bcl2fastqmd.="bcl2fastq ".($nln?"--no-lane-splitting":"")." -R $dataDir -o $proj_dirname --sample-sheet $sampleSht --barcode-mismatches $misMch";
	$mesg= "Runing ".strtoupper($bcl2fastq)." .....";
	$jnam=docmds(array($bcl2fastqmd." \n"),"my$jnam.log",$bcl2fastq ,$ntf,0,$proj);
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}

if($okey){
echo "<script>window.location.href ='./waiting.php?sub=bcl2fastq&mesg=".urlencode($mesg)."&jnam=".urlencode(implode(",",$jnam))."&log=".urlencode(implode(",",$log))."';</script>";
 	}

include './footer.php'; ?>
