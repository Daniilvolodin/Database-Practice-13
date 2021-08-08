<h2>All Results</h2>

<?php
if (!isset($_REQUEST['subject_ID'])) {
    header('Location: index.php');

}

$subject_to_find = $_REQUEST['subject_ID'];

    // subject heading... 
    $sub_sql = "SELECT * FROM `subject` WHERE `Subject_ID` = $subject_to_find";
    $sub_query = mysqli_query($dbconnect, $sub_sql);
    $sub_rs = mysqli_fetch_assoc($sub_query);

$find_sql = "SELECT * FROM `quotes`
JOIN author ON (`author`.`Author_ID` = `quotes`.`Author_ID`)

";

?>
<h2>Subject Results (<?php echo $sub_rs['Subject']; ?>)</h2>

<?php

// get quotes

$find_query = mysqli_query($dbconnect, $find_sql);
$find_rs = mysqli_fetch_assoc($find_query);

// Loop through results and display them.

do{
    $quote = preg_replace('/[^A-Za-z0-9.,\s\'\-]/', ' ', $find_rs['Quote']);
    include("get_author.php");

    ?>

<div class="results">
    <p>
        <?php echo $quote; ?> <br />
        
        <!-- display author name -->
        <a href="index.php?page=author&author_ID=<?php echo $find_rs['Author_ID'];?>">
        <?php echo $full_name; ?>
        </a>
    </p>

    <!-- subject tags go here -->
    <?php include("show_subjects.php"); ?>    


</div>
<br />

<?php
} // end of display results 'do' 

while($find_rs = mysqli_fetch_assoc($find_query))

?>