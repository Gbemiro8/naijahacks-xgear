<?php
$message = '';
if(isset($_POST['submit_btn'])){
    include('connection.php');
        $name = mysqli_escape_string($conn, $_POST ['name']);
        $email = mysqli_escape_string($conn, $_POST['email']);
        $password1 = mysqli_escape_string($conn, $_POST['password1']);
        $confirm_password = mysqli_escape_string($conn, $_POST['confirm_password']);
        if(empty($name) || empty($email) || empty($password1) || empty($confirm_password)){
          $message .= '<div class="alert alert-danger" role="alert">ALL FEILDS ARE REQUIRED</div>';
        }
            elseif ( strlen ( $name ) < 3 || strlen ( $name ) > 20) {
                $message .= '<div class="alert alert-danger" role="alert">Name must be between 3 and 20 characters</div>';
                }
                elseif ( strlen ( $email ) < 3 || strlen( $email ) > 50) {
                $message .= '<div class="alert alert-danger" role="alert">Email must be between 3 and 50 characters</div>';
                }
                elseif ( strlen ( $password1) < 6) {
                  $message .= '<div class="alert alert-danger" role="alert">Password must be more than 6 characters</div>';
                }
                elseif ( strlen ( $confirm_password ) < 6) {
                  $message .= '<div class="alert alert-danger" role="alert">Password must be more than 6 characters</div>';
                }
                elseif ($password1 != $confirm_password){
                  $message .= '<div class="alert alert-danger" role="alert">Password doesnt match, please enter the correct password</div>';
                }
        else{
        $sql = "INSERT INTO registration (name ,email,  
        password1, confirm_password)
       VALUES('$name', 
               '$email',
               '$password1', 
               '$confirm_password')";
                $result = mysqli_query($conn , $sql);
                if($result){
                  $message .= '<div class="alert alert-success" role="alert">
                  Record Saved Successfully </div>';
                  header("Location:welcome.php");
                  }
                  else{
                      $message .= '<div class="alert alert-danger" role="alert">
                      Record not Saved ' . mysqli_error($conn) . '<button class="btn"><a href = "#"></a></button>
                      </div>';
                  
                  }
                  }
              }
           ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Signup Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" media="screen" href="style.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" 
        integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>  
    </head>
<body>
<?php
echo $message;
?>
</body>
</html>


  