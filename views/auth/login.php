<!DOCTYPE html>
<html>
<head>
    <title>Login - ThanksCompany</title>
    <style>
        body { font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .login-box { background: white; padding: 40px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: black; color: white; border: none; cursor: pointer; font-weight: bold; }
        h2 { font-family: 'Impact', sans-serif; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>LOGIN</h2>
        <form action="index.php?page=auth&action=login_process" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">SIGN IN</button>
        </form>
        <p><small>Belum punya akun? <a href="#">Register</a></small></p>
    </div>
</body>
</html>