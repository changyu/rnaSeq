<?php
$uid=$current_user->user_login;
$umail=$current_user->user_email;
$usercode=md5("It sounds great!".$umail).$uid;
$user_dirname =  getcwd().'/user_data/'.$usercode;
$home_www="/home/".$current_user->user_login."/www-data";

$gffFolder ='gff/';
$macsFolder = 'macs/';
$macsEnrichedFolder = 'macsEnriched/';
$mappedEnrichedFolder = 'mappedEnriched/';
$mappedFolder = 'mappedFolder/';
$wiggleFolder = 'wiggles/';
$metaFolder = 'meta/';


$user_cpu=4;
$user_mem='4G';
$bradner="/usr/local/BradnerPipeline";
$hg19BT2idx="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/Bowtie2Index/genome";
$hg19BT2idxER="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/Bowtie2Index_ERCC/genome";
$hg19STAidx="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/STARindex/hg19";
$hg19STAidxER="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/STARindex/hg19_ERCC";
$hg19HS2idx="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/Hisat2Index/genome";
$hg19HS2idxER="/data/share/genomes/Homo_sapiens/UCSC/hg19/Sequence/Hisat2Index_ERCC/hg19_ercc";


$mm10BT2idx="/data/share/genomes/Mus_musculus/UCSC/mm10/Sequence/Bowtie2Index/genome";
$mm10STAidx="/data/share/genomes/Mus_musculus/UCSC/mm10/Sequence/STARindex/mm10";
$mm10HS2idx="/data/share/genomes/Mus_musculus/UCSC/mm10/Sequence/Hisat2Index/genome";


$mm9BT2idx="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/Bowtie2Index/genome";
$mm9BT2idxER="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/Bowtie2Index_ERCC/genome";
$mm9STAidx="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/STARindex/mm9";
$mm9STAidxER="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/STARindex/mm9_ercc";
$mm9HS2idx="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/Hisat2Index/mm9hisat2";
$mm9HS2idxER="/data/share/genomes/Mus_musculus/UCSC/mm9/Sequence/Hisat2Index_ERCC/mm9hisat2_ercc";


if (!session_id()) session_start();
$_SESSION['user_dir']=$user_dirname;
?>
