<style>
/* Tamanho específico de cada coluna */
.table .col-mov {
    width: 3%;
    text-align: center;
}

.table .col-descricao {
    width: 45%;
}

.table .col-fase {
    width: 7%;
    text-align: center;
}

.table .col-status {
    width: 7%;
    text-align: center;
}

.table .col-data {
    width: 15%;
    text-align: center;
}

.table .col-usuario {
    width: 10%;
    text-align: center;
}

.table .col-anexos {
    width: 10%;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

</style>

<div>
    <div class="container-fluid mt-2">

        <!-- Card principal da Visualização -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white p-2">
                <h2 class="mb-0">Visualizar NDI</h2>
            </div>
            <div class="card" style="font-size: 0.9em;">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        NDI: <?= $ndi->id_ndi ?? '---' ?> 
                        | Prioridade: <?= ($ndi->prioridade == 'U') ? 'Urgente' : 'Normal' ?>
                    </h5>
                </div>

                <div class="card-body p-2">
                    <!-- Linha superior: NDI e DADOS -->
                    <div class="row">
                        <!-- NDI -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-1 mb-2">NDI</h6>
                
                            <div>
                                <strong class="me-2">Assunto:</strong>
                                <span class="text-muted"><?= $ndi->assunto ?: '---' ?></span>
                            </div>
                
                            <div>
                                <strong class="me-2">Nº Processo:</strong>
                                <span id="processoExibido" class="text-muted"><?= $ndi->processo ?: '---' ?></span>
                            </div>
                
                            <div>
                                <strong class="me-2">Data de Abertura:</strong>
                                <span class="text-muted">
                                    <?= isset($ndi->dt_abertura) 
                                        ? date('d/m/Y', strtotime($ndi->dt_abertura)) 
                                        : '---' 
                                    ?>
                                </span>
                            </div>
                        </div>
                
                        <!-- DADOS -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-1 mb-2">DADOS</h6>
                
                            <div>
                                <strong class="me-2">Responsável:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeResponsavel = '---';
                                        foreach ($usersAtivos as $users) {
                                            if ($ndi->id_responsavel == $users->id_usuario) {
                                                $nomeResponsavel = $users->username;
                                                break;
                                            }
                                        }
                                        echo $nomeResponsavel;
                                    ?>
                                </span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Cliente:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeCliente = '---';
                                        foreach ($clientes as $cliente) {
                                            if ($ndi->id_cliente == $cliente->id_pessoa) {
                                                $nomeCliente = $cliente->nm_pessoa;
                                                break;
                                            }
                                        }
                                        echo $nomeCliente;
                                    ?>
                                </span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">UF / Cidade:</strong>
                                <span class="text-muted">
                                    <?= ($ndi->uf ?: '---') . ' / ' . ($ndi->cidade ?: '---') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                                    
                    <!-- Linha inferior: INFO DO PROCESSO e STATUS DO OFICIO -->
                    <div class="row">
                        <!-- INFO DO PROCESSO -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-1 mb-2 mt-3">INFO DO PROCESSO</h6>
                            <div>
                                <strong class="me-2">Autor:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeAutor = '---';
                                        foreach ($pessoasParte as $pessoasAutor) {
                                            if ($ndi->id_promovente == $pessoasAutor->id_pessoa) {
                                                $nomeAutor = $pessoasAutor->nm_pessoa;
                                                break;
                                            }
                                        }
                                        echo $nomeAutor;
                                    ?>
                                </span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Advogado do Autor:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeAdvAutor = '---';
                                        foreach ($pessoasAdv as $pessoasAutorAdv) {
                                            if ($ndi->id_advogado_autor == $pessoasAutorAdv->id_pessoa) {
                                                $nomeAdvAutor = $pessoasAutorAdv->nm_pessoa;
                                                break;
                                            }
                                        }
                                        echo $nomeAdvAutor;
                                    ?>
                                </span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Complexidade:</strong>
                                <span class="text-muted"><?= $ndi->complexidade ?: '-' ?></span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Réu:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeReu = '---';
                                        foreach ($pessoasParte as $pessoasReu) {
                                            if ($ndi->id_promovido == $pessoasReu->id_pessoa) {
                                                $nomeReu = $pessoasReu->nm_pessoa;
                                                break;
                                            }
                                        }
                                        echo $nomeReu;
                                    ?>
                                </span>
                            </div>
                                    
                            <div class="mb-2">
                                <strong class="me-2">Advogado do Réu:</strong>
                                <span class="text-muted">
                                    <?php
                                        $nomeAdvReu = '---';
                                        foreach ($pessoasAdv as $pessoasReuAdv) {
                                            if ($ndi->id_advogado_reu == $pessoasReuAdv->id_pessoa) {
                                                $nomeAdvReu = $pessoasReuAdv->nm_pessoa;
                                                break;
                                            }
                                        }
                                        echo $nomeAdvReu;
                                    ?>
                                </span>
                            </div>
                                    
                            <!-- <div class="mb-3">
                                <strong class="d-block mb-1">Observações:</strong>
                                <textarea 
                                    class="form-control form-control-sm" 
                                    rows="5" 
                                    readonly
                                ><?= isset($ndi->observacoes) ? $ndi->observacoes : 'Sem observações.' ?></textarea>
                            </div> -->
                        </div>
                                    
                        <!-- STATUS DO OFICIO -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-1 mb-2 mt-3">STATUS DO NDI</h6>
                                    
                            <div>
                                <strong class="me-2">Fase:</strong>
                                <span class="text-muted"><?= $ndi->nm_fase ?? '---' ?></span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Status:</strong>
                                <span class="text-muted"><?= $ndi->nm_status ?? '---' ?></span>
                            </div>
                                    
                            <div>
                                <strong class="me-2">Prazo:</strong>
                                <span class="text-muted">
                                    <?= isset($ndi->dt_prazo)
                                        ? date('d/m/Y', strtotime(datetime: $ndi->dt_prazo))
                                        : '---'
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php if (!in_array($ndi->situacao, ['C', 'F', 'R'])) { ?>
                                <a href="<?= base_url('juridico/ndiControllers/editar/' . $ndi->id_ndi) ?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                            <?php } ?>
                            <?php if (in_array($ndi->situacao, ['C', 'F', 'R'])) { ?>
                                <a href="<?= base_url('juridico/ndiControllers/reabrirNdi/' . $ndi->id_ndi) ?>" class="btn btn-sm btn-info">
                                    Reabrir NDI
                                </a>
                            <?php } ?>
                            <a href="<?= base_url('juridico/ndiControllers') ?>" class="btn btn-sm btn-secondary">
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card para histórico/movimentações -->
        <div class="card shadow-sm mt-3">
            <!-- Altere aqui a cor de fundo do cabeçalho -->
            <div class="card-header bg-secondary text-white p-2">
                <h4 class="mb-0" style="font-size:1.1rem;">Histórico de Movimentações</h4>
            </div>
            <div class="card-body p-2">
                <!-- Verifica se o NDI está fechado (C, F ou R) -->
                <?php if (in_array($ndi->situacao, ['C', 'F', 'R'])) { ?>
                    <!-- Aviso de que o NDI está fechado -->
                    <div class="alert alert-warning mt-2">
                        <?php if ($ndi->situacao == 'C') { ?>
                            <strong>Este NDI está Cancelado.</strong> Não é possível adicionar novas movimentações.
                        <?php }elseif ($ndi->situacao == 'F') { ?>
                            <strong>Este NDI está Fechado.</strong> Não é possível adicionar novas movimentações.
                        <?php }elseif ($ndi->situacao == 'R') { ?>
                            <strong>Este NDI está Recusado.</strong> Não é possível adicionar novas movimentações.
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <!-- Caso o NDI esteja aberto, exibe o formulário normal de movimentação -->
                    <div class="mt-2">
                        <form action="<?= base_url('juridico/ndiControllers/adicionarMovimentacao/' . $ndi->id_ndi) ?>" 
                              method="post" 
                              enctype="multipart/form-data" 
                              class="mb-0">

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="comentario" class="form-label mb-0">Comentário</label>
                                    <textarea class="form-control form-control-sm"
                                              name="comentario"
                                              id="comentario"
                                              rows="5"
                                              placeholder="Descreva aqui a nova movimentação..."
                                              required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="id_fase" class="form-label mb-0">Fase</label>
                                            <select class="form-control" id="id_fase" name="id_fase">
                                                <option value="">Selecione a fase</option>
                                                <?php foreach ($fases as $fase) : ?>
                                                    <option value="<?= $fase->id_fase ?>" 
                                                        <?= isset($ndi->id_fase) && $ndi->id_fase == $fase->id_fase ? 'selected' : '' ?>>
                                                        <?= $fase->nm_fase ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status" class="form-label mb-0">Status</label>
                                            <select class="form-control" id="id_status" name="id_status">
                                                <option value="">Selecione o status</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="responsavel" class="form-label mb-0">Responsável</label>
                                            <select class="form-control" id="id_responsavel" name="id_responsavel">
                                                <option value="">Selecione o responsável</option>
                                                <?php foreach ($usersAtivos as $users) : ?>
                                                    <option value="<?= $users->id_usuario ?>" 
                                                        <?= isset($ndi->id_responsavel) && $ndi->id_responsavel == $users->id_usuario ? 'selected' : '' ?>>
                                                        <?= $users->username ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label for="dt_prazo" class="form-label mb-0">Data Prazo</label>
                                            <input type="text" class="form-control datepicker"
                                                   name="dt_prazo"
                                                   id="dt_prazo"
                                                   value="<?= date('d/m/Y', strtotime($ndi->dt_prazo)) ?>"
                                                   required>
                                        </div>
                                        <div class="col-md-8 mt-2">
                                            <label for="anexos" class="form-label mb-0">Anexar Arquivo(s) (opcional)</label>
                                            <input type="file" class="form-control" name="anexos[]" id="anexos" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                
                            <!-- Botão Adicionar -->
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" id="btnAdicionar" class="btn btn-sm btn-primary">
                                    Adicionar
                                </button>
                            </div>
                            <br>
                        </form>
                    </div>
                <?php } ?>

                <!-- Lista de movimentações em Tabela -->
                <?php if (!empty($movimentacoes)): ?>
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table id="ndiMov" class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-mov" style="width: 3%;">Risk.</th>
                                        <th class="col-mov" style="width: 3%;">Mov.</th>
                                        <th class="col-descricao" style="width: 45%;">Descrição</th>
                                        <th class="col-fase" style="width: 7%;">Fase</th>
                                        <th class="col-status" style="width: 7%;">Status</th>
                                        <th class="col-data" style="width: 15%;">Data do Movimento</th>
                                        <th class="col-usuario" style="width: 10%;">Usuário</th>
                                        <th class="col-anexos" style="width: 10%;">Anexos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalMovimentacoes = count($movimentacoes); // Total de movimentações
                                    foreach ($movimentacoes as $key => $mov): 
                                        $isDesconsiderado = $mov->desconsiderar == 1; // Verifica se está desconsiderado
                                    ?>
                                        <tr class="<?= $isDesconsiderado ? 'text-decoration-line-through' : '' ?>" id="movimento-<?= $mov->id_movimento ?>">
                                            <td class="col-mov">
                                                <input 
                                                    type="checkbox" 
                                                    class="mov-check" 
                                                    name="mov_selecionados[]" 
                                                    value="<?= $mov->id_movimento ?>" 
                                                    <?= $isDesconsiderado ? 'checked' : '' ?>
                                                    onchange="toggleDesconsiderar(<?= $mov->id_movimento ?>, this.checked)"
                                                />
                                            </td>
                                            <td class="col-mov"><?= $totalMovimentacoes - $key ?></td>
                                            <td class="col-descricao"><?= htmlspecialchars($mov->descricao) ?></td>
                                            <td class="col-fase"><?= !empty($mov->nm_fase) ? htmlspecialchars($mov->nm_fase) : '-' ?></td>
                                            <td class="col-status"><?= !empty($mov->nm_status) ? htmlspecialchars($mov->nm_status) : '-' ?></td>
                                            <td class="col-data">
                                                <?= date('d/m/Y H:i', strtotime($mov->dt_movimento)) ?>
                                            </td>
                                            <td class="col-usuario"><?= htmlspecialchars($mov->username) ?></td>
                                            <td class="col-anexos">
                                                <?php if (!empty($mov->anexos) && is_array($mov->anexos)): ?>
                                                    <?php foreach ($mov->anexos as $anexo): ?>
                                                        <?php
                                                            $nomeArquivo = htmlspecialchars($anexo->nm_arquivo);
                                                            $nomeTruncado = strlen($nomeArquivo) > 30 
                                                                ? substr($nomeArquivo, 0, 10) . '...' . substr($nomeArquivo, -10) 
                                                                : $nomeArquivo;
                                                        ?>
                                                        <a href="<?= base_url(htmlspecialchars($anexo->caminho_server)) ?>" target="_blank">
                                                            <?= $nomeTruncado ?>
                                                        </a><br>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="small mb-1">Nenhuma movimentação registrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $('#btnAdicionar').on('click', function (e) {
            const prazoInput = $('#dt_prazo').val();
            const dataAtual = "<?= date('d/m/Y'); ?>"; // Data atual vinda do PHP

            if (!prazoInput) return; // Se o campo estiver vazio, permite o envio normal

            // Convertendo as datas para comparação (formato d/m/Y)
            const [diaP, mesP, anoP] = prazoInput.split('/');
            const [diaA, mesA, anoA] = dataAtual.split('/');

            const prazoDate = new Date(anoP, mesP - 1, diaP);
            const dataAtualDate = new Date(anoA, mesA - 1, diaA);

            if (prazoDate < dataAtualDate) {
                e.preventDefault(); // Impede o envio do formulário
                showCustomAlert('A data de prazo não pode ser menor que a data atual.', 'danger');
            }
        });

        inicializarPaginator('#ndiMov');

        // Função para aplicar a máscara ao número do processo
        function aplicarMascaraProcesso(processo) {
            if (!processo || processo === '---') return '---';

            // Remove quaisquer caracteres não numéricos
            const somenteNumeros = processo.replace(/\D/g, '');

            // Aplica a máscara '0000000-00.0000.0.00.0000'
            return somenteNumeros.replace(/^(\d{7})(\d{2})(\d{4})(\d{1})(\d{2})(\d{4})$/, '$1-$2.$3.$4.$5.$6');
        }

        // Seleciona o elemento e aplica a máscara
        const $processoElemento = $('#processoExibido');
        const processo = $processoElemento.text().trim();

        // Aplica a máscara e atualiza o texto do elemento
        $processoElemento.text(aplicarMascaraProcesso(processo));

        const idFase = "<?= isset($ndi->id_fase) ? $ndi->id_fase : '' ?>";
        const idStatus = "<?= isset($ndi->id_status) ? $ndi->id_status : '' ?>";

        if (idFase) {
            getStatus(idFase, idStatus);
        }

        $('#id_fase').change(function () {
            const selectedFase = $(this).val(); // Obtém o ID da fase selecionada
            $('#id_status').empty().append('<option value="">Carregando...</option>'); // Feedback visual

            if (selectedFase) {
                getStatus(selectedFase);
            } else {
                $('#id_status').empty().append('<option value="">Selecione uma fase primeiro</option>');
            }
        });

        inicializarSelect2('#id_responsavel', 'Selecione um responsável.');
        inicializarSelect2('#id_cliente', 'Selecione um cliente.');
        inicializarSelect2('#id_servico', 'Selecione um serviço.');
        inicializarSelect2('#id_fase', 'Selecione a fase.');
        inicializarSelect2('#id_status', 'Selecione o status.');
    });

    function toggleDesconsiderar(idMovimento, isChecked) {
        const desconsiderar = isChecked ? 1 : 0;

        $.ajax({
            url: '<?= base_url('juridico/ndiControllers/updateDesconsiderar') ?>', // Rota para atualizar
            type: 'POST',
            data: {
                id_movimento: idMovimento,
                desconsiderar: desconsiderar
            },
            success: function (response) {
                if (response.success) {
                    const row = $(`#movimento-${idMovimento}`);
                    if (desconsiderar === 1) {
                        row.addClass('text-decoration-line-through');
                        showCustomAlert('Movimento desconsiderado com sucesso!', 'success');
                    } else {
                        row.removeClass('text-decoration-line-through');
                        showCustomAlert('Movimento considerado com sucesso!', 'success');
                    }
                } else {
                    showCustomAlert('Erro ao atualizar. Tente novamente.', 'danger');
                }
            },
            error: function () {
                showCustomAlert('Erro de conexão com o servidor. Tente novamente.', 'danger');
            }
        });
    }

    function getStatus(idFase, selectedStatus = '') {
        $.ajax({
            url: '<?= base_url('juridico/ndiControllers/getStatusPorFase') ?>',
            type: 'POST',
            data: { id_fase: idFase },
            dataType: 'json',
            success: function (response) {
                $('#id_status').empty();
                $('#id_status').append('<option value="">Selecione o status</option>');

                response.forEach(function (status) {
                    const isSelected = status.id_status == selectedStatus ? 'selected' : '';
                    $('#id_status').append(
                        `<option value="${status.id_status}" ${isSelected}>${status.nm_status}</option>`
                    );
                });
            },
            error: function () {
                showCustomAlert('Erro ao carregar os status. Tente novamente.', 'danger');
                $('#id_status').empty();
                $('#id_status').append('<option value="">Erro ao carregar</option>');
            },
        });
    }

</script>