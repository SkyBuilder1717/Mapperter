<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/antinull.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
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

    if (((!($_SESSION['user']['username'] == $post['author'])) and !(($_SESSION['user']['state'] == "admin") or ($_SESSION['user']['state'] == "developer"))) or ($_SESSION['user']['state'] == "not_verified") or ($_SESSION['user']['state'] == "banned")) {
        header("Location: /network/main");
        exit();
    }

    if (mysqli_num_rows($result) == 0) {
        header("Location: /network/main");
        exit();
    }

    $emojiFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='emoji' AND username='$username'");
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        if (strlen($name) == 0) {
            $name = 'Пост';
        } else {
            $name = limitString($name, 32);
        }
        $desc = limitString($_POST['description'], 255);
        if (mysqli_num_rows($emojiFeature) == 0) {
            $name = removeEmojis($name);
            $desc = removeEmojis($desc);
        }
        $name = base64_encode($name);
        $desc = base64_encode($desc);

        $query = "UPDATE Posts SET name='$name',description='$desc',is_changed='1' WHERE id='$id'";
        mysqli_query($connect, $query);
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
        <title>Mapperter: Редактирование</title>
        <style>
            body, html {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                background-image: url('/img/backgrounds/default.png');
                background-size: cover;
                text-shadow: 2px 4px 4px rgba(0, 0, 0, 0.7);
                font-family: Arial, sans-serif;
                color: white;
            }
            .header {
                color: white;
                text-align: left;
                padding: 15px;
                width: 100%;
                font-size: 24px;
                font-weight: bold;
            }
            .header .p2 {
                font-size: 20px;
                width: 40%;
            }
            .cookie-banner {
                background-color: #333;
                color: white;
                padding: 10px;
                position: fixed;
                bottom: 0;
                width: 99%;
                display: none;
                justify-content: space-between;
                align-items: center;
            }
            .cookie-banner button {
                background-color: #007bff;
                border: none;
                color: white;
                padding: 5px 0;
                cursor: pointer;
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
            .nav-buttons a:hover {
                color: red;
            }
            .menu {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 100px 0;
                width: 100%;
                height: 50%;
            }
            form {
                text-align: center;
                background-color: #ddd;
                border-radius: 25px;
                width: 230px;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            .btn-group {
                padding: 15px 0;
            }
            form label {
                color: black;
            }
            form button {
                border: none;
                border-radius: 20px;
                font: 1.2rem "Fira Sans", sans-serif;
                text-align: center;
                font-size: 19px;
                color: white;
                width: 220px;
                cursor: pointer;
                height: 30px;
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
                <h1>Редактирование поста</h1>
            </div>
            <div class="nav-buttons">
                <a href="/network/main">Домой</a>
                <a href="/network/main/search">Поиск</a>
                <a href="/network/user?username=<?php echo $_SESSION['user']['username']; ?>">Профиль</a>
                <a href="/network/vendor/signout.php">Выйти</a>
            </div>
        </div>

        <div class="menu">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Название:</label><br>
                    <input type="text" name="name" value="<?php echo htmlspecialchars(base64_decode($post['name'])); ?>" maxlength="32" required><br>
                    <label for="description">Описание:</label><br>
                    <textarea name="description" style="width: 220px; height: 70px;" maxlength="255" ><?php echo htmlspecialchars(base64_decode($post['description'])); ?></textarea><br>
                    <label for="files">Файлы:</label><br>
                    <input type="text" name="files" value="<?php echo htmlspecialchars($post['files']); ?>" readonly /><br>
                </div>
                <div class="form-group">
                    <div class="btn-group">
                        <button type="submit" class="submit-button">Сохранить изменения</button><br>
                    </div>
                </div>
            </form>
        </div>

        <div class="header">
            <footer>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>

        <div class="cookie-banner" id="cookieBanner">
            <span>Этот сайт использует cookies для улучшения опыта пользователей.</span>
            <button onclick="acceptCookies()">Принять</button>
        </div>
        <script>
            function checkCookies() {
                if (!localStorage.getItem('cookiesAccepted')) {
                    document.getElementById('cookieBanner').style.display = 'flex';
                }
            }
            function acceptCookies() {
                localStorage.setItem('cookiesAccepted', 'true');
                document.getElementById('cookieBanner').style.display = 'none';
            }
            document.addEventListener('DOMContentLoaded', checkCookies);
        </script>
    </body>
</html>
