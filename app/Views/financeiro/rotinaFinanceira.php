<?php
// Supondo que exista um model para contas
$lancamentoModel = new \App\Models\Financeiro\LancamentosModels();

// Definições de data
$dataHoje = date('Y-m-d');
$mesAtual = date('m');
$anoAtual = date('Y');

// Lançamentos (sem parcelas)
$contasVencerHoje   = $lancamentoModel->getContasAVencerHoje($dataHoje);
$contasVencerMes    = $lancamentoModel->getContasAVencerMes($mesAtual, $anoAtual);
$contasVencidas     = $lancamentoModel->getContasVencidas($dataHoje);
$contasPagas        = $lancamentoModel->getContasPagas($mesAtual, $anoAtual);
?>

<style>
    .card-header {
        background-color: #343a40;
        color: #fff;
    }
    .table thead th {
        background-color: #f8f9fa;
    }
    .status-vencida {
        color: #dc3545;
        font-weight: bold;
    }
    .status-a-vencer {
        color: #28a745;
        font-weight: bold;
    }
    .link-custom {
        color: #3498db;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    .link-custom:hover {
        color: #2980b9;
        text-decoration: underline;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12 mb-3">
            <h1 class="text-center">Painel de Rotina Financeira</h1>
        </div>
    </div>

    <div class="row">
        <!-- Lançamentos a Vencer Hoje -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h3>Lançamentos a Vencer Hoje (<?= date('d/m/Y'); ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($contasVencerHoje)): ?>
                        <div class="table-responsive">
                            <table id="lanToday" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contasVencerHoje as $conta): ?>
                                        <tr>
                                            <td><a class="link-custom" href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $conta->id_lan) ?>" target="_blank"><?= $conta->id_lan ?></a></td>
                                            <td><?= htmlspecialchars($conta->descricao); ?></td> 
                                            <td>R$ <?= number_format($conta->vl_original, 2, ',', '.'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta->dt_vencimento)); ?></td>
                                            <td><label class="status-a-vencer">A Vencer</label></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nenhum lançamento a vencer hoje.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Lançamentos a Vencer no Mês -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h3>Lançamentos a Vencer no Mês (<?= date('m/Y'); ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($contasVencerMes)): ?>
                        <div class="table-responsive">
                            <table id="lanMonth" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contasVencerMes as $conta): ?>
                                        <tr>
                                            <td><a class="link-custom" href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $conta->id_lan) ?>" target="_blank"><?= $conta->id_lan ?></a></td>
                                            <td><?= htmlspecialchars($conta->descricao); ?></td>
                                            <td>R$ <?= number_format($conta->vl_original, 2, ',', '.'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta->dt_vencimento)); ?></td>
                                            <td><label class="status-a-vencer">A Vencer</label></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nenhum lançamento a vencer neste mês.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lançamentos Vencidos -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger">
                    <h3>Lançamentos Vencidos</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($contasVencidas)): ?>
                        <div class="table-responsive">
                            <table id="lanVencidos" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Dias em Atraso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contasVencidas as $conta): ?>
                                        <tr>
                                            <td><a class="link-custom" href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $conta->id_lan) ?>" target="_blank"><?= $conta->id_lan ?></a></td>
                                            <td><?= htmlspecialchars($conta->descricao); ?></td>
                                            <td>R$ <?= number_format($conta->vl_original, 2, ',', '.'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta->dt_vencimento)); ?></td>
                                            <td>
                                                <?php
                                                    $dataVencimento = new DateTime($conta->dt_vencimento);
                                                    $dataHojeObj = new DateTime($dataHoje);
                                                    echo abs($dataHojeObj->diff($dataVencimento)->format('%r%a'));
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nenhum lançamento vencido.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Lançamentos Pagos no Mês -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success">
                    <h3>Lançamentos Pagos no Mês</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($contasPagas)): ?>
                        <div class="table-responsive">
                            <table id="lanPagos" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Data de Pagamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contasPagas as $conta): ?>
                                        <tr>
                                            <td><a class="link-custom" href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $conta->id_lan) ?>" target="_blank"><?= $conta->id_lan ?></a></td>
                                            <td><?= htmlspecialchars($conta->descricao); ?></td>
                                            <td>R$ <?= number_format($conta->vl_original, 2, ',', '.'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta->dt_baixa)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nenhum lançamento pago neste mês.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        inicializarPaginator('#lanToday');
        inicializarPaginator('#lanMonth');
        inicializarPaginator('#lanVencidos');
        inicializarPaginator('#lanPagos');
    });
</script>
