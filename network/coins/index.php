<?php
    require_once "../vendor/connect.php";
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }

    function getCoinsText($count) {
        if ($count % 10 == 1 && $count % 100 != 11) {
            return "тер-койн";
        } elseif ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20)) {
            return "тер-койна";
        } else {
            return "тер-койнов";
        }
    }

    require_once "../vendor/updateinfo.php";

    $name = base64_decode($_SESSION['user']['name']);
    $username = $_SESSION['user']['username'];
    $coins = $_SESSION['user']['coins'];

    date_default_timezone_set('Europe/Moscow');

    $premium = "выключена";
    $is_prem = 0;
    $query = "SELECT * FROM Premium WHERE username = '$username' AND expires > NOW()";
    $result = mysqli_query($connect, $query);
    $prem_exp = mysqli_fetch_assoc($result)["expires"];
    if (mysqli_num_rows($result) > 0) {
        $premium = "включена";
        $is_prem = 1;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = mysqli_real_escape_string($connect, $_POST['promocode']);

        $query = "SELECT * FROM Promocodes WHERE code = '$code' AND expiration > NOW()";
        $result = mysqli_query($connect, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $promo = mysqli_fetch_assoc($result);
            $query = "SELECT * FROM Promocodes_USED WHERE promo_id = " . (int)$promo['id'] . " AND username = '" . mysqli_real_escape_string($connect, $username) . "'";
            $used_result = mysqli_query($connect, $query);
            
            if (mysqli_num_rows($used_result) == 0) {
                if ($promo['use_limit'] == -1 || $promo['used'] < $promo['use_limit']) {
                    $query = "INSERT INTO Promocodes_USED (promo_id, username) VALUES (" . (int)$promo['id'] . ", '" . mysqli_real_escape_string($connect, $username) . "')";
                    mysqli_query($connect, $query);

                    $query = "UPDATE Promocodes SET used = used + 1 WHERE id = " . (int)$promo['id'];
                    mysqli_query($connect, $query);

                    $_SESSION['message'] = "Промокод успешно использован!";
                    if ($code == "newmtpremiumesya") {
                        $query = "UPDATE Accounts SET coins = coins + 20 WHERE username = '" . mysqli_real_escape_string($connect, $username) . "'";
                        mysqli_query($connect, $query);
                    }
                } else {
                    $_SESSION['message'] = "Использование промокода было исчерпано.";
                }
            } else {
                $_SESSION['message'] = "Вы уже использовали этот промокод.";
            }
        } else {
            $_SESSION['message'] = "Промокод не существует или истек.";
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Mapperter: Balance</title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            body {
                background-image: url('/img/backgrounds/default.png');
                background-size: cover;
                margin: 0;
                color: white;
                width: auto;
                height: 100%;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
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
            .premium, main {
                display: flex;
                justify-content: center;
                text-align: center;
            }
            main p {
                font-size: 50px;
            }
            .promocode-input form {
                width: 175px;
            }
            .promocode-input input {
                width: 170px;
            }
            .promocode-input button,
            .promocode-input label,
            .promocode-input input {
                border: none;
                border-radius: 10px;
                display: flex;
                justify-content: center;
            }
            .promocode-input button {
                cursor: pointer;
            }
            .promocode-input .enter-btn {
                padding: 7px 0;
                display: flex;
                justify-content: center;
            }
            .promocode-input {
                display: flex;
                justify-content: center;
            }
            footer {
                padding: 150px 0;
            }
            .premium p em {
            <?php if ($is_prem > 0): ?>
                color: yellow;
            <?php else: ?>
                color: red;
            <?php endif; ?>
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="p2">
                <h1>С наилучшими пожеланиями, <?php echo htmlspecialchars($name); ?>!</h1>
            </div>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $username; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/signout">Выйти</a>
            </div>
        </div>

        <main>
            <p>Баланс:<br><em><?php echo $coins; ?></em> <strong><?php echo getCoinsText($coins); ?></strong></p><br>
        </main>

        <div class="promocode-input">
            <form action="" method="post">
                <label for="promocode">Промокод:</label>
                <input type="text" id="promo" name="promocode" required />
                <div class="enter-btn">
                    <button type="submit">Проверить</button>
                </div>
                <?php session_start(); ?>
                <p><?php echo $_SESSION["message"]; ?></p>
                <?php $_SESSION["message"] = null; ?>
            </form>
        </div>

        <div class="premium">
            <p><strong>Premium подписка:</strong> <em><?php echo $premium; ?></em>
            <?php if ($is_prem > 0): ?>
            до <?php echo $prem_exp; ?>
            <?php endif; ?>
            
            </p><br>
        </div>

        <div class="header">
            <footer>
                <p>Помните о <a href="/network/rules">Правилах платформы</a>!</p>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>