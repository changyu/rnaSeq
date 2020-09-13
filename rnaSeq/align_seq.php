<?php include './header.php';

function docmds($cmdls,$log,$alg,$ntf,$i,$proj){
     global $current_user, $uid, $user_dirname,$tms;
     $proj_dirname= $user_dirname."/".$proj;
     $jb = $proj_dirname."/".$alg.".".$tms.$i.".sh";
     $cml=$cm = array();
     //$jn = $current_user->email.date("YmdHis");
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
#$ -M $current_user->user_email
";
}

$jn=implode(".",$cm);
$bch.="#$ -N ".$uid.$proj."-".$cm[0]."\n";
$bch.=". /etc/environment\n";
 $f = fopen($jb, "w") or die("Unable to open file!");
 
 fwrite($f,$bch."\n".implode(" \n ",$cml));
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
   global $mm9BT2idx,$mm9BT2idxER,$mm9HS2idx,$mm9HS2idxER,$mm9STAidx,$mm9STAidxER;
   global $mm10BT2idx,$mm10STAidx,$mm10HS2idx;
   if($alg=='bowtie2'){ 
 	switch($gnom){
   	       case "human":$indx=$hg19BT2idx;break;
	       case "humanER":$indx=$hg19BT2idxER;break;
	       case "mouse":$indx=$mm9BT2idx;break;
	       case "mouseER":$indx=$mm9BT2idxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19BT2idx;break;
		}
	}elseif($alg=='hisat2'){
	 switch($gnom){
   	       case "human":$indx=$hg19HS2idx;break;
	       case "humanER":$indx=$hg19HS2idxER;break;
	       case "mouse":$indx=$mm9HS2idx;break;
	       case "mouseER":$indx=$mm9HS2idxER;break;
		  // case "rat":$indx=$hg19HS2idx;break;
		  // case "ratER":$indx=$hg19HS2idxER;break;
   		default:$indx=$hg19HS2idx;break;
		}

	}elseif($alg=='star'){
	switch($gnom){
   	       case "human":$indx=$hg19STAidx;break;
	       case "humanER":$indx=$hg19STAidxER;break;
	       case "mouse":$indx=$mm9STAidx;break;
	       case "mouseER":$indx=$mm9STAidxER;break;
		  // case "rat":$indx=$hg19BT2idx;break;
		  // case "ratER":$indx=$hg19BT2idxER;break;
   		default:$indx=$hg19STAidx;break;
		}

	
	}
#	echo "<br>$alg--->$indx<br>";
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




   //echo  "===".$user_dirname;
   $mesg=$steps=$mis=$indx="";
   $okey=1;
   $stages=array("alignment","sam to bam", "sort bam","index bam");
   //$cupg=htmlspecialchars($_SERVER['PHP_SELF']);
   $tms=date("YmdHis");

