<?php 
include 'php/config.php';
session_start();
$user_id = $_SESSION['user_id'];

$get_user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
if(!isset($user_id)){
    header('location: login.php');
}

$select = mysqli_query($conn, "SELECT * FROM user_form WHERE user_id = '$get_user_id' ");
if(mysqli_num_rows($select) > 0){
    $row = mysqli_fetch_assoc($select);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if(isset($_FILES['send_image'])){
        $img_name = $_FILES['send_image']['name'];
        $tmp_name = $_FILES['send_image']['tmp_name'];
        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);

        $extensions = ['png', 'jpeg', 'jpg'];
        if(in_array($img_ext, $extensions) === true){
            $time = time(); // to make image name unique
            $new_img_name = $time.$img_name;
            $img_upload_path = 'uploaded_img/'.$new_img_name;
            if(move_uploaded_file($tmp_name, $img_upload_path)){
                $query = "INSERT INTO messages (incoming_id, outgoing_id, message, image) VALUES ('$incoming_id', '$user_id', '$message', '$new_img_name')";
                mysqli_query($conn, $query);
            }
        }
    } else {
        $query = "INSERT INTO messages (incoming_id, outgoing_id, message) VALUES ('$incoming_id', '$user_id', '$message')";
        mysqli_query($conn, $query);
    }
    header('Location: chat.php?user_id='.$incoming_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Chat Area</title>
</head>
<body>
    <div class="container">
        <section class="chat-area">
            <header>
                <a href="home.php" class="back-icon"><img src="images/arrow.svg" alt=""></a>
                <img src="uploaded_img/<?php echo $row['img'] ?>" alt="">
                <div class="details">
                    <span><?php echo $row['name'] ?></span>
                    <p><?php echo $row['status'] ?></p>
                </div>
            </header>
            <div class="chat-box">
                <?php
                $query = "SELECT * FROM messages WHERE (incoming_id = '$user_id' AND outgoing_id = '$get_user_id') OR (incoming_id = '$get_user_id' AND outgoing_id = '$user_id') ORDER BY id";
                $result = mysqli_query($conn, $query);
                while($row = mysqli_fetch_assoc($result)){
                    if($row['image']){
                        echo '<div class="chat-message"><img src="uploaded_img/'.$row['image'].'" alt=""></div>';
                    }
                    if($row['message']){
                        echo '<div class="chat-message">'.$row['message'].'</div>';
                    }
                }
                ?>
            </div>
            <form class="typing-area" action="chat.php?user_id=<?php echo $get_user_id ?>" method="POST" enctype="multipart/form-data">
                <input type="text" name="incoming_id" value="<?php echo $get_user_id ?>" class="incoming_id" hidden>
                <input type="text" name="message" class="input-field" placeholder="Type a message here...">
                <button type="button" class="image" onclick="document.querySelector('.upload_img').click();"><img src="images/camera.svg" alt=""></button>
                <input type="file" name="send_image" accept="image/*" class="upload_img" hidden>
                <button type="submit" class="send_btn"><img src="images/send.svg" alt=""></button>
            </form>
        </section>
    </div>

    <script src="js/chat.js"></script>
</body>
</html>
