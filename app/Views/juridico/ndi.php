<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }
    .main {
        padding: 20px;
    }
    h2 {
        color: #333;
    }
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        margin-top: 30px;
    }
    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
        margin: 15px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s;
        text-decoration: none;
    }
    .card:hover {
        transform: translateY(-5px);
        text-decoration: none;
    }
    .card-icon {
        font-size: 40px;
        color: #333;
        margin-bottom: 15px;
    }
    .card-title {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }
    .card-description {
        font-size: 14px;
        color: #777;
    }
</style>

<div class="main">
    <h2>Bem-vindo ao Sistema Jurídico</h2>
    <p>Aqui você pode gerenciar suas demandas.</p>
    
    <div class="card-container">
        <a href="<?= base_url('juridico/ndiControllers/cadastrarNDI') ?>" class="card">
            <div class="card-icon"><i class="fas fa-file-alt"></i></div>
            <div class="card-title">Criar NDI</div>
            <div class="card-description">Crie novos NDIs para suas demandas.</div>
        </a>
        <a href="<?= base_url('juridico/ndiControllers/visualizarNDI') ?>" class="card">
            <div class="card-icon"><i class="fas fa-eye"></i></div>
            <div class="card-title">Visualizar NDI</div>
            <div class="card-description">Veja os NDIs existentes.</div>
        </a>
    </div>
</div>