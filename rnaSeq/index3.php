<?php include './header.php';

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
<div id="form-content">

<form id='sqfile' action="worker1.php" method="post" enctype="multipart/form-data" autocomplete="off">
<b>Project name:</b> <input type="text" name="proj" id="proj"  style="width:40%" value="myProj.<?php echo date("Y-m-d");?>"><label><b>Type:</b></label> <select name="ptype">
  <option value="chip">ChIPseq</option>
  <option value="rna">RNAseq</option>
</select>
<b>Color:</b> <input type="color" name="color" value="#00000">
<br><br>
<h4>Please paste your sequence files with full path here:</h4>
<div  style="border:1px solid #CCCCCC; line-height:1.5; text-align:left; color:#333333; font-size:80%; padding:4px 40px; margin: 5px 0;" >
<b>Notes:</b> Use comma <b>","</b> to separate different lanes of the same mate (1st or 2nd). Example: '/path/seqfile1.fastq.gz,/path/seqfile2.fastq.gz'. </div>
<label for="filepath">
</label>
<textarea rows="6" cols="20" name="filepath">/data/share/test/20160723-T1-DMSO-1-MH3241_S1_R1_001.fastq.gz, /data/share/test/20160918_LMS20_input_MH3500_S6_R1_001.fastq.gz,/data/share/test/20160918_LMS20_H3K27Ac_ChIP_MH3500_S5_R1_001.fastq.gz</textarea>
<textarea rows="6" cols="20" name="filepath2"i id="filepath2">/home/cf916/20160417-GISTT1-input-DMSO-1-ML2961_S4_R2_001.fastq.gz</textarea>
<label>Paired:</label><input type="checkbox" id="paired" name="paired" value="paired">
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" value="paired" >
 <select name="alg">
  <option value="bowtie2">Bowtie2</option>
  <option value="hisat2">HISAT2</option>
  <option value="star">STAR</option>
</select> 
<select name="gnm">
  <!--option value="zbfish">Danio rerio</option-->
  <option value="human">Homo sapiens</option>
  <option value="humanER">Homo sapiens ERCC</option>
  <!--option value="mouse">Mus musculus</option>
  <option value="mouseER">Mus musculus ERCC</option>
  <option value="rat">Rattus norvegicus</option>
  <option value="ratER">Rattus norvegicus ERCC</option>
  <option value="fly">Drosophila melanogaster</option-->
</select>
<input type="submit" value="Submit" /> 
</form>
</div>

</main><!-- .site-main -->
<?php include './footer.php';?>
