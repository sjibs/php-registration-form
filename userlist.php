<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
 <div class = "centered-box">
 <h2>List of Accounts</h2>
<table>
  <tr>
    <th class = "table-header">User ID</th>
    <th class = "table-header">Username</th>
    <th class = "table-header">Email Address</th>
    <th class = "table-header">Recieves Email Updates</th>
  </tr>
  <div class="line"></div>
  <?php
   $conn = mysqli_connect('localhost:3307', 'root', 'mAsterk3y', 'pz_db');
   $sql = "SELECT `id`,`username`,`email_updates`,`email` FROM users";
   $statement = mysqli_prepare($conn, $sql);
   mysqli_stmt_execute($statement);
   mysqli_stmt_bind_result($statement, $id, $username, $email_updates,$email);
   //Iterates through all of the users returned by the SQL query and displays them in the table
   while(mysqli_stmt_fetch($statement)){
    echo("<tr><th>".$id."</th>");
    echo("<th>".$username."</th>");
    echo("<th>".$email."</th>");
    echo("<th>".($email_updates==1?"true":"false")."</th></tr>");
   }
  ?>
</table>
 </div>

</body>
</html>