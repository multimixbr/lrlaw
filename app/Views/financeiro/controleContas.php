<?php 

$lancamentoModel = new \App\Models\Financeiro\LancamentosModels();

$receitas = [];
$despesas = [];

// Suponha que o ano seja fixo ou recebido por parâmetro
$ano = date('Y'); // ou qualquer ano desejado

for ($mes = 1; $mes <= 12; $mes++) {
    // Obtem os valores para receitas e despesas
    $receitasMensal = $lancamentoModel->getTotalRecebidosMensal($mes, $ano);
    $despesasMensal = $lancamentoModel->getTotalDespesasMensal($mes, $ano);

    // Adiciona os valores ao array
    $receitas[] = $receitasMensal ?: 0; // Garante valor 0 se não houver dados
    $despesas[] = $despesasMensal ?: 0;
}

// Trimestres
$receitas_trimestres = [
    array_sum(array_slice($receitas, 0, 3)),  // Trimestre 1: Janeiro, Fevereiro, Março
    array_sum(array_slice($receitas, 3, 3)), // Trimestre 2: Abril, Maio, Junho
    array_sum(array_slice($receitas, 6, 3)), // Trimestre 3: Julho, Agosto, Setembro
    array_sum(array_slice($receitas, 9, 3))  // Trimestre 4: Outubro, Novembro, Dezembro
];

$despesas_trimestres = [
    array_sum(array_slice($despesas, 0, 3)),  // Trimestre 1: Janeiro, Fevereiro, Março
    array_sum(array_slice($despesas, 3, 3)), // Trimestre 2: Abril, Maio, Junho
    array_sum(array_slice($despesas, 6, 3)), // Trimestre 3: Julho, Agosto, Setembro
    array_sum(array_slice($despesas, 9, 3))  // Trimestre 4: Outubro, Novembro, Dezembro
];

?>
<!-- Estilo customizado para aumentar o tamanho do switch e cores dos bancos -->
<style>
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        margin-left: -2.5rem;
    }
    .form-switch .form-check-input:checked {
        background-color: #198754;
    }
    .form-switch .form-check-input::before {
        width: 1.5rem;
        height: 1.5rem;
        transform: translateX(0.2rem);
    }
    .form-switch .form-check-input:checked::before {
        transform: translateX(1.5rem);
    }
    /* Cores dos bancos */
    .bg-inter {
        background-color: #ff6c00 !important;
        /* Laranja Inter */
        color: white;
    }
    .bg-nubank {
        background-color: #820ad1 !important;
        /* Roxo Nubank */
        color: white;
    }
    /* Estilo para o totalizador */
    .bg-total {
        background-color: #343a40 !important;
        /* Cinza escuro para o totalizador */
        color: white;
    }
</style>

<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Painel de Controle Financeiro</h2>
            </div>
            <div class="card-body">

                <!-- Totalizador de Todas as Contas -->
                <div class="row text-center justify-content-center">
                    <div class="col-12">
                        <h3>Total de Todas as Contas</h3>
                    </div>
                
                    <!-- Organização dos totalizadores com cores variadas -->
                    <div class="col-md-2">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total a Receber</h5>
                                <p class="card-text">R$ <?= number_format(@$total_a_receber, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Recebido</h5>
                                <p class="card-text">R$ <?= number_format(@$total_recebido, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-dark mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total a Pagar</h5>
                                <p class="card-text">R$ <?= number_format(@$total_a_pagar, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Baixado</h5>
                                <p class="card-text">R$ <?= number_format(@$total_baixado, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total em Aberto</h5>
                                <p class="card-text">R$ <?= number_format(@$total_aberto, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos existentes -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <canvas id="totalBaixadoChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="totalReceberPagarChart"></canvas>
                    </div>
                </div>

                <!-- Adição de novas estatísticas -->

                <!-- Totalizador Mensal -->
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-12">
                        <h3>Estatísticas Mensais de <?= date('Y') ?></h3>
                    </div>
                    <div class="col-12">
                        <!-- Gráfico Mensal -->
                        <div class="row mt-4 justify-content-center">
                            <div class="col-md-12">
                                <canvas id="mensalChart" style="width:25%; height:50%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totalizador Trimestral -->
                <div class="row text-center justify-content-center mt-5">
                    <div class="col-12">
                        <h3>Estatísticas Trimestrais de <?= date('Y') ?></h3>
                    </div>
                    <div class="col-12">
                        <!-- Gráfico Trimestral -->
                        <div class="row mt-4 justify-content-center">
                            <div class="col-md-12">
                                <canvas id="trimestralChart" style="width:25%; height:50%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para os novos gráficos -->
<script>
    // Gráfico Mensal
    var ctxMensal = document.getElementById('mensalChart').getContext('2d');
    var mensalChart = new Chart(ctxMensal, {
        type: 'bar',
        data: {
            labels: [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ],
            datasets: [
                {
                    label: 'Receitas',
                    data: <?= json_encode($receitas) ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.6)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    fill: false,
                    tension: 0.1
                },
                {
                    label: 'Despesas',
                    data: <?= json_encode($despesas) ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    fill: false,
                    tension: 0.1
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Fluxo Financeiro Anual'
                }
            }
        }
    });

    // Gráfico Trimestral
    var ctxTrimestral = document.getElementById('trimestralChart').getContext('2d');
    var trimestralChart = new Chart(ctxTrimestral, {
        type: 'bar',
        data: {
            labels: ['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Trimestre 4'],
            datasets: [
                {
                    label: 'Receitas',
                    data: <?= json_encode($receitas_trimestres) ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.6)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Despesas',
                    data: <?= json_encode($despesas_trimestres) ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Receitas e Despesas Trimestrais'
                }
            }
        }
    });

    // Gráficos existentes (mantidos conforme o código original)

    // Gráfico de Total Baixado por Banco
    var ctxTotalBaixado = document.getElementById('totalBaixadoChart').getContext('2d');
    var totalBaixadoChart = new Chart(ctxTotalBaixado, {
        type: 'bar',
        data: {
            labels: ['Total Baixado'],
            datasets: [
                {
                    label: 'Banco Inter',
                    data: [<?= @$total_baixado_inter ?: 0 ?>],
                    backgroundColor: 'rgba(255, 108, 0, 0.6)', // Laranja Inter
                    borderColor: 'rgba(255, 108, 0, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Nubank',
                    data: [<?= @$total_baixado_nubank ?: 0 ?>],
                    backgroundColor: 'rgba(130, 10, 209, 0.6)', // Roxo Nubank
                    borderColor: 'rgba(130, 10, 209, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total',
                    data: [<?= @$total_baixado ?: 0 ?>],
                    backgroundColor: 'rgba(52, 58, 64, 0.6)',
                    borderColor: 'rgba(52, 58, 64, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Totais Baixados'
                }
            }
        }
    });

    // Gráfico de Total a Receber e Total a Pagar
    var ctxTotalReceberPagar = document.getElementById('totalReceberPagarChart').getContext('2d');
    var totalReceberPagarChart = new Chart(ctxTotalReceberPagar, {
        type: 'bar',
        data: {
            labels: ['Totais Financeiros'],
            datasets: [
                {
                    label: 'Total a Receber',
                    data: [<?= @$total_a_receber ?: 0 ?>],
                    backgroundColor: 'rgba(25, 135, 84, 0.6)', // Verde
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total a Pagar',
                    data: [<?= @$total_a_pagar ?: 0 ?>],
                    backgroundColor: 'rgba(220, 53, 69, 0.6)', // Vermelho
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Total a Receber e Total a Pagar'
                }
            }
        }
    });
</script>
<!-- Fim do código adicionado -->
