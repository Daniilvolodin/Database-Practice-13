<?php 

// check user is logged in...
if(isset($_SESSION['admin'])) {


    $author_ID = $_REQUEST['author_ID'];

    // get country & occupation lists from database
    $all_countries_sql = "SELECT * FROM `country` ORDER BY `Country` ASC";
    $all_countries = autocomplete_list($dbconnect, $all_countries_sql, 
    'Country');
        
    $all_occupations_sql = "SELECT * FROM `career` ORDER BY `Job` ASC";
    $all_occupations = autocomplete_list($dbconnect, $all_occupations_sql, 'Job');
        
    // Get author details from database
    $all_authors_sql = "SELECT * FROM `author` WHERE `Author_ID` = $author_ID";
    $all_authors_query = mysqli_query($dbconnect, $all_authors_sql);
    $all_authors_rs = mysqli_fetch_assoc($all_authors_query);


    // initialise author variables
    $first = $all_authors_rs['First'];
    $initial = $all_authors_rs['Initial'];
    $last = $all_authors_rs['Last'];
    $job = $all_authors_rs['Born'];
    $gender_code = $all_authors_rs['Gender'];
    
    if ($gender_code == "M") {
        $gender = "Male";
    }
    else {
        $gender = "Female";
    }

    // retrieve country and occupation ID's from table

    $country_1_ID = $all_authors_rs['Country1_ID'];
    $country_2_ID = $all_authors_rs['Country1_ID'];

    $occupation_1_ID = $all_authors_rs['Career1_ID'];
    $occupation_2_ID = $all_authors_rs['Career2_ID'];
    
    // retrievee country / occupation names from country / occupation table... 

    // Look up ID and Name from each table using get_rs function... 
    $country_1_rs = get_rs($dbconnect, "SELECT * FROM `country` WHERE `Country_ID` = $country_1_ID");
    $country_2_rs = get_rs($dbconnect, "SELECT * FROM `country` WHERE `Country_ID` = $country_2_ID");

    $occupation_1_rs = get_rs($dbconnect, "SELECT * FROM `career` WHERE `Career_ID` = $occupation_1_ID");
    $occupation_2_rs = get_rs($dbconnect, "SELECT * FROM `career` WHERE `Career_ID` = $occupation_2_ID");

    $country_1 = $country_1_rs['Country'];
    $country_2 = $country_2_rs['Country'];

    $occupation_1 = $occupation_1_rs['Career'];
    $occupation_2 = $occupation_1_rs['Career'];


    // Initialise country and occupation ID's
    
        
    $last_error = $job_error = $gender_error = $country_1_error = 
    $occupation_1_error = "no-error";

    $last_field = $job_field = $gender_field = "form-ok";
    $country_1_field = $occupation_1_field = "tag-ok";


    
    // Get subject / topic list from database
    $all_tags_sql = "SELECT * FROM `subject` ORDER BY `Subject` ASC";
    $all_subjects = autocomplete_list($dbconnect, $all_tags_sql, 'Subject');


    // initialise form variables for quote
    $quote = "Please type your quote here";
    $notes = "";
    $tag_1 = "";
    $tag_2 = "";
    $tag_3 = "";
    
    // initialise tag ID's
    $tag_1_ID = $tag_2_ID = $tag_3_ID = 0;
    $has_errors = "no";

    // set up error fields / visibility
    $quote_error = $tag_1_error = "no-error";
    $quote_field = "form-ok";
    $tag_1_field = "tag-ok";
// Code below executes when the form is submitted... 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // if author is unknown, get values from author part of form

        $first = mysqli_real_escape_string($dbconnect, $_POST['first']);
        $initial = mysqli_real_escape_string($dbconnect, $_POST['initial']);
        $last = mysqli_real_escape_string($dbconnect, $_POST['last']);
        $job = mysqli_real_escape_string($dbconnect, $_POST['job']);

        $gender_code = mysqli_real_escape_string($dbconnect, $_POST['gender']);
        if ($gender_code == "F") {
            $gender = "Female";
        }
        elseif ($gender_code == "M") {
            $gender = "Male";
        }
        else {
            $gender = "";
        }
    
    $country_1 = mysqli_real_escape_string($dbconnect, $_POST['country1']);
    $country_2 = mysqli_real_escape_string($dbconnect, $_POST['country2']);
    $occupation_1 = mysqli_real_escape_string($dbconnect, $_POST['occupation1']);
    $occupation_2 = mysqli_real_escape_string($dbconnect, $_POST['occupation2']);
    
    // Error checking goes here

    // check last name is not blank
    if ($last == "") {
        $has_errors = "yes";
        $last_error = "error-text";
        $last_field = "form-error";
    }

    // check year of birth is valid
    $valid_job = isValidYear($job);

    if ($job < 0 || $valid_job != 1 || !preg_match('/^\d{1,4}$/', $job)) {
        $has_errors = "yes";
        $job_error = "error-text";
        $job_field = "form-error";
    }

    // check that first country has been filled in
    if ($country_1 == ""){
        $has_errors = "yes";
        $country_1_error = "error-text";
        $country_1_field = "tag-error";
    }   
    
    // check that first country has been filled in
    if ($occupation_1 == ""){
        $has_errors = "yes";
        $occupation_1_error = "error-text";
        $occupation_1_field = "tag-error";
    }
    
    if ($has_errors != "yes") {
        
        $countryID_1 = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_1);
        $countryID_2 = get_ID($dbconnect, 'country', 'Country_ID', 'Country', $country_2);

        $occupationID_1 = get_ID($dbconnect, 'career', 'Job_ID', 'Job', $occupation_1);
        $occupationID_2 = get_ID($dbconnect, 'career', 'Job_ID', 'Job', $occupation_2); 

        // edit entry to database
        $editauthor_sql = "UPDATE `author` SET `First` = '$first', `Last` = '$last', 
        `Gender` = '$gender_code', `Born` = '$job', `Country1_ID` = '$countryID_1', 
        `Country2_ID` = '$countryID_2', `Career1_ID` = '$occupationID_1', 
        `Career2_ID` = '$occupationID_2' WHERE `author`.`Author_ID` = $author_ID;";
        $editentry_author = mysqli_query($dbconnect, $editauthor_sql);

        header('Location: index.php?page=author&author_ID='.$author_ID);

    }

} // end submit button if

} // end user logged in if

