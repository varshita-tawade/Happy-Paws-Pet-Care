<?php 
include 'connect.php'; // तुमची database connection फाईल (खात्री करा यात DB नाव 'register' आहे)

if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // १. 'users' टेबलमध्ये ईमेल आधीच आहे का ते तपासले
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if($result->num_rows > 0){
        echo "<script>alert('Email Address Already Exists!'); window.location='index.php';</script>";
    } else {
        // २. 'users' टेबलमध्ये नवीन युजरचा डेटा भरला
        $insertQuery = "INSERT INTO users(firstName, lastName, email, password)
                       VALUES ('$firstName', '$lastName', '$email', '$password')";
        
        if($conn->query($insertQuery) == TRUE){
            header("location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if(isset($_POST['signIn'])){
   $email = $_POST['email'];
   $password = md5($_POST['password']);
   
   // ३. 'users' टेबलमधून लॉगिनची माहिती तपासली
   $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
   $result = $conn->query($sql);

   if($result->num_rows > 0){
    session_start();
    $row = $result->fetch_assoc();
    $_SESSION['email'] = $row['email'];
    
    // लॉगिन झाल्यावर 'homepage.php' कडे पाठवा
    header("Location: homepage.php");
    exit();
   } else {
    echo "<script>alert('Incorrect Email or Password'); window.location='index.php';</script>";
   }
}
?>