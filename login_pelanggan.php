<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pelanggan Baru</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        .card { border: 1px solid #ccc; padding: 20px; border-radius: 8px; width: 300px; }
        input { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Pelanggan</h2>
        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" placeholder="Masukkan username" required>
            
            <label>Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <button type="submit" name="login">Masuk sini</button>
        </form>
    </div>
</body>
</html>