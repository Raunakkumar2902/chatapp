<?php
include 'php/config.php'; // Including the database connection
session_start();
$image_rename = 'default-avatar.png'; // User default image

if (isset($_POST['submit'])) { // If user clicks the submit button
    $ran_id = rand(time(), 1000000000); // Creating random number

    // Declaring input and escaping to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpassword = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

    // Checking if email is valid
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $image = $_FILES['image']['name']; // User image name
        $image_size = $_FILES['image']['size']; // User image size
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_rename = time() . '_' . $image;
        $image_folder = 'uploaded_img/' . $image_rename; // Image folder
        $status = 'Active Now'; // User status

        // Checking if user already exists
        $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'");
        
        if (!$select) {
            die("Error: " . mysqli_error($conn)); // Display error if the query fails
        }

        if (mysqli_num_rows($select) > 0) {
            $alert[] = "User already exists!";
        } else {
            if ($password != $cpassword) {
                $alert[] = "Password does not match!";
            } elseif ($image_size > 2000000) {
                $alert[] = "Image size is too large!";
            } else {
                // Inserting user data into the database
                $insert = mysqli_query($conn, "INSERT INTO `user_form`(`user_id`, `name`, `email`, `password`, `img`, `status`) 
                    VALUES ('$ran_id', '$name', '$email', '$password', '$image_rename', '$status')");
                
                if ($insert) { // If insert is successful
                    move_uploaded_file($image_tmp_name, $image_folder); // Moving image file
                    header('location: login.php');
                } else {
                    $alert[] = "Connection failed, please retry!";
                }
            }
        }
    } else {
        $alert[] = "$email is not a valid email!";
    }
}

