<?php
    require_once '../vendor/connect.php';
    require_once '../vendor/antinull.php';
    session_start();
    require_once '../vendor/mailclass.php';

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }

    require_once '../vendor/updateinfo.php';

    $user = $_SESSION['user'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = base64_encode($_POST['name']);
        $description = base64_encode(htmlspecialchars($_POST['description']));
        $email = $user['email'];

        $limitCheck = mysqli_query($connect, "SELECT * FROM Reports WHERE email='$email'");
        if (mysqli_num_rows($limitCheck) > 2) {
            header("Location: /network/report");
            $_SESSION['message'] = "У вас уже есть 3 сделанных репорта!<br>Попробуйте позже.";
            exit();
        }
        
        $emailQuery = mysqli_query($connect, "SELECT * FROM Accounts WHERE email='$email'");
        if (mysqli_num_rows($emailQuery) == 0) {
            header("Location: /network/report");
            $_SESSION['message'] = "Аккаунта с такой почтой не существует!";
            exit();
        }

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $fl = $_FILES['file'];
            $flNN = generateString(16) . '.png';
            $flP = 'uploads/' . $flNN;

            move_uploaded_file($fl['tmp_name'], $flP);

            $fil = mysqli_real_escape_string($connect, $flNN);
        } else {
            $fil = "";
        }
        
        $query = "INSERT INTO Reports (email, name, description, files) VALUES ('$email', '$name', '$description', '$fil')";
        mysqli_query($connect, $query);
        $id = mysqli_insert_id($connect);
        header("Location: /network/report");
        $_SESSION['message'] = "Репорт успешно создан!";

        // Письмо об репорте
        $mail = new SendMail('mappercoder@yandex.com', 'efkfcibrrtirlzlx', 'ssl://smtp.yandex.ru', 465, "UTF-8");
        $from = array("mappercoder", "mappercoder@yandex.com");
        $name = base64_decode($name); // Декодирование названия
        $description = base64_decode($description); // Декодирования описания
        $result =  $mail->send($email, "Репорт '$name'", "Наша модерация только что получила запрос об вашем репорте \"$name\"!<br>ID репорта: <i>$id</i><br><br>Содержание было следующее:<br>'<i>$description</i>'.<br><br>Модерация Mapperter уже получила ваш запрос об репорте и скоро всё устранит, то что произошло не так!<br><strong>Ожидайте!<strong>", $from);

        exit();
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=2.0">
        <title>Mapperter: Reports</title>
        <link rel="icon" type="image/png" href="/favicon.png">
        <meta name="robots" content="noindex, nofollow">
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
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            .header {
                max-width: auto;
                color: #fff;
                background-color: rgba(0, 0, 0, 0)fff; 
                border-radius: 10px;
                text-align: center;
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
            .header__extrtanav {
                padding: 14px 0;
                margin: auto;
                justify-content: space-between;
                display: flex;
                font-size: 24px;
                font-weight: bold;
                align-items: center;
                text-align: center;
                max-width: 180px;
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
            form {
                text-align: center;
                justify-content: center;
            }
            .report-box {
                text-shadow: none;
                background-color: #ffffff;
                border: 1px solid #d4d4d4;
                border-radius: 16px;
                padding: 16px;
                width: auto;
                max-width: 900px;
                margin: 20px auto;
                text-align: left;
            }
            input[type="email"],
            input[type="text"] {
                font-size: 16px;
            }
            input[type="email"],
            input[type="text"],
            textarea {
                border-radius: 9px;
                border: 2px solid lightgrey;
            }
            .boxes {
                text-align: center;
            }
            .form-button {
                padding: 7px 0;
            }
            .form-button button {
                border-radius: 5px;
                border: none;
                background-color: red;
                color: white;
                width: 150px;
                height: 30px;
                font-size: 18px;
                cursor: pointer;
            }
            footer {
                width: 100%;
                color: white;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="p2">
                <h1>Репорт модерации</h1>
            </div>
            <?php if (($user['state'] == "mod") or ($user['state'] == "admin") or ($user['state'] == "developer")): ?>
            <div class="header__extrtanav">
                <a class="header__nav-button" href="/network/report/list">Список репортов</a>
            </div>
            <?php endif; ?>
            <div class="header__nav">
                <a class="header__nav-button" href="/network/main">Домой</a>
                <a class="header__nav-button" href="/network/main/search">Поиск</a>
                <a class="header__nav-button" href="/network/user/<?php echo $username; ?>">Профиль </a>
                <a class="header__nav-button" id="logout" href="/network/signout">Выйти</a>
            </div>
        </div>

        <main class="report-box">
            <h1 class="boxes">Сообщите об нарушающем пользователе!</h1>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">Email:</label><br>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" style="width: 230px;" readonly/><br>
                    <label for="name">Название:</label><br>
                    <input type="text" name="name" maxlength="32" style="width: 230px;" required><br>
                    <label for="description">Описание:</label><br>
                    <textarea name="description" maxlength="255" style="width: 230px; height: 120px;" required></textarea><br>
                    <label for="image">Загрузить изображение:</label><br>
                    <input type="file" name="file" id="file" accept="image/png" width="235px"><br>
                </div>
                <div class="form-button">
                    <button type="submit" class="submit-button">Сообщить</button><br>
                </div>
                <p><?php echo $_SESSION["message"]; ?></p><br>
             <?php $_SESSION["message"] = null; ?>
            </form>
        </main>
        
        <div class="header">
            <footer>
                <p>Важно помнить о правилах платформы!</p>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>