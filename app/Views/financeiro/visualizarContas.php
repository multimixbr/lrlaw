<?php 
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Financeiro\LanParcelaModels;

$pessoasModel = new PessoasModels();
$financeiroModel = new LancamentosModels();
?>

<style>
    .card-header.bg-primary-custom {
        background-color: #343a40;
        color: #ffffff;
    }
    .card-header.bg-secondary-custom {
        background-color: #6c757d;
        color: #ffffff;
    }
    /* Centralizando os ícones nas células */
    .table td {
        text-align: center;
        vertical-align: middle;
    }
    .icon-status {
        font-size: 1.5rem;
    }
    .icon-partial {
        color: orange;
    }
    .linha-vencida td {
        background-color: #fdd !important;
    }
</style>

<div>
    <div class="container-fluid mt-5">
        <!-- Campo de filtro -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" style="cursor: pointer;">
                <span>Filtros</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div id="filtrosCollapse" class="collapse">
                <div class="card-body">
                    <form action="<?= base_url('financeiro/financeiroControllers') ?>" method="GET">
                        <div class="row g-3">
                            <!-- ID do Lançamento -->
                            <div class="col-md-2">
                                <label for="id_lan" class="form-label">ID do Lançamento</label>
                                <input type="text" name="id_lan" value="<?= $filtros['id_lan'] ?>" id="id_lan" class="form-control" placeholder="Ex: 12345">
                            </div>
                            <!-- Tipo de Conta -->
                            <div class="col-md-2">
                                <label for="tp_lancamento" class="form-label">Tipo de Conta</label>
                                <select name="tp_lancamento" id="tp_lancamento" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="R" <?= $filtros['tp_lancamento'] == 'R' ? 'selected' : '' ?>>Contas a Receber</option>
                                    <option value="D" <?= $filtros['tp_lancamento'] == 'D' ? 'selected' : '' ?>>Contas a Pagar</option>
                                </select>
                            </div>
                            <!-- Situação -->
                            <div class="col-md-2">
                                <label for="situacao" class="form-label">Situação</label>
                                <select name="situacao" id="situacao" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="A" <?= $filtros['situacao'] == 'A' ? 'selected' : '' ?>>Aberto</option>
                                    <option value="B" <?= $filtros['situacao'] == 'B' ? 'selected' : '' ?>>Baixado</option>
                                    <option value="C" <?= $filtros['situacao'] == 'C' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                            <!-- Data de Vencimento -->
                            <div class="col-md-2">
                                <label for="dt_vencimento" class="form-label">Data de Vencimento</label>
                                <input type="text" name="dt_vencimento" id="dt_vencimento" value="<?= empty($filtros['dt_vencimento']) ? '' : date('d/m/Y', strtotime($filtros['dt_vencimento'])) ?>" class="form-control datepicker" placeholder="dd/mm/yyyy">
                            </div>
                            <!-- Forma de Pagamento -->
                            <div class="col-md-2">
                                <label for="id_forma_pagto" class="form-label">Forma de Pagamento</label>
                                <select name="id_forma_pagto" id="id_forma_pagto" class="form-control">
                                    <option value="">Selecione</option>
                                    <?php foreach ($formasPagamento as $forma) : ?>
                                        <option value="<?= $forma->id_formapagto; ?>" <?= $filtros['id_forma_pagto'] == $forma->id_formapagto ? 'selected' : '' ?>>
                                            <?= $forma->dsc_forma_pagto; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Valor da Conta -->
                            <div class="col-md-2">
                                <label for="vl_conta" class="form-label">Valor da Conta</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" name="vl_conta" value="<?= $filtros['vl_conta'] ?>" id="vl_conta" class="form-control" placeholder="Ex: 1000,00">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-4">
                            <!-- Conferido -->
                            <div class="col-md-2">
                                <label for="is_conferido" class="form-label">Conferido</label>
                                <select name="is_conferido" id="is_conferido" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="1" <?= $filtros['is_conferido'] == '1' ? 'selected' : '' ?>>Sim</option>
                                    <option value="0" <?= $filtros['is_conferido'] == '0' ? 'selected' : '' ?>>Não</option>
                                </select>
                            </div>
                            <!-- Aprovado -->
                            <div class="col-md-2">
                                <label for="is_aprovado" class="form-label">Aprovado</label>
                                <select name="is_aprovado" id="is_aprovado" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="1" <?= $filtros['is_aprovado'] == '1' ? 'selected' : '' ?>>Sim</option>
                                    <option value="0" <?= $filtros['is_aprovado'] == '0' ? 'selected' : '' ?>>Não</option>
                                </select>
                            </div>
                            <!-- Num doc -->
                            <div class="col-md-2">
                                <label for="num_doc" class="form-label">Numero Doc.</label>
                                <input type="text" name="num_doc" id="num_doc" value="<?= $filtros['num_doc'] ?>" class="form-control" placeholder="Numero Doc.">
                            </div>
                            <!-- NDI -->
                            <div class="col-md-2">
                                <label for="id_ndi" class="form-label">NDI</label>
                                <input type="text" name="id_ndi" id="id_ndi" value="<?= $filtros['id_ndi'] ?>" class="form-control" placeholder="NDI">
                            </div>
                            <!-- Nome do Cliente -->
                            <div class="col-md-2">
                                <label for="nm_pessoa" class="form-label">Nome do Cliente</label>
                                <input type="text" name="nm_pessoa" value="<?= $filtros['nm_pessoa'] ?>" id="nm_pessoa" class="form-control" placeholder="Ex: João Silva">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary-custom text-white d-flex justify-content-between align-items-center">
                <span>Listagem de Lançamentos</span>
                <a href="<?= base_url('financeiro/financeiroControllers/novo') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Novo Lançamento
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelaLancamentos" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tp. Conta</th>
                                <th>Descrição</th>
                                <th>Cliente</th>
                                <th>Valor da Conta</th>
                                <th>Dt. Vencimento</th>
                                <th>Forma de Pagamento</th>
                                <th>Conferido</th>
                                <th>Aprovado</th>
                                <th>Baixado</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lancamentos)) { 
                                foreach ($lancamentos as $lancamento) {

                                    // Agora, não há lógica de parcelas, então os status são obtidos diretamente do lançamento
                                    $isConferido = $lancamento->is_conferido == 1 ? 'full' : 'none';
                                    $isAprovado = $lancamento->is_aprovado == 1 ? 'full' : 'none';
                                    $isBaixado = !empty($lancamento->baixado_por) ? 'full' : 'none';

                                    // A data de vencimento exibida será a própria do lançamento
                                    $dt_vencimento_exibido = $lancamento->dt_vencimento;

                                    // Verifica se o vencimento está vencido
                                    $hoje = date('Y-m-d');
                                    $row_class = (strtotime($dt_vencimento_exibido) < strtotime($hoje)) ? 'linha-vencida' : '';
                            ?>
                                    <tr class="<?= $row_class; ?>">
                                        <td><?= $lancamento->id_lan ?></td>
                                        <td><?= $lancamento->tp_lancamento == 'R' ? 'CR' : 'CP' ?></td>
                                        <td><?= $lancamento->descricao ?></td>
                                        <td><?= $pessoasModel->getNomePessoaByID($lancamento->id_pessoa) ?></td>
                                        <td>R$ <?= number_format($lancamento->vl_original, 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($dt_vencimento_exibido)) ?></td>
                                        <td><?= $financeiroModel->getFormaPgtoByID($lancamento->id_forma_pagto) ?></td>
                                        <!-- Coluna Conferido -->
                                        <td>
                                            <?php if ($isConferido == 'full') : ?>
                                                <span style="color: green;" class="icon-status">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;" class="icon-status">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Coluna Aprovado -->
                                        <td>
                                            <?php if ($isAprovado == 'full') : ?>
                                                <span style="color: green;" class="icon-status">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;" class="icon-status">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Coluna Baixado -->
                                        <td>
                                            <?php if ($isBaixado == 'full') : ?>
                                                <span style="color: green;" class="icon-status">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;" class="icon-status">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Coluna Situação -->
                                        <td>
                                            <?php
                                                $situacao = ($lancamento->situacao == 'A') ? 'Aberto' : (($lancamento->situacao == 'B') ? 'Baixado' : 'Cancelado');
                                                echo $situacao;
                                            ?>
                                        </td>
                                        <!-- Coluna Ações -->
                                        <td>
                                            <a href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $lancamento->id_lan) ?>" class="btn btn-warning btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="12" class="text-center">Nenhum lançamento encontrado.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#vl_conta').mask('#.##0,00', { reverse: true });
        <?php if (!empty($lancamentos)) { ?>
            inicializarPaginator('#tabelaLancamentos');
        <?php } ?>
        inicializarSelect2('#id_forma_pagto', 'Digite para pesquisar uma forma de pagamento.');
    });
</script>
