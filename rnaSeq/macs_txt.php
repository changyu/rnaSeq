Model-based Analysis for ChIP-Seq (MACS) is a peak-finding algorithm useful in ChIP-seq analysis. Output from MACS is helpful in assessing the quality of your data (including peak count and negative peak count), generating wig files that can be visualized on IGV or UCSC browsers and BED files that can be used for visualization and downstream analysis. <br>
Through the dropdown menus, select the version of MACS to use and the P-value cutoff for peak calling (the default is 1e-9 for MACS1.4). Enter both your ChIP and input files into the appropriate boxes – while an input control file is not necessary to run MACS, it is often a helpful control. Check the box next to “Control” to enter the path to your input file. Samples can be run in batch, though the sequencing of the ChIP and input samples in the different boxes must match. 
<br>
<i>References:</i> 
<br>1.<a href="http://liulab.dfci.harvard.edu/MACS/">http://liulab.dfci.harvard.edu/MACS/</a>
<br>2. Model-based analysis of ChIP-Seq (MACS). Zhang et al, Genome Biology 2008. 
