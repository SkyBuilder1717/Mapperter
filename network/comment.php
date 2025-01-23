<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/updateinfo.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }

    if ($_SESSION['user']['state'] == "not_verified") {
        header("Location: /network/main");
        exit();
    }

    require_once 'vendor/updateinfo.php';

    $id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
    if (!isset($id)) {
        header("Location: /network/main");
        exit();
    }
    $id = mysqli_real_escape_string($connect, $id);
    
    $username = $_SESSION['user']['username'];

    $result = mysqli_query($connect, "SELECT * FROM Posts WHERE id='$id'");
    $post = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) == 0) {
        header("Location: /network/main");
        exit();
    } elseif ($post["is_pinned"]) {
        header("Location: /network/main");
        exit();
    }

    $emojiFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='emoji' AND username='$username'");
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $desc = $_POST['description'];
        if (strlen($desc) == 0) {
            $desc = 'Комментарий!';
        } else {
            $desc = limitString($desc, 255);
        }
        if (mysqli_num_rows($emojiFeature) == 0) {
            $desc = removeEmojis($desc);
        }
        $desc = base64_encode($desc);

        mysqli_query($connect, "INSERT INTO Comments (post_id, username, description) VALUES ('$id', '$username', '$desc')");
        header("Location: /network/main");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Mapperter: Комментирование</title>
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
            .menu {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 100px 0;
                width: 100%;
            }
            form {
                text-align: center;
                background-color: #ddd;
                border-radius: 25px;
                width: 230px;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            form label {
                color: black;
            }
            form .submit {
                padding: 20px 0;                
            }
            form button {
                border: none;
                border-radius: 20px;
                font: 1.2rem "Fira Sans", sans-serif;
                text-align: center;
                font-size: 19px;
                color: white;
                width: 200px;
                cursor: pointer;
                height: 35px;
                background-color: #1f75fe;
            }
            footer {
                width: 100%;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="p2">
                <h1>Создание комментария</h1>
            </div>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $username; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/vendor/signout.php">Выйти</a>
            </div>
        </div>

        <div class="menu">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="description">Текст комментария:</label><br>
                    <textarea name="description" placeholder="Ваш комментарий" style="width: 220px; height: 70px;" maxlength="255" required></textarea><br>
                </div>
                <div class="form-group">
                    <div class="btn-group">
                        <button type="submit" class="submit-button">Опубликовать</button><br>
                    </div>
                </div>
            </form>
        </div>

        <div class="header">
            <footer>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>
