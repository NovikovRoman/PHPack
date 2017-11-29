<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPack\CssPack;
use PHPack\JsPack;

?>
<html>
<head>
    <?php
    $isProduction = false; // or true
    $cp = new CssPack(
        __DIR__ . '/scss/', // абсолютный путь к файлам scss
        __DIR__ . '/dist/css/' // абсолютный путь к сборкам css
    );
    $cp->setProduction($isProduction);
    // путь к скриптам для web-страниц
    $cp->setRelativeWebPath('/css/');
    try {
        $pathCss = $cp->compileCrunched('personal');
        $css = $cp->compileCrunched('admin', true);
    } catch (\Exception $e) {
        die($e->getMessage());
    }

    $jp = new JsPack(
        __DIR__ . '/js/', // абсолютный путь к файлам js
        __DIR__ . '/dist/js/' // абсолютный путь к сборкам js
    );
    $jp->setProduction($isProduction);
    // путь к скриптам для web-страниц
    $jp->setRelativeWebPath('/js/');
    try {
        $pathJsCommon = $jp->compileCrunched('common');
        $pathJsAdmin = $jp->compileCrunched('admin');
        $contentJsMain = $jp->setObfuscation(true)->compileCrunched('main', true);
    } catch (\Exception $e) {
        die($e->getMessage());
    }
    ?>
	<link type="text/css" href="<?= $pathCss ?>">
	<style><?= $css ?></style>
</head>
<body>
<h1>webpage</h1>
<script src="<?= $pathJsCommon ?>"></script>
<script src="<?= $pathJsAdmin ?>"></script>
<script><?= $contentJsMain ?></script>
</body>
</html>

