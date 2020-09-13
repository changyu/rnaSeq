<?php 
$navsub="bcl2fastq";

include './header.php';

//$usercode=md5($current_user->user_login.$current_user->user_email);
//echo "$usercode=$current_user->user_login+$current_user->user_email";
//$user_dirname =  getcwd().'/user_data/'.$usercode;

if ( ! file_exists( $user_dirname ) ) {
    wp_mkdir_p( $user_dirname );
    //echo "user name == ".$user_dirname ;
}

?>

<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>


<main id="main" class="site-main" role="main">
<div id="form-content">

<form id='sqfile' action="bcl2fastq_seq.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<b>Project name:</b> <input type="text" name="proj" id="proj"  style="width:40%" value="Bcl2Fastq.<?php echo date("Y-m-d");?>">
<br><br>
<label for="dataDir">Please paste your data full path here:</label>
<input name="dataDir" type="text" id="dataDir" style="width:95%;display:block;" value="/data/share/test/bcl2fastq/Intensities">

<label for="sampleSht">Please paste your sample sheet with full path here:</label>
<input name="sampleSht" type="text" id="sampleSht" style="width:95%;display:block; "  value="/data/share/test/bcl2fastq/Sample_barcode.tsv">

<label for="misMch">Barcode mismatches option:</label>
<input name="misMch" type="text" id="misMch" style="width:95%;display:block; "  value="1">


 <div style = "width:40%;float:right;" >
<label>No Lane Split:</label><input type="checkbox" id="nolane" name="nolane" >
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" >
<input type="submit" value="Submit" /> </div>
</form>
</div>
<div class='note'><b>FastQC:</b> FastQC is an NGS tool that will generate summaries and visualizations of various aspects of sequencing quality. FASTQC can be run on FASTQ, SAM or BAM files. View the html output for graphical visualizations. 
<br><br><i>References:</i>
<br>1. https://www.bioinformatics.babraham.ac.uk/projects/fastqc/
<br>2. https://www.youtube.com/watch?v=bz93ReOv87Y
</div>
</main><!-- .site-main -->
<?php include './footer.php';?>
