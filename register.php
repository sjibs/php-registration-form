<?php
$error_message = "";
/**
 * connection information for a MySQL database hosted on an XAMPP server.
 * Users table can be created with the SQL command: 
 * CREATE TABLE `users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` text NOT NULL,`password` text NOT NULL, `email_updates` tinyint(1) NOT NULL, `email` text NOT NULL, PRIMARY KEY (`id`) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4
 * the database name is pz_db
 */
$conn = mysqli_connect('localhost:3307', 'root', 'mAsterk3y', 'pz_db');

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    //Sets the variables if they exist in the POST request.
    $username = isset($_POST["username"])?$_POST["username"]:"";
    $email = isset($_POST["email"])?$_POST["email"]:"";
    $password = isset($_POST["password"])?$_POST["password"]:"";
    $confirm_password = isset($_POST["confirm_password"])?$_POST["confirm_password"]:"";
    $updates = isset($_POST["email_updates"])?true:false;
    $terms = isset($_POST["terms"])?true:false;
    
    //Username error check
    if(!isset($username) || empty(trim($username))){
        $error_message.="<br>• Please enter a valid username";
    }else{
        if(!preg_match("/^[A-Za-z0-9_]+$/", $username)){
            $error_message.="<br>• Username must be alpha-numeric";
        }
        if(strlen($password)<3 || strlen($password)>15){
            $error_message.="<br>• Username must be between 2 and 16 characters long";
        }
    }

    //email error check
    if(!isset($email)|| empty(trim($email))){
        $error_message.="<br>• Please enter a valid email address";
    }else{
        $sql = "SELECT COUNT(`id`) FROM users WHERE email = ?";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement,"s", $email);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $count);
        mysqli_stmt_fetch($statement);
        if($count>0){
            $error_message.="<br>• Email address is already in use";
        }
        mysqli_stmt_close($statement);
    }
    //Password error check
    if(!isset($password)|| empty(trim($password))){
        $error_message.="<br>• Please enter a valid password";
    }else{
        if(strlen($password)<8){
            $error_message.="<br>• Password must be at least 8 characters long";
        }
        if(preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])+$/",$password)){
            $error_message.="<br>• Password must contain upper and lower case letters";
        }
        if(!preg_match("/[0-9]+$/",$password)){
            $error_message.="<br>• Password must contain numbers";
        }
    }
    //Confirm password error check
    if(!isset($confirm_password)|| empty(trim($confirm_password))){
        $error_message.="<br>• Please enter a valid confirm password";
    }else{
        if($confirm_password != $password){
            $error_message .= "<br>• Password and confirm password do not match";
        }
    }

    //Checks that the user has accepted their terms
    if(!$terms){
        $error_message .="<br>• Please accept the and conditions";
    }

    if(empty($error_message)){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (`id`, `username`, `email`, `password`, `email_updates`) VALUES (NULL, ?, ?, ?, ?);";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "sssi", $username,$email, $hashed_password, $updates);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        //would be replaced by emailing system, token management, cookies etc.
        header('location: login_success.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
        <div class="centered-box">
        <h2>Create Account</h2>

        <div class = "grid-container">
        <form action="register.php" method="post">
            <label class="item">Username</label>
            <input type="text" class="item" name="username" value=<?php echo isset($username)?$username:""?>>
            <br>
            <label class="item">Email</label>
            <input type="email" class="item" name="email" value=<?php echo isset($email)?$email:""?>>
            <br>
            <label class="item">Password</label>
            <input type="password" class="item" name="password" value=<?php echo isset($password)?$password:""?>>
            <br>
            <label class="item" >Confirm Password</label>
            <input type="password" class="item" name="confirm_password" value=<?php echo isset($confirm_password)?$confirm_password:""?>>
            <br>
            <label class="item">Email Updates</label>
            <input type="checkbox" class="item" name="email_updates" id="email_updates">
            <br>
            <label class="item">I accept the terms and conditions</label>
            <input type="checkbox" class="item" name="terms" id="terms">
            <br>
            <?php 
            //Will display an error message if one exists.
            if(isset($error_message)&& !empty($error_message)){
                echo "<p class = \"error-message\">Error creating account: ". $error_message ."</p>";
            } 
            ?>
            <input type="submit" name="submit" value = "Create Account">
            <a href= userlist.php>View all users</a>
    </div>    
</body>
</html>