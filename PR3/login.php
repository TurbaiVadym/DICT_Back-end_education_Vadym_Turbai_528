<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

$aConfig = require_once 'config.php';

$db = mysqli_connect($aConfig['host'], $aConfig['user'], $aConfig['pass'], $aConfig['name']);

if (!empty($_SESSION['auth'])) {
    header('Location: admin.php');
    die;
}

// TODO 2: ROUTING

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

// 1. Create empty $infoMessage
$infoMessage = '';

// 2. handle form data
if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Шукаємо користувача з таким мейлом ТА паролем
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['auth'] = true;
        $_SESSION['email'] = $user['email']; // Зберігаємо пошту в сесію для guestbook
        header("Location: admin.php");
        die;
    } else {
        $infoMessage = "Невірний email або пароль. ";
        $infoMessage .= "<a href='register.php'>Зареєструватися</a>";
    }
} elseif (!empty($_POST)) {
    $infoMessage = 'Заповніть форму авторизації!';
}

mysqli_close($db);

?>


<!DOCTYPE html>
<html>

<?php require_once 'sectionHead.php' ?>

<body>

    <div class="container">

        <?php require_once 'sectionNavbar.php' ?>

        <br>

        <div class="card card-primary">
            <div class="card-header bg-primary text-light">
                Login form
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email"/>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control" type="password" name="password"/>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="form"/>
                    </div>
                </form>

                <!-- TODO: render php data   -->
                <?php
                    if ($infoMessage) {
                        echo '<hr/>';
                        echo "<span style='color:red'>$infoMessage</span>";
                    }
                ?>

            </div>
        </div>
    </div>


</body>
</html>

