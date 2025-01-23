<?php
    require_once 'vendor/connect.php';
    require_once 'vendor/antinull.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }

    require_once 'vendor/updateinfo.php';

    $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : $_SESSION['user']['username'];
    $username = mysqli_real_escape_string($connect, $username);

    $result = mysqli_query($connect, "SELECT * FROM Accounts WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    if ((!($_SESSION['user']['username'] == $username) and !($_SESSION['user']['state'] == "developer")) or !(isset($user))) {
        header("Location: /network/user/" . $username);
        exit();
    }

    $pic = $user['avatar'];
    $balancec = $user['coins'];

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
    $premium = mysqli_query($connect, "SELECT * FROM Premium WHERE username = '$username' AND expires > NOW()");
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            if (strlen($name) == 0) {
                $name = 'Ноунейм';
            } else {
                $name = limitString($name, 32);
            }
            $password = $_POST['password'];
            $description = $_POST['description'];
            if (mb_strlen($description) > 0) {
                $description = limitString($description, 255);
            }
            $videoMimeTypes = [
                'video/mp4',
                'video/x-msvideo',
                'video/x-flv',
                'video/x-matroska',
                'video/webm',
                'video/ogg',
                'image/gif'
            ];

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $avatar = $_FILES['avatar'];
                $avatarNewName = generateString(16) . '.png';
                $avatarPath = 'vendor/avatar/' . $avatarNewName;

                if (!(in_array(mime_content_type($_FILES['avatar']['tmp_name']), $videoMimeTypes) or (mysqli_num_rows($premium) == 0))) {
                    move_uploaded_file($avatar['tmp_name'], $avatarPath);

                    $av = mysqli_real_escape_string($connect, $avatarNewName);
                } else {
                    $av = $pic;
                }
            } else {
                $av = $pic;
            }
            if (isset($_FILES['wallpaper_image']) && $_FILES['wallpaper_image']['error'] == 0) {
                $wallpapers = $_FILES['wallpaper_image'];
                $wallpapersNewName = $username . '.png';
                $wallpapersPath = '../img/backgrounds/custom/' . $wallpapersNewName;
                if (file_exists($wallpapersPath)) {
                    unlink($dir);
                }

                if (!(in_array(mime_content_type($_FILES['wallpaper_image']['tmp_name']), $videoMimeTypes) or (mysqli_num_rows($premium) == 0))) {
                    move_uploaded_file($wallpapers['tmp_name'], $wallpapersPath);

                    $wp = mysqli_real_escape_string($connect, $wallpapersNewName);
                } else {
                    $wp = $custom_wallpaper;
                }
            } else {
                $wp = $custom_wallpaper;
            }

            if ($password == "") {
                $password = $user['password'];
            } else {
                $password = md5($password);
            }

            if (mysqli_num_rows($emojiFeature) == 0) {
                $name = removeEmojis($name);
                $description = removeEmojis($description);
            }

            $name = base64_encode($name);
            $description = base64_encode($description);

            $query = "UPDATE Accounts SET name='$name', description='$description', avatar='" . $av . "', password='$password' WHERE username='$username'";
            mysqli_query($connect, $query);
            if ((mysqli_num_rows($wallpaperFeature) > 0) or (mysqli_num_rows($premium) > 0)) {
                $wallpaper = $_POST['wallpapers'];
                mysqli_query($connect, "UPDATE `Features: Extra` SET value='$wallpaper' WHERE username='$username'");
            }
            if ((mysqli_num_rows($customWallpaperFeature) > 0) or (mysqli_num_rows($premium) > 0)) {
                mysqli_query($connect, "UPDATE `Features: Custom Wallpapers` SET image='$wp' WHERE username='$username'");
            }
            header("Location: /network/user/$username");
            exit();
        } elseif (isset($_POST['buy-emoji'])) {
            if ((mysqli_num_rows($emojiFeature) == 0) or (mysqli_num_rows($premium) == 0)) {
                $query = "INSERT INTO Features (feature, username) VALUES ('emoji', '$username')";
                if ($balancec >= 25) {
                    mysqli_query($connect, $query);
                    $_SESSION["message"] = "Куплено!";
                    $query = "UPDATE Accounts SET coins=coins-25 WHERE username='$username'";
                    mysqli_query($connect, $query);
                } else {
                    $_SESSION["message"] = "Нехватает тер-койнов!";
                }
            } else {
                $_SESSION["message"] = "У вас уже есть возможность эмодзи!";
            }
        } elseif (isset($_POST['buy-wallpaper'])) {
            if ((mysqli_num_rows($wallpaperFeature) == 0) or (mysqli_num_rows($premium) == 0)) {
                if ($balancec >= 40) {
                    mysqli_query($connect, "INSERT INTO Features (feature, username) VALUES ('wallpaper', '$username')");
                    mysqli_query($connect, "INSERT INTO `Features: Extra` (username, value) VALUES ('$username', 'default')");
                    $_SESSION["message"] = "Куплено!";
                    mysqli_query($connect, "UPDATE Accounts SET coins=coins-40 WHERE username='$username'");
                } else {
                    $_SESSION["message"] = "Нехватает тер-койнов!";
                }
            } else {
                $_SESSION["message"] = "У вас уже есть возможность обоев!";
            }
        } elseif (isset($_POST['buy-customwallpaper'])) {
            if ((mysqli_num_rows($customWallpaperFeature) == 0) or (mysqli_num_rows($premium) == 0)) {
                if ($balancec >= 30) {
                    mysqli_query($connect, "INSERT INTO Features (feature, username) VALUES ('custom_wallpaper', '$username')");
                    mysqli_query($connect, "INSERT INTO `Features: Custom Wallpapers` (username, image) VALUES ('$username', 'default.png')");
                    $_SESSION["message"] = "Куплено!";
                    mysqli_query($connect, "UPDATE Accounts SET coins=coins-30 WHERE username='$username'");
                } else {
                    $_SESSION["message"] = "Нехватает тер-койнов!";
                }
            } else {
                $_SESSION["message"] = "У вас уже есть возможность пользовательских обоев!";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Редактировать профиль</title>
        <style>
            body {
                <?php if (mysqli_num_rows($wallpaperFeature) > 0): ?>
                <?php if (mysqli_num_rows($customWallpaperFeature) > 0): ?>
                <?php if ($wallpapers_thing == 'custom'): ?>
                background-image: url('/img/backgrounds/custom/<?php echo $custom_wallpaper; ?>');
                background-size: contain;
                <?php else: ?>
                background-image: url('/img/backgrounds/<?php echo $wallpapers_thing; ?>.png');
                background-repeat: no-repeat;
                <?php endif; ?>
                <?php else: ?>
                background-image: url('/img/backgrounds/<?php echo $wallpapers_thing; ?>.png');
                background-repeat: no-repeat;
                <?php endif; ?>
                <?php else: ?>
                background-image: url('/img/backgrounds/default.png');
                background-repeat: no-repeat;
                <?php endif; ?>
                margin: 0;
                width: 100%;
                height: 100%;
                font-family: Arial, sans-serif; 
                color: white;
                padding: 0;
            }
            h1, h2, h3, p, a, label {
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            .element {
                padding: 5px 0;
            }
            .avatar {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
            }
            form {
                padding: 15px;
            }
            form input[type="text"],
            form input[type="email"] {
                width: 200px;
                height: 20px;
                box-sizing: border-box;
            }
            .button {
                padding: 10px 15px;
                font-size: 16px;
                <?php if ($wallpapers_thing == 'default'): ?>
                background-color: #007bff;
                <?php elseif ($wallpapers_thing == 'matrix'): ?>
                background-color: lime;
                <?php else: ?>
                <?php if (!($wallpapers_thing == 'custom')): ?>
                background-color: <?php echo $wallpapers_thing; ?>;
                <?php else: ?>
                background-color: #007bff;
                <?php endif; ?>
                <?php endif; ?>
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .button:hover {
                background-color: #0056b3;
            }
            input, textarea {
                border: none;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <form method="post" enctype="multipart/form-data">
            <h1>Редактировать профиль</h1>
            <div class="form-group">
                <div class="element">
                    <label for="avatar">Загрузить аватар:</label><br>
                    <img src="/network/vendor/avatar/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Аватар" class="avatar" /><br>
                    <input type="file" name="avatar" id="avatar" accept="image/png" width="235px"><br>
                </div>
                <div class="element">
                    <label for="name">Имя:</label><br>
                    <input type="text" name="name" value="<?php echo htmlspecialchars(base64_decode($user['name'])); ?>" maxlength="32" required><br>
                <div class="element">
                </div class="element">
                    <label for="description">Описание:</label><br>
                    <textarea name="description" style="width: 225px; height: 50px;" maxlength="255"><?php echo htmlspecialchars(base64_decode($user['description'])); ?></textarea><br>
                </div>
                <div class="element">
                    <label for="password">Пароль:</label><br>
                    <input type="password" name="password" placeholder="Оставьте пустым, чтобы не изменять пароль" minlength="8" maxlength="16" style="width: 280px;"><br>
                </div>
                <div class="element">
                    <label for="email">Email:</label><br>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly /><br>
                </div>
                <?php if ((mysqli_num_rows($wallpaperFeature) > 0) or (mysqli_num_rows($premium) > 0)): ?>
                <div class="element">
                    <label for="wallpapers">Обои:</label><br>
                    <select id="wallpapers" name="wallpapers">
                        <option value="default" <?php if ($wallpapers_thing == 'default') echo 'selected'; ?>>По умолчанию</option>
                        <option value="red" <?php if ($wallpapers_thing == 'red') echo 'selected'; ?>>Красные</option>
                        <option value="green" <?php if ($wallpapers_thing == 'green') echo 'selected'; ?>>Зелёные</option>
                        <option value="matrix" <?php if ($wallpapers_thing == 'matrix') echo 'selected'; ?>>Матрица</option>
                        <?php if ((mysqli_num_rows($customWallpaperFeature) > 0) or (mysqli_num_rows($premium) > 0)): ?>
                        <option value="custom" <?php if ($wallpapers_thing == 'custom') echo 'selected'; ?>>Пользовательские</option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if ((mysqli_num_rows($customWallpaperFeature) > 0) or (mysqli_num_rows($premium) > 0)): ?>
                <div id="customwp" <?php if (!($wallpapers_thing == 'custom')) echo 'style="display:none;"'; ?>>
                    <div class="element">
                        <label for="wallpaper_image">Загрузить обои:</label><br>
                        <input type="file" name="wallpaper_image" id="wallpaper_image" accept="image/png"><br>
                    </div>
                </div>
                <script>
                    document.getElementById('wallpapers').addEventListener('change', function () {
                        var style = this.value == "custom" ? 'block' : 'none';
                        document.getElementById('customwp').style.display = style;
                    });
                </script>
                <?php endif; ?>
            </div>
            <div class="form-button">
                <button type="submit" name="update" value="update" class="button">Сохранить изменения</button><br>
            </div>
            <p>Тер-койны: <?php echo htmlspecialchars($balancec); ?></p>
            <?php if (mysqli_num_rows($premium) == 0): ?>
            <h1>Магазин</h1>
            <?php if (mysqli_num_rows($emojiFeature) == 0): ?>
            <div class="form-button">
                <button type="submit" name="buy-emoji" value="buy" class="button" title="Вы сможете вставлять в описание и имя любые эмодзи!&#013;(ЦЕНА: 25 тер-койнов)">Купить эмодзи</button><br>
            </div>
            <?php else: ?>
            <p>Эмодзи были куплены!</p>
            <?php endif; ?>
            <?php if (mysqli_num_rows($wallpaperFeature) == 0): ?>
            <div class="form-button">
                <button type="submit" name="buy-wallpaper" value="buy" class="button" title="Вы сможете изменять свои обои на вашем профиле.&#013;Эти обои также увидят все, кто зайдут на Ваш профиль!&#013;(ЦЕНА: 40 тер-койнов)">Купить обои</button><br>
            </div>
            <?php else: ?>
            <p>Обои были куплены!</p>
            <?php if (mysqli_num_rows($customWallpaperFeature) == 0): ?>
            <div class="form-button">
                <button type="submit" name="buy-customwallpaper" value="buy" class="button" title="Теперь вы сможете ставить своё изображение в качество обоев на профиле!&#013;(ЦЕНА: 30 тер-койнов)">Купить пользовательские обои</button><br>
            </div>
            <?php else: ?>
            <p>Пользовательские Обои были куплены!</p>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
        </form>
        <p><?php echo $_SESSION["message"]; ?></p><br>
        <?php $_SESSION["message"] = null; ?>
    </body>
</html>
