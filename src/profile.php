<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <?php session_start(); ?>
</head>
<body>
    <header>
        <h1><?= isset($_SESSION['user_name']) ? "Üdvözlünk, {$_SESSION['user_name']}!": "Még nem vagy bejelentkezve."?></h1>
    </header>
</body>
</html>