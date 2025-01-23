<?php
    require_once "../vendor/checkVAR.php";
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Glitch Projects</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
        <style>
            main {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .file {
                margin: 35px;
                background-color: #24252a;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                align-items: center;
                border-radius: 10px;
                width: 400px;
                height: 360px;
                max-height: 390px;
            }
            .file section .title {
                display: inline-block;
                padding: 10px;
                text-align: center;
            }
            .file img {
                margin-bottom: 10px;
                width: 120px;
                height: 150px;
            }
            .file .downloadbtn {
                width: 180px;
                height: 50px;
                font-size: 25px;
                cursor: pointer;
                color: white;
                background-color: #ADD8E6;
                border-radius: 15px;
                text-shadow: 2px 4px 4px rgba(0, 0, 0, 0.7);
                margin-top: 8px;
            }
            .file .copybtn {
                width: 260px;
                height: 50px;
                font-size: 25px;
                cursor: pointer;
                color: white;
                background-color: #FFD8E6;
                border-radius: 15px;
                text-shadow: 2px 4px 4px rgba(0, 0, 0, 0.7);
                margin-top: 8px;
            }
            .files-container {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-gap: 20px;
                padding: 20px;
            }
            .title {
                color: #fff;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
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
            .header__nav-button { 
                background-color: #333;
                color: #fff;
                border: 16px;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                display: inline-block;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
            }
            .header__nav-button:hover {
                background-image: linear-gradient(to bottom right, #28ebff80, #8400ff80);
            }
            body {
                background-image: url('/img/backgrounds/default.png');
                background-repeat: no-repeat;  
                background-position: center;
                margin: 0;
                padding: 0;
                background-size: cover;
                font-family: Arial, sans-serif;
            }
            .penis {
                font-family: Arial, sans-serif;
                font-weight: bold;
                font-size: 50px;
                text-align: center;
                color: #fff;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            h2 {
                text-align: center;
            }
            footer p {
                color: #fff;
            }
        </style>
        <link rel="icon" type="image/png" href="/favicon.png">
    </head>
    <body>
        <div class="header">
            <div class="header__logo">Glitch Union</div>
            <div class="header__nav">
                <a class="header__nav-button" href="https://www.youtube.com/@Glitch_union">
                    Канал <img src="/img/youtube.png" alt="YouTube" width="16" height="16"  style="vertical-align: middle;">
                </a>
                <button class="header__nav-button" onclick="window.location.href='../index.html'">
                    На главную <img src="/img/home.png" alt="Домой" width="16" height="16" style="vertical-align: middle;">
                </button>
                <button class="header__nav-button" onclick="window.location.href='/network'">
                    Mapperter <img src="/favicon.png" alt="Соцсеть" width="16" height="16" style="vertical-align: middle;">
                </button>
            </div>
        </div>

        <!-- Файлы по дате загрузки -->
        <?php
        $directory = 'uploads';
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));

        $file_dates = [];
        foreach ($files as $file) {
            $file_path = $directory . '/' . $file;
            if (is_file($file_path)) {
                $file_dates[$file] = filectime($file_path);
            }
        }

        asort($file_dates);
        $sorted_files = array_keys($file_dates);
        ?>
        <main>
        <?php if (count($files) > 0): ?>
            <div class="header__nav-button:hover">
                <h1 class="penis">Доступные файлы для скачивания:</h1>
            </div>
            <div class="files-container">
            <?php foreach ($files as $file): ?>
                <?php if ($file != "index.php"): ?>
                    <div class="file">
                        <section id="<?php echo str_replace(" ", "_", htmlspecialchars(pathinfo($file, PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8')); ?>">
                            <h1 class="title"><?php echo htmlspecialchars(pathinfo($file, PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8'); ?></h1>
                        </section>
                        <img class="icon" src="file_icon.png" alt="file">
                        <button class="downloadbtn" onclick="window.location.href='downloading?file=<?php echo urlencode($file); ?>'">Скачать</button>
                        <?php if ($is_admin == true): ?>
                            <button class="copybtn" data-clipboard-text="mappercoder.yzz.me/projects#<?php echo str_replace(" ", "_", htmlspecialchars(pathinfo($file, PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8')); ?>">Скопировать ссылку</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="header__nav-button:hover">
                <p class="penis">Нет доступных файлов.</p>
            </div>
        <?php endif; ?>
        </main>

        <!-- Конец сайта -->
        <div class="header">
            <footer>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>

        <script>
            const clipboard = new ClipboardJS('.copybtn');
            clipboard.on('success', function(e) {
                alert('Ссылка скопирована!');
                console.info(e);
                e.clearSelection();
            });
            clipboard.on('error', function(e) {
                alert('Ошибка копирования!');
                console.error("Error: ", e);
            });
        </script>
    </body>
</html>