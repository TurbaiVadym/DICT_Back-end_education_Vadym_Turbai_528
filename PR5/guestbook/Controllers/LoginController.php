<?php

namespace PR5\guestbook\Controllers;

class LoginController {

    // TODO 3.raw: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3.raw) transforming data
    public function execute() {

        if (!empty($_SESSION['auth'])) {
            header('Location: /Back-end/PR5/guestbook/admin');
            die;
        }

        // 1. Create empty $infoMessage
        $infoMessage = '';

        // 2. handle form data
        if (!empty($_POST['email']) && !empty($_POST['password'])) {

            $aConfig = require_once 'config.php';

            // 3.raw. Check that user has already existed
            $pdo = new \PDO("mysql:dbname={$aConfig['name']};host={$aConfig['host']};charset={$aConfig['charset']}", $aConfig['user'], $aConfig['pass']);

            $pdoStatement = $pdo->query("SELECT * FROM users WHERE `email`='".$_POST['email'] . "' AND `password`=" . "'". sha1($_POST['password']). "'");  // sha1 is more prefer than md5
            $user = $pdoStatement->fetch(\PDO::FETCH_ASSOC);

            if (!empty($user)) {
                $_SESSION['auth'] = true;
                header("Location: /Back-end/PR5/guestbook/admin");
                die;
            }

            if (empty($user)) {
                $infoMessage = "Невірний email або пароль. ";
                $infoMessage .= "<a href='register'>Зареєструватися</a>";
            }

        } elseif (!empty($_POST)) {
            $infoMessage = 'Заповніть форму авторизації!';
        }

        $arguments = [
            'infoMessage' => $infoMessage
        ];

        $this->renderView($arguments);
    }

    // TODO 4: RENDER: 1) view (html) 2) data (from php)
    public function renderView($arguments = []) {
        ?>
        <!DOCTYPE html>
        <html>

        <?php require_once 'ViewSections/sectionHead.php' ?>

        <body>

        <div class="container">

            <?php require_once 'ViewSections/sectionNavbar.php' ?>

            <br>

            <div class="card card-primary">
                <div class="card-header bg-primary text-light">
                    Форма входу
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email"/>
                        </div>

                        <div class="form-group">
                            <label>Пароль</label>
                            <input class="form-control" type="password" name="password"/>
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="form"/>
                        </div>
                    </form>

                    <!-- TODO: render php data   -->
                    <?php
                    if ($arguments['infoMessage']) {
                        echo '<hr/>';
                        echo "<span style='color:red'>{$arguments['infoMessage']}</span>";
                    }
                    ?>

                </div>
            </div>
        </div>

        </body>
        </html>

        <?php
    }
}