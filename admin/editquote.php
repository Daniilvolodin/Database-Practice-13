<?php 

// check user is logged in...
if(isset($_SESSION['admin'])) {
    $ID = $_REQUEST['ID'];

    // Get author ID
    $find_sql = "SELECT * FROM quotes JOIN author ON (`author`.`Author_ID` = `quotes`.`Author_ID`) 
    WHERE `quotes`.`ID` = $ID";
    
    $find_query = mysqli_query($dbconnect, $find_sql);
    $find_rs = mysqli_fetch_assoc($find_query);


    $author_ID = $find_rs['Author_ID'];
    $first = $find_rs['First'];
    $initial = $find_rs['Initial'];
    $last = $find_rs['Last'];

    $current_author = $last.", ".$first." ".$initial;

    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');


    // initialise form variables for quote
    $quote = $find_rs['Quote'];
    $notes = $find_rs['Notes'];

    // Get subjects to populate tags...
    $subjectID_1 = $find_rs['Subject1_ID'];
    $subjectID_2 = $find_rs['Subject2_ID'];
    $subjectID_3 = $find_rs['Subject3_ID'];

    // retrieve subject names from subject table... 
    $tag1_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = $subjectID_1");
    $tag_1 = $tag1_rs['Subject'];

    $tag2_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = $subjectID_2");
    $tag_2 = $tag2_rs['Subject'];

    $tag3_rs = get_rs($dbconnect, "SELECT * FROM `subject` WHERE Subject_ID = $subjectID_3");
    $tag_3 = $tag3_rs['Subject'];

    
    // initialise tag ID's
    $tag_1_ID = $tag_2_ID = $tag_3_ID = 0;
    $has_errors = "no";

    // set up error fields / visibility
    $quote_error = $tag_1_error = "no-error";
    $quote_field = "form-ok";
    $tag_1_field = "tag-ok";
// Code below executes when the form is submitted... 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get data from form
    $author_ID = mysqli_real_escape_string($dbconnect, $_POST['author']);
    $quote = mysqli_real_escape_string($dbconnect, $_POST['quote']);
    $notes = mysqli_real_escape_string($dbconnect, $_POST['notes']);
    $tag_1 = mysqli_real_escape_string($dbconnect, $_POST['Subject_1']);
    $tag_2 = mysqli_real_escape_string($dbconnect, $_POST['Subject_2']);
    $tag_3 = mysqli_real_escape_string($dbconnect, $_POST['Subject_3']);

    // check data is valid
    // check quote is not blank
    if ($quote == "Please type your quote here" || $quote == "") {
        $has_errors = "yes";
        $quote_error = "error-text";
        $quote_field = "form-error";
    }

    if ($tag_1 == "") {
        $has_errors = "yes";
        $tag_1_error = "error-text";
        $tag_1_field = "tag-error";
    }

    if($has_errors != "yes") {
        // Get subject ID's via get_ID function... 
        $subjectID_1 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject',
        $tag_1);

        $subjectID_2 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject',
        $tag_2);

        $subjectID_3 = get_ID($dbconnect, 'subject', 'Subject_ID', 'Subject',
        $tag_3);
        
        // edit database entry
        $editentry_sql = "UPDATE `quotes` SET `Author_ID` = '$author_ID', `Quote` = '$quote', `Notes` = '$notes', 
        `Subject1_ID` = '$subjectID_1', `Subject2_ID` = '$subjectID_2', `Subject3_ID` = '$subjectID_3' 
        WHERE `quotes`.`ID` = $ID;";
        $editentry_query = mysqli_query($dbconnect, $editentry_sql);

        // get quote ID for next page
        $get_quote_sql = "SELECT * FROM `quotes` WHERE `Quote` = '$quote'";
        $get_quote_query = mysqli_query($dbconnect, $get_quote_sql);
        $get_quote_rs = mysqli_fetch_assoc($get_quote_query);

        $quote_ID = $get_quote_rs['ID'];
        $_SESSION['Quote_Success']=$quote_ID;

        // Got to success page... 
        header('Location: index.php?page=editquote_success&quote_ID='.$quote_ID);        
    } // end add entry to database if

} // end submit button if

} // end user logged in if

else {
    $login_error = "Please login to access this page";
    header("Location: index.php?page=../admin/login&error=$login_error");

} // end user not logged in else
?>

<h1>Edit Quote... </h1>

<form autocomplete="off" method="post" action="<?php echo
htmlspecialchars($_SERVER['PHP_SELF']."?page=../admin/editquote&ID=$ID"); ?>"
enctype="multipart/form-data">

    <p><i>If you need to change this quote's author and the author you need is 
    NOT in the list below. Please <a href="index.php?page=../admin/add_author" targe="_blank">
    add the author</a>. Then come back and reload this page to refresh the list.</i></p>

    <select class="adv gender" name="author">
                <!-- Deafult option is new author -->
                <option value="<?php echo $author_ID; ?>" selected><?php echo $current_author; ?></option>

                <?php 
                
                // get authors from database
                $all_authors_sql = "SELECT * FROM `author` ORDER BY `Last` ASC";
                $all_authors_query = mysqli_query($dbconnect, $all_authors_sql);
                $all_authors_rs = mysqli_fetch_assoc($all_authors_query);


                do {
                    $author_ID = $all_authors_rs['Author_ID'];
                    $first = $all_authors_rs['First'];
                    $initial = $all_authors_rs['Initial'];
                    $last = $all_authors_rs['Last'];

                    $author_full = $last.", ".$first." ".$initial;
                ?>

                <option value="<?php echo $author_ID; ?>"> 
                    <?php echo $author_full; ?>
                </option>

                <?php 
                

                } // end of author options 'do'

                while($all_authors_rs=mysqli_fetch_assoc($all_authors_query))

                ?>
            </select>
    <!-- Quote text area -->
    <div class="<?php echo $quote_error; ?>">
        This field can't be blank
    </div>

    <textarea class="add-field <?php echo $quote_field?>" name="quote"
    row="6"><?php echo $quote; ?></textarea>
    
    <br/><br />
        <input class="add-field <?php echo $notes; ?>" type="text"
        name="notes" value="<?php echo $notes; ?>" placeholder="Notes (optional) ..."/>
    
    <br/><br />
    
    <div class="<?php echo $tag_1_error ?>">
        Please enter at least one subject tag
    </div>
    
    
    <div class="autocomplete">
    <input class="add-field <?php echo $tag_1_field; ?>" id="subject1"
    type="text" name="Subject_1" 
    value="<?php echo $tag_1; ?>"placeholder="Subject 1 (Start Typing)..."/>
    
    </div>
    <br/><br />
    
    <div class="autocomplete">
        <input id="subject2" type="text" name="Subject_2"
        value="<?php echo $tag_2; ?>"
        placeholder="Subject 2 (Start Typing, Optional)...">
    </div>
    
    <br /><br />
    
    <div class="autocomplete">
        <input id="subject3" type="text" name="Subject_3"
        value="<?php echo $tag_3; ?>"
        placeholder="Subject 3 (Start Typing, Optional)...">
    </div>
    
    <br /><br />
    <!-- Submit Button -->
    <p>
        <input type="submit" value="Submit" />
    </p>

</form>

<!-- script to make autocomplete work -->
<script>
<?php include("autocomplete.php"); ?>

/* Arrays containing lists. */
var all_tags = <?php print("$all_subjects"); ?>;
autocomplete(document.getElementById("subject1"), all_tags);
autocomplete(document.getElementById("subject2"), all_tags);
autocomplete(document.getElementById("subject3"), all_tags);


</script>