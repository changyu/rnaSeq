Use this section to name your project, input the full path of your sequence file, select whether you have paired-end or single-end sequencing data, indicate whether you want to be notified by email when the analysis is complete and to choose the NGS alignment program. 
<br>	When inputting multiple files in batch, separate each file path by a comma. For paired-end data, indicate the file path of each mate in the separate boxes, as indicated in the example. Paired-end alignment can also be done in batch, though the order of the paired samples must match between the two input boxes. 
<br>	Input files are in the FASTQ format, which contains both the sequence and sequencing quality score. FASTQ files are typically demultiplexed and barcodes removed by your sequencing facility, and samples are zipped with the suffix ‘fastq.gz’.  The output from alignment is a BAM file (Binary Alignment/Map), which is used in downstream analysis. 
<br><br>
<i>References:</i> 
<br>1. <a href="http://bowtie-bio.sourceforge.net/bowtie2/index.shtml">http://bowtie-bio.sourceforge.net/bowtie2/index.shtml</a>
<br>2. Lighter: fast and memory-efficient sequencing error correction without counting. Song et al, Genome Biology 2014.
<br>3. <a href="https://ccb.jhu.edu/software/hisat2/index.shtml">https://ccb.jhu.edu/software/hisat2/index.shtml</a>
<br>4. Transcript-level expression analysis of RNA-seq experiments with HISAT, StringTie and Ballgown. Pertea et al, Nature Protocols 2016. 
<br>5. <a href="http://labshare.cshl.edu/shares/gingeraslab/www-data/dobin/STAR/STAR.posix/doc/STARmanual.pdf">http://labshare.cshl.edu/shares/gingeraslab/www-data/dobin/STAR/STAR.posix/doc/STARmanual.pdf</a>
<br>6. STAR: ultrafast universal RNA-seq aligner. Dobin et al, Bioinformatics 2013.
