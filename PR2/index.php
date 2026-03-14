<?php

$search_query = '';
$error = '';
$serper_api_key = '1b5778bf79e5465a83b1f6da60b9dabfcaad3e09';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);

    $url = 'https://google.serper.dev/search';

    $post_data = [
            'q' => $search_query,
    ];

    $json_post = json_encode($post_data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $serper_api_key,
            'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = 'cURL помилка: ' . curl_error($ch);
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code !== 200) {
            $error = "Serper API повернув код $http_code. Перевір ключ, ліміт або запит.";
        } else {
            $data = json_decode($response, true);

            // Для тестування: виводимо всю структуру
            // echo '<pre>'; var_dump($data); echo '</pre>';

            // Беремо органічні результати — це наш "items"
            if (isset($data['organic']) && is_array($data['organic'])) {
                $items = $data['organic'];
            } else {
                $error = 'У відповіді немає ключа "organic" або він не є масивом.';
            }
        }
    }

    curl_close($ch);

} else if (isset($_GET['search'])) {
    $error = 'Введіть пошуковий запит (не може бути порожнім)';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
    </head>
    <body>
        <h2>My Browser</h2>
        <form method="GET" action="/PR2/index.php">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" value=""><br><br>
            <input type="submit" value="Submit">
        </form >
        <?php

        if ($error) {
            echo '<div style="color: red; font-weight: bold; margin: 20px 0; padding: 10px; border: 1px solid red;">'
                    . htmlspecialchars($error)
                    . '</div>';
        }

        if (!empty($items)) {
            echo '<h3>Результати для запиту: ' . htmlspecialchars($search_query) . '</h3>';
            echo '<ol start="1">';

            foreach ($items as $item) {
                $title   = htmlspecialchars($item['title']   ?? 'Без заголовка');
                $link    = htmlspecialchars($item['link']    ?? '#');
                $snippet = htmlspecialchars($item['snippet'] ?? 'Опис відсутній');

                // Додаткові поля Serper (якщо є)
                $date    = !empty($item['date']) ? ' (' . htmlspecialchars($item['date']) . ')' : '';

                echo '<li style="margin-bottom: 20px;">';
                echo '<strong><a href="' . $link . '" target="_blank" style="color: #1a0dab; text-decoration: none;">' . $title . '</a></strong>' . $date . '<br>';
                echo '<div style="color: #006621; font-size: 14px;">' . htmlspecialchars($item['displayLink'] ?? parse_url($link, PHP_URL_HOST)) . '</div>';
                echo '<div style="color: #545454; font-size: 14px;">' . $snippet . '</div>';
                echo '</li>';
            }

            echo '</ol>';
        }
        ?>

    </body>
</html>
