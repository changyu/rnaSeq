<?php 
$navsub="RNASeq";

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
<script type="text/javascript">
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_groups"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
   
    var x = 2; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
	    var newa='<div><label for="sbam".x>Sorted Bam File Group '+(x)+'</label><a href=javascript:void(0);  title="Remove field" class="remove_field" style="display: inline;color:#900">REMOVE</a><textarea rows="2" cols="20" name="sbam[]"></textarea></div>';
            $(wrapper).append($(newa)); //add input box
        }
    });
   
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove();  x--; 
    })
});
</script>

<main id="main" class="site-main" role="main">
<h1> RNASeq </h1>
<div id="form-content">

<form id='rnaform' action="rna_seq.php" method="post" enctype="multipart/form-data" autocomplete="off">
<table class="responsive">
<tr><td colspan='2'><label>Project name:</label> <input type="text" name="proj" id="proj"  value="RNASeq.<?php echo date("Y-m-d");?>">
<td colspan='1'><label for="gtf">Transcripts GTF file</label>
<select name="gtf">
  <!--option value="/data/share/test/wiggles/genes.gtf"> genes.gtf</option>
  <option value="/data/share/test/wiggles/genes_ercc.gtf">genes_ercc.gtf</option-->
  <option value="/data/share/genomes/index/hg19/GeneGTF/genes.gtf"> hg19 genes</option>
  <option value="/data/share/genomes/index/hg19/GeneGTF/genes_ercc.gtf">hg19 genes_ercc</option>
  <option value="/data/share/genomes/index/mm9/GeneGTF/genes.gtf">mm9 genes</option>
  <option value="/data/share/genomes/index/mm9/GeneGTF/genes_ercc.gtf">mm9 genes_ercc</option
  
</select></td>
<td colspan='1'><label for="libtp">Library Types</label>
<select name="libtp">
  <option value="ff-firststrand">ff-firststrand</option>
  <option value="ff-secondstrand">ff-secondstrand</option>
  <option value="ff-unstranded">ff-unstranded</option>
  <option value="fr-firststrand" selected>fr-firststrand</option>
  <option value="fr-secondstrand">fr-secondstrand</option>
  <option value="fr-unstranded">fr-unstranded</option>
  <option value="transfrags">transfrags</option>
</select></td>
</tr>
<tr><td colspan='4'>
<div class="input_groups"><div>
<label for="sbam1">Sorted Bam File Group 1</label> <button class="add_field_button">Add Groups</button>
<textarea rows="2" cols="20" name="sbam[]" >
/data/share/test/goldStd/SE_RNA/20160723-T1-DMSO-1-MH3241_S1_R1_001.20170405093503Aligned.out_hg19.sorted.bam,
/data/share/test/goldStd/SE_RNA/20160723-T1-DMSO-2-MH3241_S2_R1_001.20170406173356Aligned.out_hg19_sorted.bam,
/data/share/test/goldStd/SE_RNA/20160723-T1-DMSO-3-MH3241_S3_R1_001.20170405100821Aligned.out_hg19.sorted.bam
</textarea>
<div>
<label for="sbam2">Sorted Bam File Group 2</label>
<textarea rows="2" cols="20" name="sbam[]" >
/data/share/test/goldStd/SE_RNA/20160723-T1-IM-24h-1-MH3241_S10_R1_001.20170405115318Aligned.out_hg19.sorted.bam,
/data/share/test/goldStd/SE_RNA/20160723-T1-IM-24h-2-MH3241_S11_R1_001.20170405194038Aligned.out_hg19.sorted.bam,
/data/share/test/goldStd/SE_RNA/20160723-T1-IM-24h-3-MH3241_S12_R1_001.20170405200422Aligned.out_hg19.sorted.bam
</textarea></div>
</div>
</td></tr>
<tr>
<td colspan='2'>
<label for="lbl">Specify a label for each group, such as: label1,label2,…,labelN </label>
<input name="lbl" type="text"  style="width:90%; display:block;"  value="SRR20177,SRR201">
<td colspan='1'>
<!--label for="jid">Specify a Name for this job </label>
<input name="jid" type="text"  style="width:90%;display:block; "  value="My_RNASeq">
<td-->
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" >
<td>
<input type="submit" value="Submit" /> 
</td></tr>
</table>

</form>
</div>
<div class='note'><b>Note:</b> <br>
<?php include 'rnaseq_txt.php'; ?>
</div>
</main><!-- .site-main -->
<?php include './footer.php';?>
