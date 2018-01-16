<?php

if(!is_null($_POST['submit'])) {
    include_once('db.php');
    
    $first = mysqli_real_escape_string($conn, $_POST['first']);
    $last = mysqli_real_escape_string($conn, $_POST['last']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
    
    $empty = FALSE;
    
    foreach($_POST as $key => $value)
        if(empty($value))
            $empty = TRUE;
  
    if($empty){
         header("Location: ../signup.php?signup=empty");
         exit();
    }
    
    // Provjeri ako je ime i prezime okej!
    else {
        if(!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last) ) {
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
                $sql = "SELECT * FROM USERS WHERE user_uid='$uid'";
#               echo "<h3>Upit: $sql</h3>";
                $result = mysqli_query($conn, $sql);
#               print_r($result);
                $resCheck = mysqli_num_rows($result);
                
                if($resCheck > 0) {
                    header("Location: ../signup.php?signup=user");
                    exit();
                }
                // Hash the password
                else {
                    $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);
                    // Insert the user into the DB
                    $stmt = $conn->prepare("INSERT INTO USERS(user_first, user_last, user_email, user_uid, user_pwd) VALUES (?, ?, ?, ?, ?)");
       
                    $result = $stmt->bind_param("sssss", $first, $last, $email, $uid, $hash_pwd);
                    if ( false===$result ) {
                        die('bind_param() failed: ' . htmlspecialchars($stmt->error));
                    }

                    $result = $stmt->execute();
                    if ( false===$result) {
                        die('execute() failed: ' . htmlspecialchars($stmt->error));
                    }
                    
                    echo "New records created successfully";

                    $stmt->close();
                    $conn->close();
                }
            }
            
        }
        
    }
}
    
else { 
    header("Location: ../signup.php");
    exit();
}

?>