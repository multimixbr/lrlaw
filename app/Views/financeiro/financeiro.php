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

<div>
    <h2>Bem-vindo ao Sistema Financeiro</h2>
    <p>Aqui você pode gerenciar suas pendências Financeiras.</p>
    
    <div class="card-container">
        <a href="<?= base_url('financeiro/financeiroControllers/cadastrarContas') ?>" class="card">
            <div class="card-icon"><i class="fas fa-wallet"></i></div>
            <div class="card-title">Cadastro de contas</div>
            <div class="card-description">Gerencie seus pagamentos e faturas.</div>
        </a>
        <a href="<?= base_url('financeiro/financeiroControllers/visualizarContas') ?>" class="card">
            <div class="card-icon"><i class="fas fa-chart-line"></i></div>
            <div class="card-title">Consultar contas</div>
            <div class="card-description">Acompanhe seus investimentos e rendimentos.</div>
        </a>

        <a href="<?= base_url('financeiro/financeiroControllers/controleContas') ?>" class="card">
            <div class="card-icon"><i class="fas fa-tasks"></i></div> <!-- Ícone alterado -->
            <div class="card-title">Controle das contas</div>
            <div class="card-description">Tenha um controle das suas contas.</div>
        </a>
    </div>
</div>