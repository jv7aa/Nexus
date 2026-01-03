<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Fortress Inventory</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icon.png">
    

</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <h2>🔒 Acesso Restrito</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="color: red; margin-bottom: 15px; font-size: 0.9rem;">
                    Usuário ou senha incorretos.
                </div>
            <?php endif; ?>

            <form action="actions/login_act.php" method="POST">
                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="username" class="form-control" placeholder="Digite seu usuário" required>
                </div>
                
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="•••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">ENTRAR</button>
            </form>
            
            <p style="margin-top: 20px; font-size: 0.8rem; color: #aaa;">
                Fortress Inventory v1.0 &copy; 2026
            </p>
        </div>
    </div>

</body>
</html>