<?php include("path.php"); ?>
<?php include(ROOT_PATH . '/app/controllers/posts.php');

//an to make sure email is valid like gmail.com , live.com, to use that , but dont know how :D
//        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
//
//        // Validate e-mail
//        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            echo("$email is a valid email address");
//        } else {
//            echo("$email is not a valid email address");
//        }
//



include(ROOT_PATH . '/app/database/connect.php');
global $post;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

usersOnly('/login.php');
$topics = selectAll('topics');
$posts = selectAll('posts', ['published' => 1]);


$loggedUserData = (is_array($_SESSION) )
    ? selectAll('users', ['id'=>$_SESSION['id']])
    :false;
if (is_array($loggedUserData[0])){
    $loggedUserData=$loggedUserData[0];
}


error_reporting(0); // For not showing any error

function banCurrentUser(){
    $currentUserId=$_SESSION['id'];
global $conn;

$now = new DateTime('now');
$now=$now->add(new DateInterval('P1D'));

    $dateFormatted=$now->format('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE users SET is_banned = 1, ban_until = '$dateFormatted' WHERE id = ?");
    $stmt->bind_param('i', $currentUserId);

    if ($stmt===false){
        die ('heck! '. $conn->error);
    }

    $insertSuccess = $stmt->execute();
    $loggedUserData['is_banned']=1;

}

if (isset($_POST['submit'])) { // Check press or not Post Comment Button FOR ADDING IN DATABASE
    $name = $_POST['name']; // Get Name from form
    $email = $_POST['email']; // Get Email from form
    $comment = trim($_POST['comment']); // Get Comment from form
    $Post_id = $_POST['comment-id'];
    $singlePostId = (isset($_GET['id']))?intval($_GET['id']):false;

    $comment=str_replace(chr(10), '', $comment);
    $badWords = getBadWords(1);
    $racistWords = getBadWords(2);

    $commentWordArray=explode(' ', $comment);
    $uniqueArray=array_unique (  $commentWordArray);
    $containsBadWords = count(array_intersect($uniqueArray, $badWords)) > 0;
    $containsRacistWords = count(array_intersect($uniqueArray, $racistWords)) > 0;

    if ($containsBadWords) {
        banCurrentUser();
        blocedUser();

    } elseif ($containsRacistWords) {
        banCurrentUser();
        blocedUser();

    } else {
        $sql = "INSERT INTO comments (name, email, comment, Post_id) VALUES (?, ?, ?, ?)";

        /** @noinspection PhpUndefinedVariableInspection */
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $name, $email, $comment, $Post_id);
        $insertSuccess = $stmt->execute();

        if ($insertSuccess) {
            echo "<script>alert('Comment added successfully.')</script>";
        }
    }



}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Candal|Lora" rel="stylesheet">
    <!-- Custom Styling -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Slick Carousel -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- Custom Script -->
    <script src="assets/js/scripts.js"></script>

    <!-- why $post is undefined? -->
    <title><?php echo $post['title']; ?> | Velibor</title>
</head>
<body>

<div class="prev-comments">

</div>


<?php include(ROOT_PATH . "/app/includes/header.php"); ?>
<!-- Page Wrapper -->
<div class="page-wrapper">

    <!-- Content -->
    <div class="content clearfix">

        <!-- Main Content Wrapper -->
        <div class="main-content-wrapper">
            <div class="main-content single">


                <h1 class="post-title"><?php echo $post['title']; ?></h1>


                <div class="post-content">
                    <?php echo html_entity_decode($post['body']); ?>

                </div>

                <div class="main-content-wrapper">

                    <?php
                    if($loggedUserData['is_banned']<1){
                    ?>

                    <form action="" method="POST" class="row">
                        <div class="row">
                            <div>
                                <input type="hidden" name="name" id="name" value= "<?php echo $loggedUserData['username'] ?>"/>
                            </div>
                            <div >
                                <input type="hidden" name="email" id="email" value= "<?php echo $loggedUserData['email'] ?>"/>
                            </div>
                            <div >
                                <input type="hidden" name="comment-id" id="comment-id" value= "<?php echo $post['id'] ?>"/>
                            </div>
                        </div>
                        <div class="row" >

                            <textarea cols="80" rows="15" id="comment" name="comment" placeholder="Enter your Comment" required></textarea>
                        </div>
                        <div >
                            <button name="submit" class="btn">Post Comment</button>
                        </div>


                    </form>
                    <?php
                    };
                    ?>
                    <div class="prev-comments">


                        <?php

                        $getId = (isset($_GET['id'])) ? intval($_GET['id']) : 0;
                        $sql = "SELECT * FROM comments WHERE Post_id= ?";


                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $getId);
                        $stmt->execute();

                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {

                            echo "<div class='single-item'>
                                <h4> {$row['name']}</h4>
                                <a href='mailto:{$row['email']}'>{$row['email']}</a>
                                <p>{$row['comment']}</p>
                            </div>";

                        }

                        ?>

                    </div>

                </div>
            </div>
        </div>
        <!-- // Main Content -->

        <!-- Sidebar -->
        <div class="sidebar single">
            <div class="section popular">
                <h2 class="section-title">Popular</h2>
                <?php foreach ($posts as $p): ?>
                    <div class="post clearfix">
                        <img src="<?php echo BASE_URL . '/assets/images/' . $p['image']; ?>" alt="">
                        <a href="" class="title">
                            <h4><?php echo $p['title'] ?></h4>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section topics">
                <h2 class="section-title">Topics</h2>
                <ul>
                    <?php foreach ($topics as $topic): ?>
                        <li><a href="<?php echo BASE_URL . '/index.php?t_id=' . $topic['id'] . '&name=' . $topic['name'] ?>"><?php echo $topic['name']; ?></a></li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
    </div>
    <!-- // Content -->

</div>
<!-- // Page Wrapper -->

<?php include(ROOT_PATH . "/app/includes/footer.php"); ?>

</body>
</html>