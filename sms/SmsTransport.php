<?php

// ID пользователя
// (узнать можно на сайте: http://www.bytehand.com/secure/settings)
define('USER_ID', '4023');

// Персональный ключ
// (узнать можно на сайте: http://www.bytehand.com/secure/settings)
define('USER_KEY', '81B178A315BB5E18');

// Подпись у сообщений. По умолчанию используется 'SMS-INFO', в случае
// изменения подписи она должна быть одобрена модератором.
define('MSG_FROM', 'liwest-nn');

// Кодировка скриптов из которых вы будете отправлять сообщения. По
// умолчанию 'utf-8'. Измените значение на 'cp1251', если хотите
// использовать кодировку Windows-1251.
define('MSG_CHARSET', 'utf-8');


// Ниже этой строки ничего изменять не надо.
define('CONFIG_PRESENT', true);
require_once 'ByteHandApi.php';

