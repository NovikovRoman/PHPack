# PHP packer

В src директории стилей создаем файл build.json

Пример,
```json
{
  "common": [
    "3",
    "2",
    "1"
  ],
  "admin": [
    "4",
    "import/5",
    "1"
  ],
  "personal": [
    "3",
    "2",
    "4"
  ]
}
```
где ключи (`common`, `admin`, `personal`) имена файлов для сборки css. Массив - имена файлов `scss`. Перечисление в необходимом порядке сборки.
Расширения не ставить. Предполагается что расширения `scss`.

Использование:
```php
// ...
$isProduction = false; // or true
$cp = new CssPack(
    '/scss/', // абсолютный путь к файлам scss
    '/web/assets/css/' // абсолютный путь к сборкам css
);
$cp->setProduction($isProduction);
// путь к скриптам для web-страниц
$cp->setRelativeWebPath('/assets/css/');
// получим путь к сборке `common`
echo '<link type="text/css" href="' . $cp->compileCrunched('personal') . '">';
echo '<style>' . $cp->compileCrunched('common', true) . '</style>';
// ...
```

Для production проверяется есть ли собранный css-файл и отдает путь к уже готовому файлу.
Для developing файл собирается каждый раз заново. При деплое нужно предусмотреть очистку сборок css для обновления.


Аналогично работает с js (В src директории js создаем файл build.json и тп).
```php
// ...
$isProduction = false; // or true
$jp = new JsPack(
    '/js/', // абсолютный путь к файлам js
    '/dist/js/' // абсолютный путь к сборкам js
);
$jp->setProduction($isProduction);
// путь к скриптам для web-страниц
$jp->setRelativeWebPath('/dist/js/');
echo '<script src="' . $jp->compileCrunched('admin') . '"></script>';
echo '<script>' . $jp->compileCrunched('main', true) . '</script>';
// ...
```