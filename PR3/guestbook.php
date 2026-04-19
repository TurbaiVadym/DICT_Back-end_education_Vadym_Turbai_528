<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

$aConfig = require_once 'config.php';
$db = mysqli_connect(
        $aConfig ['host'],
        $aConfig ['user'],
        $aConfig ['pass'],
        $aConfig ['name']
);

if (!$db) {
    die("Помилка підключення: ".mysqli_connect_error());
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

            $safeEmail = mysqli_real_escape_string($db, $email);
            $safeName = mysqli_real_escape_string($db, $name);
            $safeText = mysqli_real_escape_string($db, $text);

            $query = "INSERT INTO comments (email, name, text) VALUES (
                                                       '$safeEmail',
                                                       '$safeName',
                                                       '$safeText'
                                                       )";
            if (mysqli_query($db, $query)) {
                header("Location: guestbook.php");
                exit;
            } else {
                $errors[] = "Помилка БД: ".mysqli_error($db);
            }
    }
}
    //Отримуємо коментарі
    $querySelect = "SELECT * FROM comments ORDER BY date DESC";
    $result = mysqli_query($db, $querySelect);
    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close ($db );


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
    <!-- Вивдимо помилки якщо є -->
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class='alert alert-danger'><?= $error ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- guestbook section -->
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            Залишити відгук у GuestBook
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-sm-6">

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

                    <?php if (empty($comments)): ?>
                        <p class="text-muted">Відгуків поки немає. Будьте першим!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="border-bottom mb-3 pb-2">
                                <strong><?= htmlspecialchars($comment['name']) ?></strong>
                                <small class="text-muted"><?= $comment['date'] ?></small>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
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
