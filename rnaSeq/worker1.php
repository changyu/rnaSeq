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

$jn=implode(".",$cm);
$bch.="#$ -N ".$uid.$jn."\n";
$bch.=". /etc/environment\n";
 $f = fopen($jb, "w") or die("Unable to open file!");
 
 fwrite($f,$bch."\n".implode(" && ",$cml));
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


#####################################
   //echo  "===".$user_dirname;
   $mesg=$steps=$mis=$indx="";
   $okey=1;
   $stages=array("alignment","sam to bam", "sort bam","index bam");
   //$cupg=htmlspecialchars($_SERVER['PHP_SELF']);
   $tms=date("YmdHis");
   $color=hex2rgb($_POST['color']);
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
   $gnm3=(strpos($gnm,"human") !== false)?"HG19":"MM9";#MM9,MM8,HG18,HG19
   $proj=$_POST['proj'];
   list($seqs1,$n1)=chkseq($_POST['filepath']);
   list($seqs2,$n2)=$paired?chkseq($_POST['filepath']):array("","");
   $mis=$n1.$n2;
   $jnam="";
   $pval="1e-9";
   //echo "<br> mis=".strlen($mis)."+$mis===$alg=====$seqs1::::$seqs2<br>";
   $log=array();
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
        
	for($i=0;$i<sizeof($seqs1);$i++){
		$fname=pathinfo($seqs1[$i], PATHINFO_FILENAME);
		$fname=substr($fname, 0, strpos($fname, "."));
		$ffile=$fname."_".$tms;
		$log[$i]=$ffile.".$alg.log";
		$exts=pathinfo($seqs1[$i], PATHINFO_EXTENSION);
		$ffile=$proj_dirname."/".$ffile;
		$lg=$ffile.".$alg.log";
		$bmfile=$smfile="";
		$fpid=$ffile.pid;
		$gzwig=$ffile."_treat_afterfiting_all.wig.gz";
		$wig=$ffile."_treat_afterfiting_all.wig";
		$addcolor="";
		if($color!="0,0,0"){
		$addcolor="gzip -d $gzwig && sed -i '1s/$/ itemRgb=\"On\" color=\"$color\"/' $wig && gzip $wig ";
		}
		
	  if($alg=='bowtie2'){
	    $smfile=$ffile.".bowtie2.sam";
	    $bmfile=$ffile.".bowtie2.bam";
	    $gidx=getgnmdb($gnm,$alg);
	    $gnam=strtoupper(substr($gidx, 0, 4));
	     $bcmd="bowtie2 -t -p $user_cpu -x $gidx -S $smfile ".($paired? " -1 ".$seqs1[$i]." -2 ".$seqs2[$i]:" -U ".$seqs1[$i]);
	     $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	     $srt="samtools sort -@ $user_cpu  $bmfile ".str_replace("bam","sorted",$bmfile);
	     $idx="samtools index ".str_replace("bam","sorted.bam",$bmfile)." ".str_replace(".bam",".bam.bai",$bmfile);
	     $macs="macs14 -t $bmfile -f BAM -g $gnm2 -n $ffile -p $pval -w -S --space=50".($addcolor?"&& $addcolor":"");
	     $jnam=docmds(array($bcmd,$s2b,$srt,$idx,$macs), $lg,$alg,$ntf,$i,$proj);
	     
       }elseif($alg=='hisat2'){
       	    $smfile="$ffile.hisat2.sam";
            $bmfile="$ffile.hisat2.bam";
	    $gidx=getgnmdb($gnm,$alg);
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    $hisat="hisat2  -t -p $user_cpu -x $gidx -S $smfile ".($paired? " -1 ".$seqs1[$i]." -2 ".$seqs2[$i]:" -U ".$seqs1[$i]);
	    $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	    $srt="samtools sort -@ $user_cpu  $bmfile ".str_replace(".bam",".sorted",$bmfile);
	    $idx="samtools index ".str_replace(".bam",".sorted.bam",$bmfile)." ".str_replace(".bam",".hisat2.bam.bai",$bmfile);
	    $macs="macs14 -t $bmfile -f BAM -g $gnm2 -n $ffile -p $pval -w -S --space=50".($addcolor?"&& $addcolor":"");
	    $jnam=docmds(array($hisat,$s2b,$srt,$idx,$macs),$lg,$alg,$ntf,$i,$proj);
       }elseif ($alg=='star'){
            //$smfile="$ffile.Aligned.out.sam";
            $bmfile=$ffile."Aligned.sortedByCoord.out.bam";	   
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    $rdcmd=($exts=='gz')?"--readFilesCommand  zcat":"";
	    $gidx=getgnmdb($gnm,$alg);
	    
	    $scmd="STAR --genomeDir $gidx $rdcmd --runThreadN $user_cpu --readFilesIn ".$seqs1[$i]." ".($paired?$seqs2[$i]:"")." --outSAMtype BAM SortedByCoordinate --outFileNamePrefix $ffile";
	    $idx="samtools index ".$bmfile."  ".str_replace(".bam",".star.bam.bai",$bmfile);
	    $macs="macs14 -t $bmfile -f BAM -g $gnm2 -n $ffile -p $pval -w -S --space=50".($addcolor?"&& $addcolor":"");
	    $rose2="ROSE2_main.py -g $gnm3 -i ".$ffile."_peaks.bed -r $bmfile -t 2500  -s --mask=/data/share/test/wiggles/hg19_encode_blacklist.bed -c /data/share/";
	    $jnam=docmds(array($scmd,$idx,$macs,$rose2),$lg,$alg,$ntf,$i,$proj);
       	    }
	$mesg= "Runing ".strtoupper($alg)." .....";
	}
   }else{
	$okey=0;
        $mesg= "Can not access these files $mis!";
	echo  $mesg;
   }
}
if($okey){

echo "<script>window.location.href ='./waiting.php?mesg=".urlencode($mesg)."&jnam=".urlencode(implode(",",$jnam))."&log=".urlencode(implode(",",$log))."';</script>";

 	}

include './footer.php'; ?>
