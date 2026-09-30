<?php 
   require 'config.php';

   $sai = "SELECT * FROM user_registration";
   $result = $conn->query($sai);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <style>
    
                table, th, td {
            border: 1px solid black; 
        }
    
        
    </style>
</head>
<body>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <div>
            <table>
                      <tr>
                        <td>s.no</td>
                        <td>TEAMNAME</td>
                        <td>USERID</td>
                        <td>EMAILID</td>
                        <td>PHONE</td>
                        <td>TEAM</td>
                      </tr>
                        
                        <?php 
                         while  ($row = $result->fetch_assoc()){
                            $likki = $row['user_registred_id'];
                            $likk1 = $row['teamname'];
                            $likk2 = $row['userid'];
                            $likk3 = $row['email'];
                            $likk4 = $row['phone'];
                            $likk5 = $row['team'];
                              ?>
                                 <tr>
                                    <td><?php echo $likki?></td>
                                    <td><?php echo $likk1?></td>
                                    <td><?php echo $likk2?></td>
                                    <td><?php echo $likk3?></td>
                                    <td><?php echo $likk4?></td>
                                    <td><?php echo $likk5?></td>
                                 </tr>
                              <?php

                         }
                        ?>
            </table>
        </div>
</body>
</html>