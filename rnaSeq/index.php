<?php 
$navsub="About";
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

<script>
$('#sqfile').submit(function(e){
  
    e.preventDefault(); // Prevent Default Submission
  
    $.ajax({
 url: 'aligns.php',
 type: 'POST',
 data: $(this).serialize(), // it will serialize the form data
        dataType: 'html'
    })
    .done(function(data){
     $('#form-content').fadeOut('slow', function(){
          $('#form-content').fadeIn('slow').html(data);
        });
    })
    .fail(function(){
 alert('Ajax Submit Failed ...'); 
    });
});
$(function(){
    $('#paired').change(function () {
        $("#filepath2").toggle(this.checked);
    }).change();
});

</script>
<main id="main" class="site-main" role="main">
<h1>Tool for ChIP-seq & RNA-seq</h1>
<div id="box2"><div class='abt' style="color:#014;font-size:1.2em;line-height:120%;background-color:#fcfcfb;padding:40px;"><span>A</span>bout: Research Computing at DFCI is pleased to offer this web-based tool for basic analysis of next generation sequencing (NGS). Through this tool you can assess NGS data quality, align NGS data to the genome and perform basic analysis pipelines for ChIP-seq and RNA-seq data. The goal for this project is to widen the bioinformatic bottleneck and make NGS analysis available through a website interface, requiring minimal to no knowledge of programming or the command line.<br><br>

<b>Steps to get started:</b> 
<ol>
<li> If you never access this server before, do ssh login first to the server rcwebapps1.dfci.harvard.edu by your partners credentials. Otherwise go next step.

<li> Connect via smb to have a windows interface: <br>
On a mac, from the main Finder menu select “Go” and “Connect to Server”. Use this address: <br>
smb://rcwebapps1.dfci.harvard.edu/userid <br>

Use your Partners or laboratory login as username and password.<br> 

<li> Run The Pipeline: <br>
Create a folder using the smb window and drag your files for analysis into it.<br>
Go to the website, pick your analysis and give the path to your data (for example, /data/home/username/folder/file.fastq).<br> 

<li> Output: <br>
Output files are populated in your ‘www-data’ folder. You can continue running the pipeline from the www-data folder, or copy the output files to another location for analysis.
</ol>
</div>
 <div class='abt'><span>B</span>rowse: 
<?php include './browse_txt.php'; ?>
</div>
<div class='abt'><span>S</span>tatus:
<?php include './status_txt.php'; ?> 
</div>
<div class='abt'><span>F</span>astQC: 
<?php include './fastqc_txt.php'; ?>
</div>
 <div class='abt'><span>A</span>lign: <?php include './align_txt.php'; ?>
</div>
 <div class='abt'><span>M</span>ACS: <?php include './macs_txt.php'; ?>
</div>
 <div class='abt'><span>R</span>OSE: <?php include './rose_txt.php'; ?>
</div>
 <div class='abt'><span>R</span>NA-Seq: <?php include './rnaseq_txt.php'; ?>
 </div>
 <!--div class='abt'><span></span></div>
 <div class='abt'><span></span></div-->
<br><br>
</div> 
</main><!-- .site-main -->
<?php include './footer.php';?>
