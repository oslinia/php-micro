<?php
/**
 * @var string $lang
 */

use function Framework\Http\{url_path, url_for};

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="<?= url_path('style.css') ?>">
</head>
<body>
<p><?= url_for('archive', year: '2025', month: '05', day: '25') ?></p>
</body>
</html>