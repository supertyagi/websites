<?php
//add our database connection script
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

//process the form
if(isset($_POST['signupBtn'])){
    //initialize an array to store any error message from the form
    $form_errors = array();

    //Form validation
    $required_fields = array('email', 'username', 'password');

    //call the function to check empty field and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    //Fields that requires checking for minimum length
    $fields_to_check_length = array('username' => 4, 'password' => 6);

    //call the function to check minimum required length and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    //email validation / merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_email($_POST));
    
    //collect form data and store in variables
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if(checkDuplicateEntries("users", "email", $email, $db)){
            $result = flashMessage("Email is already taken, please try another one");
        }
        else if(checkDuplicateEntries("users", "username", $username, $db)){
            $result = flashMessage("Username is already taken, please try another one");
        }

    //check if error array is empty, if yes process form data and insert record
    else if(empty($form_errors)){

        //hashing the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try{

            //create SQL insert statement
            $sqlInsert = "INSERT INTO users (username, email, password, join_date)
              VALUES (:username, :email, :password, now())";

            //use PDO prepared to sanitize data
            $statement = $db->prepare($sqlInsert);

            //add the data into the database
            $statement->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));

            //check if one new row was created
            if($statement->rowCount() == 1){
                $result = flashMessage("Registration Successful", "Pass");
            }
        }catch (PDOException $ex){
            $result = flashMessage("An error occured: ".$ex->getMessage());
        }
    }
    else{
        if(count($form_errors) == 1){
            $result = flashMessage("There was 1 error in the form<br>");
        }else{
            $result = flashMessage("There were ".count($form_errors). "errors in the form <br>");
        }
    }

}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login Or Sign Up to Natour</title>
        <link rel="stylesheet" href="style.css" type="text/css">
    </head>

    <body>
    <div class="signUpcontainer">
        <video autoplay muted loop class="myVideo">
            <source src="img/video.mp4" type="video/mp4">
        </video>
        <div class="overlay"></div>
        <div class="formLogin">
        <img src="img/logo-green-1x.png">
              <div class="u-center-text u-margin-bottom-small">
            <h2 class="heading-secondary">
            Exciting tours for adventurous people
            </h2>
         </div>
        <div class="signUPform">
            <?php if(isset($result)) echo $result; ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
            <form action="" method="post">
                <input type="email" class="formElement" value="" placeholder="Your Email" name="email"><br>
                <input type="text" class="formElement" value="" placeholder="Your Username" name="username"><br>
                <input type="password" class="formElement" value=""  placeholder="Create Password" name="password"><br>
                <input type="submit" class="btn btn--green " name="signupBtn" value="Signup"> <br>
                <h4>Already SIgned Up ?</h4>
                <a href="login.php" class="loginLink">Login</a>
            </form>
        </div>
        </div>
        </div>
    </body>
</html>