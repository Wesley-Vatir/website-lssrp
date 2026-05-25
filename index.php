<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCP - Los Santos Stories</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #1a1a1a; color: #fff; text-align: center; padding-top: 50px; }
        .container { background: #262626; padding: 30px; border-radius: 8px; border-top: 5px solid #ffcc00; display: inline-block; width: 300px; }
        .logo { width: 150px; margin-bottom: 20px; }
        input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #444; background: #333; color: white; }
        button { background: #ffcc00; color: black; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; width: 95%; }
        button:hover { background: #e6b800; }
        .discord-link { margin-top: 20px; font-size: 14px; color: #ffcc00; text-decoration: none; display: block; }
    </style>
</head>
<body>

    <div class="container">
        <img src="https://cdn.discordapp.com/attachments/1507933181368733706/1508384252205731920/20260525_151941.png" alt="Logo" class="logo">
        <h2>Daftar UCP</h2>
        <form action="proses.php" method="POST">
            <input type="text" name="username" placeholder="Masukkan Username" minlength="4" required>
            <button type="submit" name="submit">REGISTER</button>
        </form>
        
        <a href="https://discord.gg/2VEzbzyJxb" class="discord-link">Join LSSRP? Click here!</a>
    </div>

</body>
</html>

