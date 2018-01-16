<?php

if(isset($_POST['submit'])) {
    include_once('db.php');
    
    $first = mysqli_real_escape_string($conn, $_POST['first']);
    $last = mysqli_real_escape_string($conn, $_POST['last']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
    
    $empty = FALSE;
    
    foreach($_POST as $submit)
        if(is_empty($submit))
            $empty = TRUE;
    
    if($empty){
        header("Location: ../signup.php?signup=empty");
        exit();
    }
    
    // Provjeri ako je ime i prezime okej!
    else {
        if(!preg_match("/^[a-zA-Z]*?", $first) || !preg_match("/^[a-zA-Z]*?", $last) ) {
            header("Location: ../signup.php?signup=invalid");
            exit();
        } 
         // Provjeri ispravnost e-maila
        else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../signup.php?signup=email");
                exit();
            }
            // Provjeri ako imamo vec korisnika s tim uid
            else {
                $sql = "SELECT * FROM users WHERE user_uid='$uid'";
                $result = mysqli_query($conn, $sql);
                $resCheck = mysqli_num_rows($result);
                
                if($resCheck > 0) {
                    header("Location: ../signup.php?signup=user");
                    exit();
                }
                // Hash the password
                else {
                    $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT)
                    // Insert the user into the DB
                    $stmt = $conn->prepare("INSERT INTO users(user_first, user_last, user_email, user_uid, user_pwd) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $firstname, $lastname, $email, $uid, $pwd);
                }
            }
            
        }
   
        
    }


else { 
    header("Location: ../signup.php");
    exit();
}