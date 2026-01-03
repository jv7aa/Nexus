<?php
session_start();
require 'config/db.php';

// 🔒 SECURITY GATE: Apenas Admin entra aqui
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Processamento de Novo Usuário (Formulário Simples)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $new_pass = $_POST['password'];
    $new_role = $_POST['role'];

    // Verifica se usuário já existe
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$new_user]);
    
    if ($check->rowCount() > 0) {
        $error = "Erro: Este nome de usuário já está em uso.";
    } else {
        // HASH DA SENHA (Segurança)
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$new_user, $hash, $new_role]);
        $msg = "Usuário criado com sucesso!";
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários | Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="icon.png">
</head>
<body>
<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>

    <main class="main-content">
        <div class="header-bar">
            <h2>Gerenciar Usuários</h2>
        </div>
        
        <?php if(isset($msg)) echo "<div style='color: green; margin-bottom:15px;'>$msg</div>"; ?>
        <?php if(isset($error)) echo "<div style='color: red; margin-bottom:15px;'>$error</div>"; ?>

        <div class="card" style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #1f2937;">
            <h4><i class="fa-solid fa-user-plus"></i> Novo Usuário</h4>
            <form method="POST" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1;">
                    <label style="font-size: 0.8rem;">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Ex:Jose Silva">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 0.8rem;">Senha</label>
                    <input type="password" name="password" class="form-control" required placeholder="******">
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 0.8rem;">Nível</label>
                    <select name="role" class="form-control">
                        <option value="user">Usuário Comum</option>
                        <option value="admin">Administrador</option>
                        <option value="auditor">Auditor</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 42px;">Criar</button>
            </form>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Cargo</th>
                        <th>Criado em</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; 
                                background: <?= $u['role']=='admin'?'#fee2e2':'#e0f2fe' ?>; 
                                color: <?= $u['role']=='admin'?'#b91c1c':'#0369a1' ?>;">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <?php if($u['id'] != $_SESSION['user_id'] && $u['username'] !== 'admin_master'): ?>
                                
                                <form action="actions/delete_user.php" method="POST" style="display:inline;" class="form-delete">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="button" onclick="confirmarExclusao(this)" class="btn btn-sm btn-danger" title="Excluir Usuário">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>

                            <?php else: ?>
                                <span style="color: #ccc; font-size: 0.8rem;"><i class="fa-solid fa-lock"></i> Bloqueado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarExclusao(botao) {
        var form = botao.closest('form');
        Swal.fire({
            title: 'Excluir Usuário?',
            text: "Essa ação removerá o acesso desta pessoa permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#d1d5db',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    }

    // Alertas via URL
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('msg') === 'deleted') {
        Swal.fire({ icon: 'success', title: 'Usuário Removido!', showConfirmButton: false, timer: 1500 });
    }
    if(urlParams.get('error') === 'self_delete') {
        Swal.fire({ icon: 'error', title: 'Ação Bloqueada', text: 'Você não pode excluir sua própria conta enquanto está logado.' });
    }
</script>

</body>
</html>