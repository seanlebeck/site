 <?php

//THIS DATA WILL BE SHOWN IN THE JAVASCRIPT FUNCTION IN "index.php"

$name = $_POST["username"];
$email = $_POST["email"];
$strlen = strlen($name); //NUMBER OF CHARACTERS
//EMAIL PATTERN
$validemail = "^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@([a-zA-Z0-9_-]+\.)+([a-zA-Z]{2,}){1}$";

//CONDITIONS THAT WILL SHOW IN THE OTHER FILE
if($strlen>25){
    echo 'Name too long.';
}
if($email){
    if(!eregi($validemail, $email)){
        echo 'Invalid email';
    }
}

?> 