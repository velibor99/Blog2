<?php include("path.php"); ?>
<?php include(ROOT_PATH . '/app/controllers/posts.php');

$topics = selectAll('topics');
$posts = selectAll('posts', ['published' => 1]);


$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'blog';

$conn = new MySQLi($host, $user, $pass, $db_name);

error_reporting(0); // For not showing any error



if (isset($_POST['submit'])) { // Check press or not Post Comment Button FOR ADDING IN DATABASE
    $name = $_POST['name']; // Get Name from form
    $email = $_POST['email']; // Get Email from form
    $comment = $_POST['comment']; // Get Comment from form
    $Post_id = $_POST['comment-id'];

    $sql = "INSERT INTO comments (name, email, comment, Post_id) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $name, $email, $comment, $Post_id);
    $success = $stmt->execute();

    if ($success) {
        echo "<script>alert('Comment added successfully.')</script>";
    } else {
        echo "<script>alert('Comment does not add.')</script>";
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
                    <form action="" method="POST" class="row">
                        <div class="row">
                            <div>
                                <input type="text" name="name" id="name" placeholder="Enter your Name" required>
                            </div>
                            <div >
                                <input type="email" name="email" id="email" placeholder="Enter your Email" required>
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