if (isset($_SESSION['user_id'])) {
    header("location: home.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        :root{
    --blue: #3498db;
    --dark-blue: #2980b9;
    --red: #e74c3c;
    --dark-red: #c0392b;
    --light-bg: #eee;
    --white: #fff;
    --box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}
*{
    margin: 0;padding: 0;
    box-sizing: border-box;
    outline: none;border: none;
    text-decoration: none;
    font-family: 'poppins', sans-serif;
}
*::-webkit-scrollbar{
    width: 10px;
}
*::-webkit-scrollbar-thumb{
    background: var(--blue);
}
.btn,
.delete-btn{
    width: 100%;
    border-radius: 5px;
    padding: 10px 30px;
    color: var(--white);
    display: block;
    text-align: center;
    cursor: pointer;
    font-size: 20px;
    margin-top: 10px;
}
.btn{ 
    background: var(--blue);
}
.btn:hover{ 
    background: var(--dark-blue);
}
.delete-btn{
    background: var(--red);
}
.delete-btn:hover{
    background: var(--dark-red);
}
.form-container{
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.form-container form{
    padding: 20px;
    background: var(--white);
    box-shadow: var(--box-shadow);
    text-align: center;
    width: 500px;
    border-radius: 5px;
}
.form-container form h3{
    margin-bottom: 10px;
    font-size: 30px;
    color: var(--blue);
    text-transform: uppercase;
}
.alert{
    margin: 10px 0;
    width: 100%;
    border-radius: 5px;
    padding: 10px;
    text-align: center;
    background: var(--red);
    font-size: 20px;
    color: var(--white);
}
.form-container form .box{
    width: 100%;
    border-radius: 5px;
    padding: 12px 14px;
    color: var(--blue);
    font-size: 18px;
    margin: 10px 0;
    background: var(--light-bg);
}

.form-container form p{
    margin-top: 15px;
    font-size: 20px;
    color: var(--blue);
}
.form-container form p a{
    color: var(--red);
}
.form-container form p a:hover{
    text-decoration: underline;
}
.container{
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.container .users{
    padding: 20px;
    background: var(--white);
    box-shadow: var(--box-shadow);
    width: 400px;
    border-radius: 5px;
}
.container .users .profile{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 20px;
}

.content img{
    height: 50px;
    width: 50px;
    border-radius: 50px;
    object-fit: cover;
}
header .content{
    display: flex;
    align-items: center;
}
header .content .details{
    color: #000;
    margin-left: 10px;
}
header .content span{
   font-size: 18px;
   font-weight: 500;
}
header .logout{
    display: block;
    background: var(--blue);
    color: var(--white);
    padding: 7px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 17px;
    cursor: pointer;
}
header .logout:hover{
    background: var(--dark-blue);
}
.users .search{
    margin: 20px 0;
    display: flex;
    position: relative;
    align-items: center;
    justify-content: space-between;
}
.users .search input{
    height: 42px;
    width: calc(100% - 50px);
    font-size: 16px;
    background: var(--light-bg);
    padding: 0 13px;
    border-radius: 5px 0 0 5px;
}
.users .search button{
    width: 47px;
    height: 42px;
    cursor: pointer;
    background: var(--blue);
    padding: 10px;
    border-radius: 0 5px 5px 0;
}
.users .search button:hover{
    background: var(--dark-blue);
}
.all_users{
    max-height: 350px;
    overflow-y: auto;
}
.all_users::-webkit-scrollbar{
    width: 0px;
}
.all_users .content{
    display: flex;
    align-items: center;
}
.all_users .details{
    margin-left: 10px;
    color: #000;
}
.all_users a{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    margin-bottom: 15px;
    padding-right: 15px;
}
.all_users a:last-child{
    margin-bottom: 0;
}
.all_users a .status-dot{
    background: var(--blue);
    padding-left: 10px;
    height: 12px;
    width: 12px;
    border-radius: 10px;
}
.all_users a .status-dot.offline{
    background: #ccc;
}



/* update profile */
.update-profile{
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.update-profile form{
    padding: 15px;
    background: var(--white);
    box-shadow: var(--box-shadow);
    text-align: center;
    width: 600px;
    border-radius: 5px;
}
.update-profile form img{
    height: 200px;
    width: 200px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 5px;
}
.update-profile form .flex{
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 15px;
}
.update-profile form .flex .inputBox{
    width: 49%;
}
.update-profile form .flex .inputBox span{
    text-align: left;
    display: block;
    margin-top: 12px;
    font-size: 17px;
    color: var(--blue);
}
.update-profile form .flex .inputBox .box{
    width: 100%;
    border-radius: 5px;
    padding: 12px 14px;
    color: var(--blue);
    font-size: 18px;
    margin: 10px 0;
    background: var(--light-bg);
}



/* chat area */
.chat-area{
    border-radius: 16px;
    box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                0 32px 64px -48px rgba(0,0,0,0.5);
                
}
.chat-area header{
    display: flex;
    align-items: center;
    padding: 18px 30px;
}
.chat-area header .back-icon img{
    height: 20px;
    width: 25px;
}
.chat-area header img{
    height: 45px;
    width: 45px;
    margin: 0px 5px;
    border-radius: 50px;
}
.chat-box{
    position: relative;
    height: 100%;
    min-height: 420px;
    max-height: 420px;
    overflow-y: auto;
    padding: 10px 30px 20px 30px;
    background: #f7f7f7;
    box-shadow: inset 0 32px 32px -32px rgb(0 0 0 / 5%),
                inset 0 -32px 32px -32px rgb(0 0 0 / 5%);
}
.chat-box::-webkit-scrollbar{
    width: 0;
}
.chat-box .text{
    display: flex;
    align-items: center;
    justify-content: center;
    flex-flow: column;
    position: absolute;
    gap: 10px;
    top: 30%;
    left: 50%;
    width: calc(100% - 50px);
    text-align: center;
    transform: translate(-50%, -50%)
}
.chat-box .text img{
    height: 150px;
    width: 150px;
    border-radius: 50%;
}
.chat-box .chat{
    margin: 15px 0;
    border-radius: 16px;
    box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
    0 32px 64px -48px rgba(0,0,0,0.5);
    /* width: 100%; */
    background: var(--white);
}
.chat-box .chat p{
    word-wrap: break-word;
    padding: 8px 16px;
    box-shadow:  0 0 32px rgb(0 0 0 / 8%),
                 0rem 16px 16px -16px rgb(0 0 0 / 10%);
}
.chat-box .outgoing{
    display: flex;
}
.chat-box .outgoing .details{
    margin-left: auto;
    max-width: calc(100% - 130px);
}
.outgoing .details p{
    background: var(--blue);
    color: var(--white);
    border-radius: 18px 18px 0 18px;
}
.outgoing .details img{
    height: 150px;
    width: 100px;
}
.incoming{
    display: flex;
    align-items: flex-end;
}
.incoming img{
    height: 40px;
    width: 40px;
    object-fit: cover;
    border-radius: 50%;
}
.incoming .details{
    margin-right: auto;
    margin-left: 10px;
    max-width: calc(100% - 130px);
}
.incoming .details p{
    background: var(--dark-blue);
    color: var(--white);
    border-radius: 18px 18px 18px 0;
}
.incoming .details img{
    height: 150px;
    width: 100px;
    border-radius: 0;
}
.typing-area{
    padding: 18px 30px;
    display: flex;
    gap: 0.5rem;
}
.typing-area input{
    height: 45px;
    width: calc(100% - 58px);
    font-size: 16px;
    padding: 0px 13px;
    background: var(--light-bg);
    border-radius: 5px 0 0 5px;
}
.typing-area .image{
    width: 50px;
    background: transparent;
    cursor: pointer;
}
.typing-area .image img{
    height: 2rem;
    width: 2rem;
}
.typing-area .send_btn{
    width: 55px;
    background: var(--blue);
    cursor: pointer;
    border-radius: 0 5px 5px 0;
    opacity: 0.7;
    pointer-events: none;
    justify-content: center;
    align-items: center;
    display: flex;
}
.typing-area .send_btn.active{
    opacity: 1;
    pointer-events: auto;
}
.typing-area .send_btn img{
    height: 25px;
    width: 25px;
}
@media (max-width: 650px){
    .container{
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;left: 0;right: 0;
        padding: 0;display: block;
        overflow-y: hidden;
    }
    .container .users{
        position: absolute;
        height: 100%;
        width: 100%;
    }
    .container .chat-box{
        position: absolute;
        left: 0;right: 0;
        min-height: 466px;
        max-width: 100%;
    }
    .typing-area{
        position: absolute;
        bottom: 0;
        width: 100%;
        background: var(--white);
    }
    .update-profile{
        min-height: 50vh;
        padding: 0;
        height: 100%;
    }
    .update-profile form .flex{
        flex-wrap: wrap;
        gap: 0;
    }
    .update-profile form .flex .inputBox{
        width: 100%;
    }
    .form-container form{
        box-shadow: none;
        border: none;
    }

}
        </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Create Account</title>
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Create Account</h3>
            <?php 
                if (isset($alert)) {
                    foreach ($alert as $message) {
                        echo '<div class="alert">' . $message . '</div>';
                    }
                }
            ?>
            <input type="text" name="name" placeholder="Enter username" class="box" required>
            <input type="email" name="email" placeholder="Enter email" class="box" required>
            <input type="password" name="password" placeholder="Enter password" class="box" required>
            <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
            <input type="file" name="image" class="box" accept="image/*">
            <input type="submit" name="submit" class="btn" value="Start Chatting">
            <p>Already have an account? <a href="login.php">Login now</a></p>
        </form>
    </div>
</body>
</html>
