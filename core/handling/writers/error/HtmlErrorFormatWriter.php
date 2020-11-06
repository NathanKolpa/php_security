<?php


namespace core\handling\writers;

require_once "ErrorFormatWriter.php";

class HtmlErrorFormatWriter implements ErrorFormatWriter
{

    function write(int $statusCode, string $error, string $extraMessage)
    {
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Error</title>
        </head>
        <body>
        <h1>Error <?= $statusCode ?></h1>
        <h2><?= $error ?></h2>
        <p><?= $extraMessage ?></p>
        </body>
        </html>
        <?php
    }
}