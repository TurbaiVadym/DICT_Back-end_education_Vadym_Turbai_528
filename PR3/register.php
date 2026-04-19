<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();
$aConfig = require_once 'config.php';

$db = mysqli_connect(
    $aConfig['host'],
    $aConfig['user'],
    $aConfig['pass'],
    $aConfig['name']
);

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

    // 3. Check that user has already existed
    $queryCheck = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $queryCheck);

    if (mysqli_num_rows($result) > 0) {
        $infoMessage = "Такий користувач вже існує! ";
        $infoMessage .= "<a href='login.php'>Сторінка входу</a>";
    } else {
        // 2. Створюємо нового користувача
        $queryInsert = "INSERT INTO users (email, password) VALUES ('$email', '$password')";

        if (mysqli_query($db, $queryInsert)) {
            header('Location: login.php');
            die;
        } else {
            $infoMessage = "Помилка реєстрації: " . mysqli_error($db);
        }
    }
} elseif (!empty($_POST)) {
    $infoMessage = 'Заповніть форму реєстрації!';
}

mysqli_close($db);

// TODO 4: RENDER: 1) view (html) 2) data (from php)

?>

<!DOCTYPE html>
<html>
<?php require_once 'sectionHead.php' ?>

<body>

<div class="container">
    <?php require_once 'sectionNavbar.php' ?>
    <br>

    <div class="card card-primary">
        <div class="card-header bg-success text-light">
            Register form
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
                    <input type="submit" class="btn btn-primary" name="formRegister"/>
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