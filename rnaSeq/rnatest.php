<?php 

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
	if($q!="")array_push($bf,$q);
	}else{
	array_push($gf,$q);
	}
    }
    return [$gf,implode(",",$bf)];
}

function chkseq2($sq){
	 $ary = explode(",",$sq);
	 $ary= array_filter($ary);
	 return implode(",", $ary);
}

#####################################
   //echo  "===".$user_dirname;
   $mesg=$steps=$mis=$indx="";
   $okey=0;
   //$cupg=htmlspecialchars($_SERVER['PHP_SELF']);
   $tms=date("YmdHis");
   $color=hex2rgb($_POST['color']);
if(1){
   $okey=1;
   $ntf=isset($_POST['notify']);
   $proj=$_POST['proj'];
   $lbls=chkseq2($_POST['lbl']);
   
   $jnam="rnaseq";
   $sbams=$_POST['sbam'];
   echo "size ==".sizeof($sbams)."<br>";
   print_r($sbams);
  echo " <table>";
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
echo "</table>";

   $sbz=0;
   $seqs=array();
   list($sqgtf,$mis)=chkseq($_POST['gtf']);
   foreach( $sbams as $sb){
	  list($seqs[$sbz],$seqb)=chkseq($sb);
	  $mis.=$seqb;
	  $sbz++;
   } 
   
   $jid=substr($proj, 0, strpos($proj, "."));
   
   #echo "$$$===".$mis."===%%%<br>";
   
   if(strlen($mis)<1){
	if(!file_exists($user_dirname)){ 
                #mkdir($user_dirname, 0775, true);
		symlink($home_www,$user_dirname);
		#exec("sudo ln -s ".$user_dirname." ".$home_linker); 
	}
	if(!file_exists($user_dirname/$proj)){
	       //echo "<h1> Make ".$user_dirname."/".$proj."</h1>";
		mkdir($user_dirname."/".$proj, 0775, true);
	}
	$proj_dirname=$user_dirname."/".$proj;
	$_SESSION["curr_dir"]= $proj_dirname;
        $gtf=$sqgtf[0];
	$ercc=(strpos($gtf, 'ercc') == 0)?"FALSE":"TRUE";

	$fdir="";
   	$cfqs="";
	#echo "size=== ".sizeof($sbams)."!";
	#print_r($seqs);
	#exit();
	
	for($i=0;$i<sizeof($sbams);$i++){
		for($j=0;$j<sizeof($seqs[$i]);$j++){
			$fname=pathinfo($seqs[$i][$j], PATHINFO_FILENAME);
			$fname=substr($fname, 0, strpos($fname, "."));
			$fd=$proj_dirname."/".$fname."_".$tms;
			$cfqs.="cuffquant -p 4 -o $fd $gtf ".$seqs[$i][$j]." & \n";
			$fdir.=$fd."/abundances.cxb,";
			echo "$i:$j===$fd <br>";
      		}
		$fdir=rtrim($fdir,',')." ";
	}

	$output="Results";
        $cfqs=$cfqs."wait\n";
	#echo $cfqs; exit();
	
        $cnm="cuffnorm -p 4 -o $proj_dirname/cuffnorm -L $lbls  $gtf $fdir\n";	
        $rmd="R --no-save $proj_dirname/cuffnorm/genes.fpkm_table $proj_dirname/cuffnorm/$output $jid $lbls $ercc < /usr/local/BradnerPipeline/normalizeRNASeq.R";

#	if (!file_exists("$proj_dirname/cuffnorm/output/")) {
#    		mkdir("$proj_dirname/cuffnorm/output/", 0775, true);
#	}

	$jb = $proj_dirname."/".$jnam.".".$tms.".sh";
        $log=$jnam.$tms.".log";
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

$jn="$jnam\n";
$bch.="#$ -N ".$uid.$jn."\n";
$bch.=". /etc/environment\n";
$bch.=$cfqs.$cnm.$rmd;
$f = fopen($jb, "w") or die("Unable to open file!");

fwrite($f,$bch."\n".implode("\n",$cml));
fclose($f);
 $l=fopen($log,"a");
 fwrite($l,$jn);
 fclose($l);
# exec("qsub $jb",$out);
 print_r($out);
        
	$mesg= "Job ".strtoupper($jnam)." Output Dir: cuffnorm/$output. ";
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}

if($okey && 0){

echo "<script>window.location.href ='./waiting.php?sub=RNASeq&mesg=".urlencode($mesg)."&jnam=".urlencode($jnam)."&log=".urlencode(implode(",",$log))."';</script>";

 	}

 ?>
