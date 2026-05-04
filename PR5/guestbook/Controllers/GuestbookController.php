<?php

namespace PR5\guestbook\Controllers;

// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions

// TODO 2: ROUTING



class GuestbookController{

    public function execute() {
        // TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

        $infoMessage = '';
        $errors = [];
        $aNews = [];
        $aConfig = require 'config.php';

        try {
            // Використовуємо PDO, як того вимагає завдання
            // Рядок підключення (DSN) написаний в один рядок без пробілів
            $dsn = "mysql:host={$aConfig['host']};dbname={$aConfig['name']};charset=utf8";
            $pdo = new \PDO($dsn, $aConfig['user'], $aConfig['pass']);
            // Вмикаємо режим виведення помилок бази даних, щоб у разі чого бачити причину
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $title = $_POST['title'] ?? '';
                $text = $_POST['text'] ?? '';

                // Валідація
                if (mb_strlen($title) < 3) $errors[] = "Заголовок занадто короткий.";
                if (mb_strlen($text) < 10) $errors[] = "Новина має бути довшою за 10 символів.";

                if (empty($errors)) {
                    $date = date("Y-m-d H:i:s");
                    // quote() додає екранування (real_escape_string) і оточує рядок одинарними лапками
                    $sql = "INSERT INTO news (title, text, date) VALUES (:title, :text, :date)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':title' => $title,
                        ':text'  => $text,
                        ':date'  => $date
                    ]);
                    // Після успішного запису оновлюємо сторінку
                    header("Location: /Back-end/PR5/guestbook/guestbook");
                    die;
                } else {
                    $infoMessage = implode("<br>", $errors);
                }
            }
            //Отримуємо новини
            $query = $pdo->query("SELECT * FROM news ORDER BY date DESC");
            $aNews = $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            $infoMessage = "Помилка бази: " . $e->getMessage();
        }

        $arguments = [
                'infoMessage' => $infoMessage,
                'aNews' => $aNews
        ];

        $this->renderView($arguments);
    }

    public function renderView($arguments = []) {
        // TODO 4: RENDER: 1) view (html) 2) data (from php)
        $news = $arguments['aNews'] ?? [];
        $infoMessage = $arguments['infoMessage'] ?? '';
        ?>

        <!DOCTYPE html>
        <html>
        <?php require_once 'ViewSections/sectionHead.php' ?>
        <body>
        <div class="container">
            <?php require_once 'ViewSections/sectionNavbar.php' ?>
            <br>

            <?php if ($infoMessage): ?>
                <div class='alert alert-danger'><?= $infoMessage ?></div>
            <?php endif; ?>

            <div class="card card-primary">
                <div class="card-header bg-primary text-light">Додавання новини</div>
                <div class="card-body">
                    <form action="guestbook" method="post"> <!-- Шлях до роуту -->
                        <div class="mb-3">
                            <label class="form-label">Заголовок</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Новина</label>
                            <textarea name="text" class="form-control" rows="3" required><?= htmlspecialchars($_POST['text'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Відправити</button>
                    </form>
                </div>
            </div>

            <br>

            <div class="card card-primary">
                <div class="card-header bg-body-secondary text-dark">Новини</div>
                <div class="card-body">
                    <?php if (empty($news)): ?>
                        <p class="text-muted">Новин поки немає.</p>
                    <?php else: ?>
                        <?php foreach ($news as $oneNew): ?>
                            <div class="border-bottom mb-3 pb-2">
                                <strong><?= htmlspecialchars($oneNew['title']) ?></strong>
                                <small class="text-muted"><?= $oneNew['date'] ?></small>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($oneNew['text'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}
