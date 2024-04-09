<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<body>
<form action="/create-user" method="post" data-cy="sign-up">
    <label>
        Email: <input type="email" name="email" placeholder="Email" data-cy="sign-up__email" required><br>
        Password: <input type="password" name="password" placeholder="Password" data-cy="sign-up__password" required><br>
        Repeat Password: <input type="password" name="password_confirmation" placeholder="Repeat Password" data-cy="sign-up__repeatPassword" required><br>
        Coins: <input type="number" name="coins" placeholder="Coins" data-cy="sign-up__coins"><br>
        <button type="submit" data-cy="sign-up__btn">Sign Up</button>
    </label>
</form>
</body>
</html>
