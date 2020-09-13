<?php
include './header.php';

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
// echo $jb;
// print_r($out);
 return $out;
}

function getgnmdb($gnom,$alg){
   global $hg19BT2idx,$hg19BT2idxER,$hg19STAidx,$hg19STAidxER,$hg19HS2idx,$hg19HS2idxER;
   global $mm10BT2idx,$mm10STAidx,$mm10HS2idx;
   if($alg=='bowtie2'){ 
 	switch($gnom){
   	       case "human":$indx=$hg19BT2idx;break;
	       case "humanER":$indx=$hg19BT2idxER;break;
	       case "mouse":$indx=$mm10BT2idx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19BT2idx;break;
		}
	}elseif($alg=='hisat2'){
	 switch($gnom){
   	       case "human":$indx=$hg19HS2idx;break;
	       case "humanER":$indx=$hg19HS2idxER;break;
	       case "mouse":$indx=$mm10HS2idx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19HS2idx;break;
		}

	}elseif($alg=='star'){
	switch($gnom){
   	       case "human":$indx=$hg19STAidx;break;
	       case "humanER":$indx=$hg19STAidxER;break;
	       case "mouse":$indx=$mm10STAidx;break;
		  // case "mouseER":$indx=$hg19BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19STAidx;break;
		}
	
	}
//	echo "<br>$alg--->$indx<br>";
	return $indx;
}

function chkseq($qs,$pj){
    global $user_dirname;
    $gf=$bf=array();
    $qs=preg_replace('/\s+/', '',$qs);
    $fs=explode(",",$qs);
    foreach($fs as $q){
    	if(!file_exists($q)){
	  if(file_exists($user_dirname."/".$q)){
	  array_push($gf,$user_dirname."/".$q);
	  }elseif(file_exists($pj."/".$q)){
	  array_push($gf,$pj."/".$q);
	  }elseif($q!=""){array_push($bf,$q);}
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
   //$cupg=htmlspecialchars($_SERVER['PHP_SELF']);
   $tms=date("YmdHis");
   $color=hex2rgb($_POST['color']);
if(strlen($current_user->user_login)!=5){
	  echo "<h1>Please logout and use your user ID to login!</h1>";
}elseif(!file_exists($home_www)) {
	  echo "<h1>Please create a folder ".$home_www." and make it owned by www-data</h1>";
}elseif(isset($_POST['sortbam'])){
   
   $macsv=$_POST["macs"];
   $pval=$_POST["pvalue"];
   #$ctrl=$_POST["ctrl"];
   $ctr=isset($_POST['control']);
   $ntf=isset($_POST['notify']);
   $gnm=$_POST['gnm'];
   $gnm2=(strpos($gnm,"human") !== false)?"hs":"mm";
   $gnm3=(strpos($gnm,"human") !== false)?"HG19":"MM9";#MM9,MM8,HG18,HG19
   $proj=$_POST['proj'];
   $proj_dirname=$user_dirname."/".$proj;
   list($seqs1,$n1)=chkseq($_POST['sortbam']);
   list($seqs2,$n2)=chkseq($_POST['ctrfiles']);
   $mis=$n1.$n2;
   $jnam="";
   echo "$$$$$$$$$$$$$$$ $n2======".$n1."<br>";
   $log=array();
   $log2=array();
   if(sizeof($seqs1)!=sizeof($seqs2)){
     echo "The number of input files is not match! ".sizeof($seqs1)." vs ".sizeof($seq12);
   }elseif(strlen($mis)<1){
     $okey=1;
	if(!file_exists($user_dirname)){ 
                #mkdir($user_dirname, 0775, true);
		symlink($home_www,$user_dirname);
		#exec("sudo ln -s ".$user_dirname." ".$home_linker); 
	}
	if(!file_exists($user_dirname/$proj)){
	       //echo "<h1> Make ".$user_dirname."/".$proj."</h1>";
		mkdir($user_dirname."/".$proj, 0775, true);
	}
 	$ntf=isset($_POST['notify']);
	$_SESSION["curr_dir"]= $proj_dirname;
        #echo sizeof($seqs1)."0000000000";
	
	for($i=0;$i<sizeof($seqs1);$i++){
		$fname=pathinfo($seqs1[$i], PATHINFO_FILENAME);
		$fname=substr($fname, 0, strpos($fname, "."));
		$ffile=$fname."_".$gnm3.$tms;
		$log[$i]=$ffile.".$macsv.log.$i";
		$exts=pathinfo($seqs1[$i], PATHINFO_EXTENSION);
		$ffile=$proj_dirname."/".$ffile;

		$addcolor=($color!="0,0,0")?"gzip -d $gzwig && sed -i '1s/$/ itemRgb=\"On\" color=\"$color\"/' $wig && gzip $wig ":"";
	    
	   # $macs="$macsv -t $seqs1[$i] -f BAM -g $gnm2 -n $ffile -c ".$seqs2[$i]." -p $pval -w -S --space=50".($addcolor?"&& $addcolor":""); 
	   if($macsv=="macs14"){
	   $macs="$macsv -t $seqs1[$i] ".(($ctr)?"-c ".$seqs2[$i]:"")." -f BAM -g $gnm2 -n $ffile -p $pval -w -S --space=50".($addcolor?"&& $addcolor":"");
	   }else{
	   $macs="$macsv callpeak -t $seqs1[$i] ".(($ctr)?"-c ".$seqs2[$i]:"")." -f BAM -g $gnm2 -n $ffile -p $pval --broad --broad-cutoff 0.01 --nomodel";
	   }

	  # echo "$macs======".$macsv;
	    $jnam=docmds(array($macs), $log[$i], $macsv, $ntf,$i,$proj);
	    # echo "submit=============";
     

	$mesg= "Runing ".strtoupper($macsv)." .....";
	}
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}

if($okey){

echo "<script>window.location.href ='./waiting.php?sub=MACS&mesg=".urlencode($mesg)."&jnam=".urlencode(implode(",",$jnam))."&log=".urlencode(implode(",",$log))."';</script>";

 	}

include './footer.php'; ?>
