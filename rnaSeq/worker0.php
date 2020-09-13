<?php include './header.php';

function docmds($cmdls,$log,$alg,$ntf){
     global $current_user, $user_dirname,$tms;
     
     $jb = $user_dirname."/".$alg.".".$tms.".sh";
     $cml=$cm = array();
     //$jn = $current_user->email.date("YmdHis");
    foreach($cmdls as $cmdl){
     array_push($cml,$cmdl);
     array_push($cm, current(explode(' ',trim($cmdl))));
    }
    
    $bch=<<<EOT
#!/bin/bash
#
#$ -wd $user_dirname
#$ -j y
#$ -S /bin/bash
#$ -o $log
EOT;
if($ntf){
$bch.="
#$ -m abe
#$ -M $current_user->user_email
";
}
$jn=implode(".",$cm);
$bch.="#$ -N ".$jn."\n";
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

function fwt($fn,$m){
    $f = fopen($fn,"a");
    fwrite($f,$m);
    fclose($f);
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
    return array(implode(",",$gf),implode(",",$bf));
}

function mkstg($ar,$n){
    for($i=0;$i<count($ar);++$i){
	$ar[$i]="<div class='".(($n==($i+2))?'active':'nun')."'>".$ar[$i]."</div>";}
    	return "<h4>".implode(" ==> ",$ar)."</h4>";    
}

   //echo  "===".$user_dirname;
   $mesg=$steps=$log=$pid=$mis=$nex="";
   $okey=1;
   $stages=array("alignment","sam to bam", "sort bam","index bam");
   //$cupg=htmlspecialchars($_SERVER['PHP_SELF']);
   $tms=date("YmdHis");
if(isset($_POST['filepath'])){
   $paired=isset($_POST['paired']);
   $ntf=isset($_POST['notify']);
   $alg=$_POST['alg'];
   list($seqs1,$n1)=chkseq($_POST['filepath']);
   list($seqs2,$n2)=$paired?chkseq($_POST['filepath']):array("","");
   $mis=$n1.$n2;
   $jnam="";
   //echo "<br> mis=".strlen($mis)."+$mis===$alg=====$seqs1::::$seqs2<br>";
   
   if(strlen($mis)<1){
	if(!file_exists($user_dirname)){ mkdir($user_dirname, 0777, true); }
       	$fname=pathinfo($seqs1, PATHINFO_FILENAME);
	$fname=substr($fname, 0, strpos($fname, "."));
	$log=$fname."_".$tms.".log";
	$exts=pathinfo($seqs1, PATHINFO_EXTENSION);
	$ffile=$user_dirname."/".$fname;
	$ffile=$ffile."_".$tms;
	$lg=$ffile.".$alg.log";
	$bmfile=$smfile=$pid="";
	$fpid=$ffile.pid;
	if($alg=='bowtie2'){
	    $smfile=$ffile.".bowtie2.sam";
	    $bmfile=$ffile.".bowtie2.bam";
	    //$bcmd="bowtie2 --met-stderr --met 1 -t -p $user_cpu -x $hg19BT2idx -S $smfile ".($paired?" -1 $seqs1 -2 $seqs2":" -U $seqs1");
	     $bcmd="bowtie2 -t -p $user_cpu -x $hg19BT2idx -S $smfile ".($paired?" -1 $seqs1 -2 $seqs2":" -U $seqs1");
	     $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	     $srt="samtools sort -@ $user_cpu  $bmfile ".str_replace("bam","srt",$bmfile);
	     $idx="samtools index ".str_replace("bam","srt.bam",$bmfile)." ".str_replace(".bam",".bai",$bmfile);
      	     $jnam=docmds(array($bcmd,$s2b,$srt,$idx), $lg,$alg,$ntf);
       }elseif($alg=='hisat2'){
       	    $smfile="$ffile.hisat2.sam";
            $bmfile="$ffile.hisat2.bam";
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    //hisat2 -x /path/to/hg19/indices -1 sample_1.fq.gz -2 sample_2.fq.gz | samtools view -Sbo sample.bam 
	    $hisat="hisat2  -t -p $user_cpu -x $hg19HS2idx -S $smfile ".($paired?" -1 $seqs1 -2 $seqs2":" -U $seqs1");
	    $s2b="samtools view -@ $user_cpu -Sb $smfile -o $bmfile";
	    $srt="samtools sort -@ $user_cpu  $bmfile ".str_replace(".bam",".srt",$bmfile);
	    $idx="samtools index ".str_replace(".bam",".srt.bam",$bmfile)." ".str_replace(".bam",".bai",$bmfile);
	    $jnam=docmds(array($hisat,$s2b,$srt,$idx),$lg,$alg,$ntf);
       }elseif ($alg=='star'){
            //$smfile="$ffile.Aligned.out.sam";
            $bmfile=$ffile."Aligned.sortedByCoord.out.bam";
	    //echo "bmfile======$bmfile<br>";
	    $fname=pathinfo($fname, PATHINFO_FILENAME);
	    $rdcmd=($exts=='gz')?"--readFilesCommand  zcat":"";
	    $scmd="STAR --genomeDir $hg19STAidx $rdcmd --runThreadN $user_cpu --readFilesIn $seqs1 ".($paired?$seqs2:"")." --outSAMtype BAM SortedByCoordinate --outFileNamePrefix $ffile";
	    $idx="samtools index ".$bmfile."  ".str_replace(".bam",".star.bai",$bmfile);
      	    $jnam=docmds(array($scmd,$idx),$lg,$alg,$ntf);
       }
	$mesg= "<h4> Runing ".strtoupper($algo)." .....</h4>";
   }else{
	$okey=0;
        $mesg= "<h2>Can not access these files $mis!</h2>";
	$nex=0;
	echo  $mesg;
   }
//$steps =mkstg($stages,$nex);
//echo "<b>WORKFLOW:</b> $steps <div style='float:left;width:26%; margin: 5px 0;'>  $mesg";
}
if($okey){
?>
     <h3><?php echo implode("",$jnam); ?>! <small><i>Do not refresh this page. Otherwise, the job will be submitted twice.</i></small> </h4>
     <div style="margin:20px;float:left;">
     <li> Check <a href='./display.php?f=<?php echo $log; ?>' target='_blank'><?php echo ucfirst($algo); ?> log file</a> 
      <li> Browse <a href='./browse_results.php'  target='_blank' > <?php echo ucfirst($current_user->user_login);?>'s Folder</a>
       </div>
  </div>

<div style="float:right;width:70%; ">
        <h3>Console</h3>
            <div id="status"  style="float:left;width:100%;border:1px solid grey; border-radius:5px;background:#f0f9f9; min-height:100px;padding:20px 10px; "></div>
</div>
<script>
 var mycheck = setInterval(get_status, 1000,<?php echo "'$mesg' , '$jnam'";?> );
 function get_status(msg,jnm){
	    var params='msg='+msg+'&jnm='+jnm;
            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
           xhr.onreadystatechange = function() {
                    try{
                        if (xhr.readyState == 4 && xhr.status == 200){
			var new_response = xhr.responseText;
                        var result = JSON.parse( new_response );
                        document.getElementById("status").innerHTML = result.data + "<br />";
 			/*if(result.key=="done"){
				clearInterval(mycheck);
				 document.forms["wkf"].submit();
			 }elseif(result.key=="bad"){
				clearInterval(mycheck);
				document.getElementById("nxt1").style.visibility='visible';
			 }	*/
                        } 
                    }catch (e){ alert("[XHR STATECHANGE] Exception: " + e); }                     
                };
                xhr.open("POST", "observer2.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(params);
        }

//console.log("test");
 </script>
	
<?php

 	}

include './footer.php'; ?>
