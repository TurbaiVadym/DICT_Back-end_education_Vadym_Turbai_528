<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();
function getComments($filename) {
    $comments = [];
    if (file_exists($filename)) {
        $fileStream = fopen($filename, "r");
        while (!feof($fileStream)) {
            $jsonString = fgets($fileStream);
            $array = json_decode($jsonString, true);
            if (!empty($array)) {
                $comments[] = $array;
            }
        }
        fclose($fileStream);
    }
    return array_reverse($comments); // Нові коментарі зверху
}

// TODO 2: ROUTING

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $text = $_POST['text'] ?? '';
    $errors = [];

    // 1. Перевірка формату Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некоректний формат email.";
    }

    // 2. Перевірка довжини імені
    if (mb_strlen($name) < 2) {
        $errors[] = "Ім'я занадто коротке.";
    }

    // 3. Перевірка довжини тексту
    if (mb_strlen($text) < 5) {
        $errors[] = "Повідомлення має бути довшим за 5 символів.";
    }

    if (empty($errors)) {

        if (!empty($email) && !empty($name) && !empty($text)) {
            $data = [
                    'email' => htmlspecialchars($email),
                    'name' => htmlspecialchars($name),
                    'text' => htmlspecialchars($text),
                    'date' => date('Y-m-d H:i:s')
            ];

            $jsonString = json_encode($data);
            $fileStream = fopen('comments.csv', 'a');
            fwrite($fileStream, $jsonString . "\n");
            fclose($fileStream);

            // Редирект, щоб уникнути повторного відправлення форми при оновленні
            header("Location: guestbook.php");
            exit;
        }
    }

    else {
        // Виводимо помилки користувачу
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}

// TODO 4: RENDER: 1) view (html) 2) data (from php)

?>

<!DOCTYPE html>
<html>

<?php require_once 'sectionHead.php' ?>

<body>

<div class="container">

    <!-- navbar menu -->
    <?php require_once 'sectionNavbar.php' ?>
    <br>

    <!-- guestbook section -->
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            Залишити відгук у GuestBook
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-sm-6">
                    <!-- TODO: create guestBook html form   -->

                    <form action="guestbook.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ім'я</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Повідомлення</label>
                            <textarea name="text" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Відправити</button>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Коментарі
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <!-- TODO: render guestBook comments   -->

                    <?php
                    $comments = getComments('comments.csv');
                        if (empty($comments)): ?>
                            <p class="text-muted">Відгуків поки немає. Будьте першим!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="border-bottom mb-3 pb-2">
                                    <strong><?= $comment['name'] ?></strong> <small class="text-muted"><?= $comment['date'] ?></small>
                                    <p class="mb-1"><?= $comment['text'] ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
