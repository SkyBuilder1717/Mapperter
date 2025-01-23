<?php
    require_once "../../network/vendor/connect.php";
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }
    
    function convertLinksToHTML($text) {
        $pattern = '/(http|https):\/\/[a-zA-Z0-9.-_\/?=&:#@]*/';
        $userpattern = '/@([a-zA-Z0-9_]+)/';
        $text = preg_replace($pattern, '<a class="link" href="$0">$0</a>', $text);
        $text = preg_replace($userpattern, '<a class="username" href="/network/user/$1">@$1</a>', $text);
        return nl2br($text);
    }
    
    require_once "../../network/vendor/updateinfo.php";

    $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';

    $result = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);
    $userna = $_SESSION['user']['username'];

    $premium = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM Premium WHERE username='$username'"));
    
    $emojiFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='emoji' AND username='$username'");
    $wallpaperFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='wallpaper' AND username='$username'");
    $customWallpaperFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='custom_wallpaper' AND username='$username'");
    $wallpaperFeature_value = mysqli_query($connect, "SELECT * FROM `Features: Extra` WHERE username='$username'");
    $customWallpaperFeature_value = mysqli_query($connect, "SELECT * FROM `Features: Custom Wallpapers` WHERE username='$username'");

    if (mysqli_num_rows($customWallpaperFeature_value) > 0) {
        $custom_wallpaper = mysqli_fetch_assoc($customWallpaperFeature_value)['image'];
    } else {
        $custom_wallpaper = 'default.png';
    }
    if (mysqli_num_rows($wallpaperFeature_value) > 0) {
        $wallpapers_thing = mysqli_fetch_assoc($wallpaperFeature_value)['value'];
    } else {
        $wallpapers_thing = 'default';
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Профиль пользователя</title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            body {
                <?php if (mysqli_num_rows($wallpaperFeature) > 0): ?>
                <?php if (mysqli_num_rows($customWallpaperFeature) > 0): ?>
                <?php if ($wallpapers_thing == 'custom'): ?>
                background-image: url('/img/backgrounds/custom/<?php echo $custom_wallpaper; ?>');
                background-size: contain;
                <?php else: ?>
                background-image: url('/img/backgrounds/<?php echo $wallpapers_thing; ?>.png');
                background-size: cover;
                <?php endif; ?>
                <?php else: ?>
                background-image: url('/img/backgrounds/<?php echo $wallpapers_thing; ?>.png');
                background-size: cover;
                <?php endif; ?>
                <?php else: ?>
                background-image: url('/img/backgrounds/default.png');
                background-size: cover;
                <?php endif; ?>
                margin: 0;
                width: auto;
                height: 100%;
                padding: 0; 
                font-family: Arial, sans-serif; 
                text-shadow: 2px 4px 4px rgba(0, 0, 0, 0.7);
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
                <?php if ($wallpapers_thing == 'default'): ?>
                background-image: linear-gradient(to bottom right, #28ebff80, #8400ff80);
                <?php elseif ($wallpapers_thing == 'matrix'): ?>
                background-image: linear-gradient(to bottom right, #00ff00, #4dff4d99);
                <?php elseif ($wallpapers_thing == 'custom'): ?>
                background-color: #fff;
                <?php else: ?>
                background-color: <?php echo $wallpapers_thing; ?>;
                <?php endif; ?>
            }
            .header__nav-button[id="logout"]:hover {
                background-image: linear-gradient(to bottom right, #ff0000, #a1a1a1);
            }
            .info-container {
                display: flex;
                justify-content: center;
            }
            .info {
                text-align: center;
                color: white;
            }
            .fladder {
                display: flex;
                justify-content: center;
                background-color: #a1a1a1;
                color: white;
                text-align: left;
                padding: 15px;
                width: auto;
                height: auto;
                max-width: 100%;
                max-height: 100px;
                font-size: 24px;
                font-weight: bold;
            }
            .nav-buttons {
                width: 100%;
                align-items: right;
                text-align: right;
            }
            .nav-buttons a {
                display: inline-block;
                color: white;
                padding: 5px 25px;
                margin: 5px 10px;
                text-decoration: none;
                border-radius: 5px;
                transition: color 0.3s;
            }
            .fladder p {
                display: inline-block;
                text-align: left;
                align-items: left;
                width: 55%;
                height: 20px;
            }
            .nav-buttons a:hover {
                color: red;
            }
            .fladder .avatar {
                justify-content: left;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
            }
            .post .avatar img[name="avatar"] {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
            }
            .fladder .elements .icon {
                display: inline-block;
            }
            .desc {
                font-size: 20px;
                width: 275px;
            }
            main {
                display: flex;
                width: 100%;
                justify-content: center;
            }
            button[id="edit"],
            button[name="unban"],
            button[name="ban"],
            button[id="makepost"],
            button[id="promocode"] {
                border-radius: 10px;
                border: none;
                height: 22px;
                width: 170px;
            }
            .form-btn {
                padding: 10px;
            }
            button[name="ban"] {
                background-color: #ff0000;
            }
            button[id="edit"],
            button[name="unban"] {
                background-color: #ffdb3d;
            }
            button[id="makepost"] {
                background-color: #ddd;
            }
            button[id="promocode"] {
                background-color: #0ff;
            }
            button:hover {
                cursor: pointer;
            }
            footer {
                width: 100%;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <?php
            if (!$user) {
                http_response_code(404);
                echo "<h1 style='color: red;'>Пользователь не был найден.</h1>";
                exit();
            }
            if ($user['state'] == "mod") {
                $statetitle = "Модератор";
            } elseif ($user['state'] == "admin") {
                $statetitle = "Администратор";
            } elseif ($user['state'] == "approved") {
                $statetitle = "Доверенный пользователь";
            } elseif ($user['state'] == "developer") {
                $statetitle = "Разработчик";
            }
        ?>
        <div class="header">
            <div class="p2">
                <h1>Пользователь <?php echo htmlspecialchars($user['username']); ?></h1>
            </div>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $_SESSION['user']['username']; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/signout">Выйти</a>
            </div>
        </div>

        <div class="fladder">
            <img src="/network/vendor/avatar/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Аватар" class="avatar" />
            <div class="elements">
                <p <?php if ($premium > 0): echo 'style="color: yellow;"'; endif; ?>><?php echo htmlspecialchars(base64_decode($user['name'])); ?> (@<?php echo htmlspecialchars($user['username']); ?>)
                <?php if ($premium > 0): ?>
                    <img src="/img/icons/premium.png" title="Premium подписка" alt="Premium значок" style="max-height: 20px;" class="icon" />
                <?php endif; ?>
                <?php if (!($user['state'] == "not_verified" or $user['state'] == "banned" or $user['state'] == "user")): ?>
                    <img src="/img/icons/<?php echo htmlspecialchars($user['state']); ?>.png" title="<?php echo $statetitle; ?>" alt="<?php echo htmlspecialchars($user['state']); ?>" style="max-height: 20px;" class="icon" />
                <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="info-container">
        <div class="info">
        <?php if (($user['username'] == $_SESSION['user']['username']) or ($_SESSION['user']['state'] == "developer")): ?>
            <?php if (!($user['state'] == "not_verified" or $user['state'] == "banned")): ?>
                <form class="form-btn" action="/network/editprofile/<?php echo htmlspecialchars($user['username']); ?>" method="GET">
                    <button type="submit" id="edit">Редактировать профиль</button>
                </form>
                <?php if ($user['username'] == $_SESSION['user']['username']): ?>
                    <form class="form-btn" action="/network/main/post" method="GET">
                        <button type="submit" id="makepost">Сделать пост</button>
                    </form>
                    <form class="form-btn" action="/network/coins" method="GET">
                        <button type="submit" id="promocode">Промокоды</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (((($_SESSION['user']['state'] == "developer" or $_SESSION['user']['state'] == "admin") and ($user['state'] == "mod" or $user['state'] == "user" or $user['state'] == "not_verified")) or ($_SESSION['user']['state'] == "mod" and ($user['state'] == "user"))) and !($user['username'] == $_SESSION['user']['username'])): ?>
            <form class="form-btn" action="" method="POST">
                <button type="submit" name="ban" value="ban">Забанить пользователя</button>
            </form>
        <?php elseif (($_SESSION['user']['state'] == "developer" or $_SESSION['user']['state'] == "admin" or $_SESSION['user']['state'] == "mod") and $user['state'] == "banned"): ?>
            <form class="form-btn" action="" method="POST">
                <button type="submit" name="unban" value="unban">Разбанить пользователя</button>
            </form>
        <?php endif; ?>
        
        <?php if ($user['state'] == "banned"): ?>
        <h2>Пользователь забанен.</h2>
        <?php elseif ($user['state'] == "not_verified"): ?>
        <!-- Nothing here -->
        <?php else: ?>
        <?php if (!(strlen($user['description']) == 0)): ?>
        <h2>Описание:</h2>
        <p class="desc"><?php echo convertLinksToHTML(htmlspecialchars(base64_decode($user['description']))); ?></p>
        </div>
        </div>
        <?php endif; ?>

        <main>
            <div class="posts">
            <?php
                $is_user_profile = true;
                require_once "../../network/vendor/getposts.php";
            ?>
            </div>
        </main>
        <?php endif; ?>

        <div class="header">
            <footer>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>