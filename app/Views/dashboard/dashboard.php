<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastro de Pessoas</title>
    
        <!-- Links -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-m6n0sPF+EuTfPqWcEM6KoVxMF12z6ntT2h27pYxXAbu1ma50QyYI2xSH+0khfkvZ8dJYyZ+MVdUQVXxIC0HyPA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
        <!-- scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-rmi4Xr42H6mP11v5LYuYkrRCzDXbtNFgV5o8uG4YxvS6HMr6X6Inkk6Gz7td7O+2ADMMlAFAujGAh26HnS5Psw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Estilos -->
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
            }
            .sidebar {
                height: 100%;
                width: 80px;
                position: fixed;
                top: 0;
                left: 0;
                background-color: #343a40;
                overflow-x: hidden;
                transition: 0.5s;
                padding-top: 20px;
            }
            .sidebar a {
                padding: 15px 8px 15px 32px;
                text-decoration: none;
                font-size: 18px;
                color: #f1f1f1;
                display: block;
                transition: 0.3s;
            }
            .sidebar a:hover {
                color: #fff;
            }
            .sidebar a span {
                display: none;
                margin-left: 10px;
            }
            .sidebar:hover {
                width: 250px;
            }
            .sidebar:hover a span {
                display: inline;
            }
            .main {
                margin-left: 80px;
                padding: 20px;
                transition: margin-left .5s;
            }
            .sidebar:hover ~ .main {
                margin-left: 250px;
            }
        </style>
    </head>
    <body>
        
        <!-- Dashboard Lateral -->
        <div class="sidebar">
            <a href="<?= base_url("home") ?>"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a>
            <a href="<?= base_url("financeiro/financeiroControllers") ?>"><i class="fas fa-dollar-sign"></i><span> Financeiro</span></a>
            <a href="<?= base_url("juridico/ndiControllers") ?>"><i class="fas fa-file-alt"></i><span> NDI</span></a>
            <a href="<?= base_url("pessoas/pessoasControllers") ?>"><i class="fas fa-user"></i><span> Pessoas</span></a>
            <a href="<?= base_url("config/configControllers") ?>"><i class="fas fa-cog"></i><span> Configurações</span></a>
            <a href="<?= base_url("loginControllers/logout") ?>"><i class="fas fa-sign-out-alt"></i><span> Sair</span></a>
        </div>
        
        <!-- Alerta suspenso -->
        <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->has('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= session('warning') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->has('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= session('info') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        
    </body>
</html>

<script>
    // Obter a data atual em UTC
    var now = new Date();

    // Ajustar manualmente para o fuso horário desejado (-3 horas)
    now.setHours(now.getHours() - 3);

    var today = now.toISOString().split('T')[0];
</script>