<?php
session_start();
if(isset($_POST['submit_btn'])){
    include('connection.php');
    $email = mysqli_escape_string($conn, $_POST['email']);
    $password1 = mysqli_escape_string($conn, $_POST['password1']);
    if(empty($email) || empty($password1)){
    echo "All fields are Required";
    }
    else{
        $sql = "SELECT * FROM registration WHERE email = '$email' and password1 ='$password1'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
    }

        if($resultCheck > 0 ){
            $row = mysqli_fetch_assoc($result);
            echo  $row['email'];
            echo  $row['name'];

            $name = $row['name'];
            $email = $row['email'];
           
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            header("Location:welcome.php");
    }
        else{
            die("Email or doesn't exist, please enter the correct Email");
            header("Location:login.html");
 }
}

?>
