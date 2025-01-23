<?php
    require_once "../../vendor/connect.php";

    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
    }

    require_once "../../vendor/updateinfo.php";

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
        <title>Mapperter: Поиск</title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            html {
                color: white;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            body {
                background-image: url('/img/backgrounds/default.png');
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
            .searching {
                display: flex;
                width: 100%;
                justify-content: center;
            }
            .user-section {
                padding: 25px;
                display: flex;
                justify-content: center;
                border-radius: 20px;
            }
            .user-section .user {
                border-radius: 20px;
                background-color: #1a1a1a;
                width: 400px;
                text-align: center;
            }
            .user-section .user a {
                display: inline-block;
                color: white;
            }
            form button {
                border: none;
                max-height: 25px;
                height: auto;
                border-radius: 10px;
                cursor: pointer;
            }
            form input {
                height: 20px;
                border: none;
                border-radius: 8px;
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
                <h1>Поиск</h1>
            </div>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $username; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/signout">Выйти</a>
            </div>
        </div>
        
        <div class="searching">
            <form method="GET" action="/network/main/search">
                <label for="search">Введите имя пользователя:</label><br>
                <input type="text" name="search" placeholder="Логин или имя" id="search" required>
                <button type="submit" style="width: auto;"><img src="/img/loupe.png" style="max-height: 15px; max-width: 15px; transform: rotate(-45deg);" /></button>
        <?php
            if (isset($_GET['search'])) {
                $searchTerm = trim($_GET['search']);
                $searchEncoded = base64_encode($searchTerm);
                $searchTerm = mysqli_real_escape_string($connect, $searchTerm);
                $query = "SELECT * FROM Accounts WHERE name LIKE '%$searchEncoded%' OR username LIKE '%$searchTerm%'";
            } else {
                $query = "SELECT * FROM Accounts ORDER BY id";
            }
            $result = mysqli_query($connect, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<h2>Результаты поиска:</h2>";
                echo '</form>';
                echo '</div>';
                while ($user = mysqli_fetch_assoc($result)) {
                    echo '<div class="user-section">';
                    echo '<div class="user">';

                    $premium = mysqli_query($connect, "SELECT * FROM Premium WHERE username = '" . $user['username'] . "'");
                    echo '<h3><a href=/network/user/' . htmlspecialchars($user['username']);
                    if (mysqli_num_rows($premium) > 0) {
                        echo ' style="color: yellow;"';
                    }
                    echo '>@' . htmlspecialchars($user['username']) . '</a>';
                    if (mysqli_num_rows($premium) > 0) {
                        echo '<img src="/img/icons/premium.png" style="max-height: 20px;" />';
                    }
                    if (($user['state'] == "admin") or ($user['state'] == "mod") or ($user['state'] == "approved") or ($user['state'] == "developer")) {
                        echo '<img src="/img/icons/' . htmlspecialchars($user['state']) . '.png" style="max-height: 20px;" />';
                    }
                    echo '</h3>';

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>Пользователи не найдены.</p>";
            }
        ?>
        <div class="header">
            <footer>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>