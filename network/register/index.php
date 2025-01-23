<?php
    //http_response_code(403);
    //echo 'Технические работы!';
    //exit();

    require_once '../vendor/antinull.php';

    function get_ip($default = '')
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $value = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $value = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $value = $_SERVER['REMOTE_ADDR'];
        } else {
            return $default;
        }
        return $value;
    }

    $ip = get_ip();

    echo '<script>console.log("' . $ip . '");</script>';

    session_start();
    if (isset($_SESSION['user'])) {
        header("Location: /network/main");
    }
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png">
        <title>Mapperter: Регистрация</title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            .header {
                background-color: rgba(0, 0, 0, 0)fff; 
                padding: 20px;
                border-radius: 10px;
                text-align: center;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            .header__logo {
                font-size: 24px;
                font-weight: bold;
                color: #fff; 
                margin-bottom: 10px;
            }
            .header__nav {
                margin: auto;
                justify-content: space-between;
            }
            body {
                background-image: url('/img/backgrounds/default.png');
                background-repeat: no-repeat;  
                background-position: center;
                margin: 0; 
                padding: 0; 
                font-family: Arial, sans-serif; 
            }
            form {
                background-color: #fff;
                display: block;
                padding: 1rem;
                width: auto;
                max-width: 400px;
                border-radius: 0.5rem;
                margin: 40px auto;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            form p {
                text-align: center;
            }
            form h2 {
                font-size: 1.25rem;
                line-height: 1.75rem;
                font-weight: 600;
                text-align: center;
                color: #000;
            }
            .input-container {
                text-align: center;
                display: flex;
                justify-content: center;
            }
            .input-container input,
            form button {
                outline: none;
                border: 1px solid #e5e7eb;
                margin: 8px;
            }
            .input-container input {

                display: block;
                justify-content: center;
                                padding-top: 0.75rem;
                                                padding-bottom: 0.75rem;
                                                                padding-left: 1.25rem;
                                                                                padding-right: 1.25rem;
                background-color: #fff;
                
                font-size: 0.875rem;
                line-height: 1.25rem;
                width: 325px;
                border-radius: 0.5rem;
                transition: background-color 0.4s;            }
            form button {
                text-align: center;
                display: block;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
                padding-left: 1.25rem;
                padding-right: 1.25rem;
                background-color: #4F46E5;
                color: #ffffff;
                font-size: 0.875rem;
                line-height: 1.25rem;
                font-weight: 500;
                width: 366px;
                border-radius: 0.5rem;
                text-transform: uppercase;
                transition: background-color 0.4s;
                cursor: pointer;
            }
            form button:hover {
                background-color: #FF46E5;
            }
            .register-btn {
                padding: 10px 0;
                text-align: center;
                display: flex;
                justify-content: center;
            }
            .signup-link {
                color: #6B7280;
                font-size: 0.875rem;
                line-height: 1.25rem;
                text-align: center;
            }
            .signup-link a {
                text-decoration: underline;
            }
            footer p {
                color: white;
            }
            footer p a {
                color: #f80000;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="header__logo">Добро пожаловать в Mapperter!</div>
            <div class="header__logo">Это социальная Pocket Code сеть нового поколения!</div>
        </div>
        <form action="/network/vendor/signup.php" method="POST">
            <h2>Регистрация на сайте</h2>
            <div class="input-container">
                <input type="text" id="name" name="name" maxlength="32" required placeholder="Введите ваше имя">
            </div>
            <div class="input-container">
                <input type="text" id="username" name="username" maxlength="16" required placeholder="Введите ваш логин">
            </div>
            <div class="input-container">
                <input type="email" id="email" name="email" maxlength="32" required placeholder="Введите ваш email">
            </div>
            <div class="input-container">
                <input type="password" id="password" name="password" minlength="8" maxlength="16" required autocomplete="new-password" placeholder="Введите ваш пароль">
            </div>
            <div class="register-btn">
                <button type="submit">Зарегистрироваться</button>
            </div>
            <?php session_start(); ?>
            <p><?php echo $_SESSION["message"]; ?></p>
            <?php unset($_SESSION["message"]); ?>
            <p class="signup-link">Уже есть аккаунт? Войдите <a href="/network/login">Здесь!</a></p>
        </form>
        <div class="header">
            <footer>
                <p>Помните о <a href="/network/rules">Правилах платформы</a>!</p>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>