if(strlen($current_user->user_login)!=5){
		  echo "<h1>Please logout and use your user ID to login!</h1>";
}elseif(!file_exists($home_www)) {
		  echo "<h1>Please create a folder ".$home_www." and make it owned by www-data</h1>";
		  
}elseif(isset($_POST['filepath'])){
   $paired=isset($_POST['paired']);
   $ntf=isset($_POST['notify']);
   $alg=$_POST['alg'];
   $gnm=$_POST['gnm'];
   $gnm2=(strpos($gnm,"human") !== false)?"hs":"mm";
   $proj=$_POST['proj'];
   list($seqs1,$n1)=chkseq($_POST['filepath']);
   list($seqs2,$n2)=$paired?chkseq($_POST['filepath2']):array("","");
   $mis=$n1.$n2;
   $jnam="";
   $pval="1e-9";
   //echo "<br> mis=".strlen($mis)."+$mis===$alg=====$seqs1::::$seqs2<br>";
   $log=array();
   $cmda=array();
   $lg=$proj.".$alg.log";
   if(strlen($mis)<1){
	if(!file_exists($user_dirname)){ 
                #mkdir($user_dirname, 0775, true);
		symlink($home_www,$user_dirname);
		//echo "<br> $home_www ============ $user_dirname<br>";
		#exec("sudo ln -s ".$user_dirname." ".$home_linker); 
	}
	if(!file_exists($user_dirname/$proj)){
	       //echo "<h1> Make ".$user_dirname."/".$proj."</h1>";
		mkdir($user_dirname."/".$proj, 0775, true);
	}
	
	$proj_dirname=$user_dirname."/".$proj;
	$_SESSION["curr_dir"]= $proj_dirname;
	#$clean="find . -type f ! -name '*.sorted.*' -delete";
	//echo "<br>size ==".sizeof($seqs1)."<br>";
	for($i=0;$i<sizeof($seqs1);$i++){
	//echo "<br>==$i==<br>";
		$fname=pathinfo($seqs1[$i], PATHINFO_FILENAME);
		$fname=substr($fname, 0, strpos($fname, "."));
		$ffile=$fname.$tms.'-'.$i;
		$log[$i]=$ffile.".$alg.log";
		$exts=pathinfo($seqs1[$i], PATHINFO_EXTENSION);
		$ffile=$proj_dirname."/".$ffile;
		#$lg=$ffile.".$alg.log";
		$bmfile=$smfile="";
	  if($alg=='bowtie2'){
	    $smfile=$ffile.".bowtie2.sam";
	    $bmfile=$ffile.".bowtie2.bam";
	    $gidx=getgnmdb($gnm,$alg);
	    
	     $bcmd="bowtie2 -t -p $user_cpu -k 1 -x $gidx -S $smfile ".($paired? " -1 ".$seqs1[$i]." -2 ".$seqs2[$i]:" -U ".$seqs1[$i]);
	     $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	     $srt="samtools sort -@ $user_cpu -m $user_mem $bmfile -o ".str_replace("bam","sorted.bam",$bmfile);
	     $idx="samtools index ".str_replace("bam","sorted.bam",$bmfile)." ".str_replace(".bam",".sorted.bam.bai",$bmfile);
	     $clean="mkdir $ffile && mv ".$ffile.".* $ffile && find $ffile -type f ! -name  '*.sorted.*'  ! -name '*.log' -delete";
	     #$jnam=docmds(array($bcmd,$s2b,$srt,$idx,$clean), $lg,$alg,$ntf,$i,$proj);
	     $cmda=array_merge($cmda,array($bcmd,$s2b,$srt,$idx,$clean));
       }elseif($alg=='hisat2'){
       	    $smfile="$ffile.hisat2.sam";
            $bmfile="$ffile.hisat2.bam";
	    $gidx=getgnmdb($gnm,$alg);
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    $hisat="hisat2  -t -p $user_cpu -x $gidx -S $smfile ".($paired? " -1 ".$seqs1[$i]." -2 ".$seqs2[$i]:" -U ".$seqs1[$i]);
	    $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	    $srt="samtools sort -@ $user_cpu  -m $user_mem  $bmfile -o ".str_replace(".bam",".sorted.bam",$bmfile);
	    $idx="samtools index ".str_replace(".bam",".sorted.bam",$bmfile)." ".str_replace(".bam",".sorted.bam.bai",$bmfile);
	    $clean="mkdir $ffile && mv ".$ffile.".* $ffile && find $ffile -type f ! -name  '*.sorted.*' ! -name '*.log' -delete";
	    #$jnam=docmds(array($hisat,$s2b,$srt,$idx,$clean),$lg,$alg,$ntf,$i,$proj);
	    $cmda=array_merge($cmda,array($hisat,$s2b,$srt,$idx,$clean));
       }elseif ($alg=='star'){
            //$smfile="$ffile.Aligned.out.sam";
            $bmfile=$ffile."Aligned.sortedByCoord.out.bam";
	   
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    $rdcmd=($exts=='gz')?"--readFilesCommand  zcat":"";
	    $gidx=getgnmdb($gnm,$alg);
	    $scmd="STAR --genomeDir $gidx $rdcmd --runThreadN $user_cpu --readFilesIn ".$seqs1[$i]." ".($paired?$seqs2[$i]:"")." --outSAMtype BAM SortedByCoordinate --outFileNamePrefix $ffile";
	    $idx="samtools index ".$bmfile."  ".str_replace(".bam",".sorted.bam.bai",$bmfile);
	    #$clean="mkdir $ffile && mv ".$ffile.".* $ffile && mv $ffile/".$ffile."*.sorted.*  .";
	    #$clean="mkdir $ffile && mv ".$ffile."* $ffile && find $ffile -type f ! -name  '*.sorted*'  ! -name '*.log' -delete";
	     $clean="mkdir $ffile && mv ".$ffile."* $ffile && find $ffile -type f ! -name  '*.sorted*'  ! -name '*.log' -delete";
	    #$jnam=docmds(array($scmd,$idx,$clean),$lg,$alg,$ntf,$i,$proj);
	    $cmda=array_merge($cmda,array($scmd,$idx,$clean));
       	    }
	}
	$jnam=docmds($cmda,$lg,$alg,$ntf,$i,$proj); 
	$mesg= "Runing ".strtoupper($alg)." .....";
	$log=array($lg);
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}
#print_r($cmda);
if($okey){
	echo "<script>window.location.href ='./waiting.php?mesg=".urlencode($mesg)."&jnam=".urlencode(implode(",",$jnam))."&log=".urlencode(implode(",",$log))."';</script>";
}

include './footer.php'; ?>