else {
    $login_error = "Please login to access this page";
    header("Location: index.php?page=../admin/login&error=$login_error");

} // end user not logged in else
?>

<h1>Add Quote... </h1>

<form autocomplete="off" method="post" action="<?php echo
htmlspecialchars($_SERVER['PHP_SELF']."?page=../admin/editauthor&author_ID=$author_ID"); ?>"
enctype="multipart/form-data">


    <!-- Author's first name, optional-->
    
    <input class="add-field" type="text" name="first" value="<?php echo $first; ?>"
    placeholder="Author's First Name" />
    
    <br /><br />
    
    <input class="add-field" type="text" name="initial" value="<?php echo $initial; ?>" 
    placeholder="Author's Middle Name (optional)" />

    <br /><br />
    
    <div class="<?php echo $last_error ?>">
        Author's last name can't be blank
    </div>
    <input class="add-field <?php echo $last_field; ?>" type="text" name="last" value="<?php echo $last; ?>" 
    placeholder="Author's Last Name" />

    <br /><br />

    <select class="adv gender <?php echo $gender_field; ?>" name="gender">
    
        <?php 
        // Selected option (so form holds user input)
        if ($gender_code==""){
        ?>
            <option value="" selected>Gender (Choose something)...
            </option>

        <?php
        }
        
        else {
        ?>
        
            <option value="<?php echo $gender_code; ?>" selected>
            <?php echo $gender; ?>
            </option>

        <?php
        } // end gender chosen else

        ?>
        <option value="F">Female</option>
        <option value="M">Male</option>
    </select>
    
    <br /><br />
    
    <div class="<?php echo $job_error; ?>"> 
        Please enter a valid year of birth (modern authors only).
    </div>
    
    <input class="add-field <?php echo $job_field; ?>" type="text" name="job"
    value="<?php echo $job; ?>" placeholder="Author's year of birth" />
    
    <br /><br />
    <div class="<?php echo $country_1_error; ?>"> 
        Please enter at least one country
    </div>
    <div class="autocomplete">
        <input class="<?php echo $country_1_field; ?>" id="country1" type="text" 
        name="country1" value="<?php echo $country_1; ?>" 
        placeholder="Country 1 (Required, Start Typing)..."/>
    </div>

    <br /><br />

    <div class="autocomplete">
        <input id="country2" type="text" 
        name="country2" value="<?php echo $country_2; ?>" 
        placeholder="Country 2 (Start Typing)..."/>
    </div>
    
    <br /><br />

    <div class="<?php echo $occupation_1_error; ?>">
        Please enter at least one country
    </div>
    
    <div class="autocomplete">
        <input class="<?php echo $occupation_1_field; ?>" id="occupation1"
        type="text" name="occupation1" value="<?php echo $occupation_1; ?>"
        placeholder="Occupation 1 (Required, Start Typing)..." />
    </div>

    <br /><br />
    
    <div class="autocomplete">
        <input id="occupation2" type="text" name="occupation2"
        placeholder="Occupation 2 (Start Typing)...">
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

var all_countries = <?php print("$all_countries"); ?>;
autocomplete(document.getElementById("country1"), all_countries);
autocomplete(document.getElementById("country2"), all_countries);

var all_occupations = <?php print("$all_occupations"); ?>;
autocomplete(document.getElementById("occupation1"), all_occupations);
autocomplete(document.getElementById("occupation2"), all_occupations);

</script>