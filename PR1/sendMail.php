<?php
$firstName = "Bobby";
$lastName = "Kottik";
$c = "!";
echo $firstName."<br>";

$to = "vadim2198@gmail.com";
$subject = "Привіт від Bobby!";
$message = $firstName.$lastName.$c;
$headers = "From: bobby.student.528@gmail.com\r<br>";

echo '============'."<br>";
echo $subject."<br>";
echo '============'."<br>";

$text1 = "firstName : $firstName"."<br>";
$text2 = "lastName : $lastName"."<br>";
$text3 = "email : $to"."<br>";

$message = $text1.$text2;
$message.= $text3;

echo $message;

mail(
    $to, // email приймаючої сторони
    $subject, // заголовок листи string
    $message, // текст листа string
    $headers // вставка в кінець заголовка листа
);

echo ini_get("smtp_port");

phpinfo (); // виводить усі налаштування php
die;

