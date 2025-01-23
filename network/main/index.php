<?php
    require_once "../vendor/connect.php";
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
    }

    require_once "../vendor/updateinfo.php";

    $name = base64_decode($_SESSION['user']['name']);
    $username = $_SESSION['user']['username'];
    $state = $_SESSION['user']['state'];
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <meta name="robots" content="noindex, nofollow">
        <title>Mapperter</title>
        <style>
            body {
                background-image: url('/img/backgrounds/default.png');
                background-repeat: no-repeat;
                background-size: cover;
                margin: 0;
                width: auto;
                height: 100%;
                padding: 0; 
                font-family: Arial, sans-serif; 
            }
            .nav-buttons {
                align-items: left;
                text-align: left;
            }
            .header {
                max-width: auto;
                color: #fff;
                background-color: rgba(0, 0, 0, 0)fff; 
                border-radius: 10px;
                text-align: center;
            }
            .header,
            .extra-buttons a {
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            .header__logo {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .header__nav {
                padding: 14px 0;
                margin: auto;
                justify-content: space-between;
                display: flex;
                font-size: 24px;
                font-weight: bold;
                align-items: center;
                text-align: center;
                max-width: 540px;
                width: auto;
                border-radius: 20px;
            }
            .header__nav-button {
                display: flex;
                align-items: center;
                text-align: center;
                background-color: #333;
                color: #fff;
                border: 16px;
                font-size: 16px;
                cursor: pointer;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                text-decoration: none;
            }
            .header__nav-button:last-child {
                margin-right: 0;
            }
            .header__nav-button:hover {
                background-image: linear-gradient(to bottom right, #28ebff80, #8400ff80);
            }
            .header__nav-button[id="logout"]:hover {
                background-image: linear-gradient(to bottom right, #ff0000, #a1a1a1);
            }
            .extra-buttons { 
                padding: 14px 0;
                margin: auto;
                justify-content: space-between;
                display: flex;
                font-size: 24px;
                font-weight: bold;
                align-items: center;
                text-align: center;
                max-width: 350px;
                width: auto;
                border-radius: 20px;
            }
            .extra-buttons .extra-button {
                border: 16px;
                padding: 10px 20px;
                font-size: 16px;
                border-radius: 5px;
                background-color: #003333;
                color: white;
                text-decoration: none;
                font-weight: bold;
            }
            .extra-buttons a:hover[id="post"] {
                background-image: linear-gradient(to bottom right, #0ff, #00f);
            }
            .extra-buttons a:hover[id="report"] {
                background-image: linear-gradient(to bottom right, #ff0000, #a1a1a1);
            }
            .nav-buttons a,
            .sub-btn a {
                display: inline-block;
                color: white;
                padding: 5px 25px;
                margin: 5px 10px;
                border-radius: 5px;
                transition: color 0.3s;
            }
            .nav-buttons a:hover,
            .sub-btn a:hover {
                color: red;
            }
            main {
                display: flex;
                width: 100%;
                justify-content: center;
            }
            .posts {
                padding: 35px;
                max-width: 380px;
                width: auto;
            }
            .post {
                margin: 50px 0;
                padding: 20px;
                width: auto;
                max-width: 390px;
                height: auto;
                max-height: none;
                box-shadow: 0 7px 5px rgba(0, 0, 0, 0.3);
                border-radius: 20px;
                color: white;
                border: none;
                background-color: #1a1a1a;
            }
            .post a {
                color: white;
                text-decoration: none;
                text-align: left;
            }
            .post p .link {
                text-decoration: underline;
                color: #00f;
            }
            .post p .username {
                text-decoration: none;
                color: #0cf;
            }
            .post .avatar p {
                padding: 0 14px;
            }
            .post .files img,
            .post .files video {
                max-width: 350px;
            }
            .post .files a {
                text-decoration: underline;
                color: #0ff;
            }
            .post .avatar {
                display: flex;
                justify-content: left;
            }
            .post button {
                display: flex;
                text-align: right;
                align-items: right;
                justify-content: right;
                padding: 3px;
                border-radius: 15px;
                cursor: pointer;
                border: none;
                height: 22px;
                width: auto;
            }
            .post button[name="edit-post"] {
                width: 115px;
            }
            .post button[id="comment"] {
                width: 140px;
            }
            .post .buttons form {
                padding: 5px 0;
            }
            footer {
                width: 100%;
                text-align: center;
            }
            .comments {
                padding: 10px 0;
            }
            .comments .avatar {
                padding: 5px;
                text-align: left;
                align-items: left;
                justify-content: left;
                height: auto;
                width: auto;
                max-width: 80%;
                max-height: none;
            }
            .comments p {
                padding: 0 10px;
                color: white;
                width: 350px;
            }
            .comments .avatar a {
                display: flex;
                color: white;
                padding: 0 10px;
                font-size: 15px;
                justify-content: space-between;
                font-weight: bold;
            }
            .comments .comment {
                border-radius: 6px;
                border: 1px solid #fff;
                padding: 10px 0;
                width: auto;
            }
            footer p a {
                color: #f80000;
            }
            .delete-comment-btn {
                width: 100%;
                margin-right: 10px;
                display: flex;
                text-align: right;
                align-items: right;
                justify-content: right;
            }
            .delete-comment-btn button {
                display: inline-block;
                text-align: center;
                background-color: #fcc;
                border-radius: 5px;
                width: auto;
                height: 20px;
            }
            .delete-comment-btn button img {
                display: flex;
                text-align: center;
                align-items: center;
                justify-content: center;
            }
            .buttons .likes {
                height: 20px;
                width: 100%;
                display: inline-block;
            }
            .buttons .likes button {
                display: inline-block;
            }
            .buttons p {
                display: flex;
                text-align: right;
                align-items: right;
                justify-content: right;
            }
            .post .avatar img[name="avatar"] {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="p2">
                <h1>Добро пожаловать, <?php echo htmlspecialchars($name); ?>!</h1>
            </div>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $username; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/signout">Выйти</a>
            </div>
        </div>

        <?php if (!($_SESSION['user']['state'] == "not_verified" or $_SESSION['user']['state'] == "banned")): ?>
            <div class="extra-buttons">
                <a class="extra-button" href="/network/main/post" id="post">Сделать пост</a>
                <a class="extra-button" href="/network/report" id="report">Сообщить</a>
            </div>
        <?php endif; ?>

        <main>
            <div class="posts">
            <?php
                $is_user_profile = false;
                require_once '../vendor/getposts.php';
            ?>
            </div>
        </main>

        <div class="header">
            <footer>
                <p>Помните о <a href="/network/rules">Правилах платформы</a>!</p>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>