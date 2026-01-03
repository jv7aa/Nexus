<nav class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-shield-halved"></i> NEXUS
    </div>
    <ul class="menu">
        <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Visão Geral</a></li>
        <li><a href="produtos.php"><i class="fa-solid fa-box"></i> Produtos</a></li>
        <li><a href="entradas.php"><i class="fa-solid fa-truck-ramp-box"></i> Entradas</a></li>
        <li><a href="saidas.php"><i class="fa-solid fa-dolly"></i> Saídas</a></li>
        <li><a href="historico.php"><i class="fa-solid fa-history"></i> Histórico</a></li>
        <li><a href="relatorios.php"><i class="fa-solid fa-file-pdf"></i> Relatórios</a></li>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="usuarios.php"><i class="fa-solid fa-users"></i> Usuários</a></li>
        <?php endif; ?>

        <li style="margin-top: 50px;"><a href="logout.php"><i class="fa-solid fa-power-off"></i> Sair</a></li>
    </ul>
</nav>