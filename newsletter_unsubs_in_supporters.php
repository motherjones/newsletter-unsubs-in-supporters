<?php
/* script to find newsletter unsubs who are still in the supporters list */

$get_emails = glob("*.csv");
$count_ge = count($get_emails);

//loop through all files and cut email column
for($i = 0;$i < $count_ge; $i++) {
    getEmails($get_emails[$i]);
    print "Files have been cut" . PHP_EOL;
}

// concat, sort, uniq files
conCatFiles();
// concat, sort, uniq files for final operation
findSupporters();
// clean up extra files
cleanUpFiles();

// function to cut all email columns from the files (position 2)
function getEmails($file) {
	// insert _cut suffix to filenames
    $name = str_ireplace(".csv", "_cut.csv", $file);
    // get cut command ready
    $cut_file = "cut -f 2 -d ',' $file > $name";
    // execute cut command
    $void = exec($cut_file);
    return;
}

// function to concat, sort, and uniq all newsletter lists
function conCatFiles() {
	// get all files with email columns cut
    $get_files = glob("*_cut.csv");
    $count = count($get_files);
    $return_arr = "";
    $count_arr = 0;
    
    for($x = 0; $x < $count; $x++) {
    	// skip supporters file, concatenate the rest
        if(trim($get_files[$x]) !== "supporters_cut.csv") {
            $return_arr .= $get_files[$x] . " ";
            $count_arr++;
            print ".+" . PHP_EOL;
        }
        else {
        	print "found supporters file...skipping..." . PHP_EOL;
        }
    }

    // concat files
    $return_arr = "cat " . trim($return_arr) . " > all_news.csv";
    $void = exec($return_arr);
    // sort files
    $return_arr = "sort all_news.csv > all_news_sorted.csv";
    $void = exec($return_arr);
    // uniq files
    $return_arr = "uniq all_news_sorted.csv > all_news_uniq.csv";
    $void = exec($return_arr);
    
    print "Newsletter list files all concatenated, sorted, and deduped" . PHP_EOL;
    return;
}

// finalizing function
function findSupporters() {
	// supporters file should be supporters_cut.csv
	// concat merged and uniqued newsletter files with the supporters file
	$unite = "cat supporters_cut.csv all_news_uniq.csv > all_merged.csv";
	$void = exec($unite);
	// sort the file
	$sort = "sort all_merged.csv > all_merged_sorted.csv";
	$void = exec($sort);
	// uniq the file and produce the final file
	$uniq = "uniq -d all_merged_sorted.csv > final_output.csv";
	$void = exec($uniq);

	print "All done" . PHP_EOL;
	return;
}

// clean up function to remove unneeded files
function cleanUpFiles() {
	// remove all cut files
	$relate = "rm *_cut.csv";
	$void = exec($relate);
	// remove all newsletter files
	$relate = "rm all_news*.csv";
	$void = exec($relate);
	// remove all merged files 
	$relate = "rm all_merged*.csv";
	$void = exec($relate);
}
?>