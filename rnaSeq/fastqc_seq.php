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
   $color=hex2rgb($_POST['color']);
if(strlen($current_user->user_login)!=5){
		  echo "<h1>Please logout and use your user ID to login!</h1>";
}elseif(!file_exists($home_www)) {
		  echo "<h1>Please create a folder ".$home_www." and make it owned by www-data</h1>";
}else{
   $okey=1;
   $ntf=isset($_POST['notify']);
   $proj=$_POST['proj'];
   list($seqs1,$n1)=chkseq($_POST['fastqc']);
   $mis=$n1;
   $jnam="fastqc";
   #echo "<br> mis=".strlen($mis)."+$mis========$seqs1::$seqs2::$seqs3<br>";
   $log=array();
   $fastqc="fastqc";
   $fastqcmd="";
   if(strlen($mis)<1){
	if(!file_exists($user_dirname)){ 
		symlink($home_www,$user_dirname);
	}
	if(!file_exists($user_dirname/$proj)){
	       //echo "<h1> Make ".$user_dirname."/".$proj."</h1>";
		mkdir($user_dirname."/".$proj, 0775, true);
	}
	$proj_dirname=$user_dirname."/".$proj;
	$_SESSION["curr_dir"]= $proj_dirname;
       # echo sizeof($seqs1)."0000000000";       
	#for($i=0;$i<sizeof($seqs1);$i++){
	#	$fname=pathinfo($seqs1[$i], PATHINFO_FILENAME);
	#	$fname=substr($fname, 0, strpos($fname, "."));
	#	$ffile=$fname."_".$gnm3.$tms;
	#	$log[$i]=$ffile.".$fastqc.log.$i";
	#	$exts=pathinfo($seqs1[$i], PATHINFO_EXTENSION);
	#	#$ffile=$proj_dirname."/".$ffile;
	#	$ffile=$seqs1[$i];
	#	#$fastqcmd.="fastqc $ffile  -o $proj_dirname > $proj_dirname/$log[$i]";	
	 #   $fastqcmd.="fastqc $ffile  -o $proj_dirname > $proj_dirname/$log[$i]";
	  #  $fastqcmd.=($i%2)?" & \n ":" && ";
	   # echo "$fastqcmd=================<br>";
	#}

        $fastqcmd.="fastqc -o $proj_dirname ".implode(" ",$seqs1);
	$mesg= "Runing ".strtoupper($fastqc)." .....";
	$jnam=docmds(array($fastqcmd." \n"),"my$jnam.log",$fastqc ,$ntf,$i,$proj);
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}

if($okey){

echo "<script>window.location.href ='./waiting.php?sub=FastQC&mesg=".urlencode($mesg)."&jnam=".urlencode(implode(",",$jnam))."&log=".urlencode(implode(",",$log))."';</script>";

 	}

include './footer.php'; ?>
