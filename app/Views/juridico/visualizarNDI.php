<style>
    /* Estilização personalizada do header do filtro */
    .card-header.bg-primary-custom {
        background-color: #343a40;
        color: #ffffff;
    }

    .card-header.bg-secondary-custom {
        background-color: #6c757d;
        color: #ffffff;
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
                    <!-- Formulário para filtrar os NDIs -->
                    <form action="<?= base_url('juridico/ndiControllers') ?>" method="GET">
                        <div class="row g-3">
                            <!-- ID do NDI -->
                            <div class="col-md-2">
                                <label for="id_ndi" class="form-label">ID do NDI</label>
                                <input type="text" name="id_ndi" id="id_ndi" class="form-control" placeholder="Ex: 123" 
                                    value="<?= isset($filtros['id_ndi']) ? $filtros['id_ndi'] : '' ?>">
                            </div>

                            <!-- Assunto -->
                            <div class="col-md-2">
                                <label for="assunto" class="form-label">Assunto</label>
                                <input type="text" name="assunto" id="assunto" class="form-control" placeholder="Ex: Contrato" 
                                    value="<?= isset($filtros['assunto']) ? $filtros['assunto'] : '' ?>">
                            </div>

                            <!-- Processo -->
                            <div class="col-md-2">
                                <label for="processo" class="form-label">Processo</label>
                                <input type="text" name="processo" id="processo" class="form-control" placeholder="Ex: 123456" 
                                    value="<?= isset($filtros['processo']) ? $filtros['processo'] : '' ?>">
                            </div>

                            <!-- Cliente -->
                            <div class="col-md-2">
                                <label for="id_cliente" class="form-label">Cliente</label>
                                <select class="form-control" id="id_cliente" name="id_cliente">
                                    <option value="">Selecione o cliente</option>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <option value="<?= $cliente->id_pessoa ?>" 
                                            <?= isset($filtros['id_cliente']) && $filtros['id_cliente'] == $cliente->id_pessoa ? 'selected' : '' ?>>
                                            <?= $cliente->nm_pessoa ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                                    
                            <!-- Prioridade -->
                            <div class="col-md-2">
                                <label for="prioridade" class="form-label">Prioridade</label>
                                <select name="prioridade" id="prioridade" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="N" <?= isset($filtros['prioridade']) && $filtros['prioridade'] == 'N' ? 'selected' : '' ?>>Normal</option>
                                    <option value="U" <?= isset($filtros['prioridade']) && $filtros['prioridade'] == 'U' ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>   

                            <!-- Serviço -->
                            <div class="col-md-2">
                                <label for="id_servico" class="form-label">Serviço</label>
                                <select class="form-control" id="id_servico" name="id_servico">
                                    <option value="">Selecione</option>
                                    <?php foreach ($servicos as $servico) : ?>
                                        <option value="<?= $servico->id_servico ?>" 
                                            <?= isset($filtros['id_servico']) && $filtros['id_servico'] == $servico->id_servico ? 'selected' : '' ?>>
                                            <?= $servico->nm_servico ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Fase -->
                            <div class="col-md-2">
                                <label for="id_fase" class="form-label">Fase</label>
                                <select class="form-control" id="id_fase" name="id_fase">
                                    <option value="">Selecione a fase</option>
                                    <?php foreach ($fases as $fase) : ?>
                                        <option value="<?= $fase->id_fase ?>" 
                                            <?= isset($filtros['id_fase']) && $filtros['id_fase'] == $fase->id_fase ? 'selected' : '' ?>>
                                            <?= $fase->nm_fase ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                                    
                            <!-- Status -->
                            <div class="col-md-2">
                                <label for="id_status" class="form-label">Status</label>
                                <select class="form-control" id="id_status" name="id_status">
                                    <option value="">Selecione o status</option>
                                </select>
                            </div>

                            <!-- Situação -->
                            <div class="col-md-2">
                                <label for="situacao" class="form-label">Situação</label>
                                <select class="form-control" id="situacao" name="situacao">
                                    <option value="">Selecione</option>
                                    <?php foreach ($situacao as $situ => $key) : ?>
                                        <option value="<?= $situ ?>" 
                                            <?= isset($filtros['situacao']) && $filtros['situacao'] == $situ ? 'selected' : '' ?>>
                                            <?= $key ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Responsável -->
                            <div class="col-md-2">
                                <label for="id_responsavel" class="form-label">Responsável</label>
                                <select class="form-control" id="id_responsavel" name="id_responsavel">
                                    <option value="">Selecione o responsável</option>
                                    <?php foreach ($usersAtivos as $users) : ?>
                                        <option value="<?= $users->id_usuario ?>" 
                                            <?= isset($filtros['id_responsavel']) && $filtros['id_responsavel'] == $users->id_usuario ? 'selected' : '' ?>>
                                            <?= $users->username ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Data de Abertura -->
                            <div class="col-md-2">
                                <label for="dt_abertura" class="form-label">Data de Abertura</label>
                                <input type="text" name="dt_abertura" id="dt_abertura" class="form-control datepicker" 
                                    value="<?= isset($filtros['dt_abertura']) ? $filtros['dt_abertura'] : '' ?>">
                            </div>
                        </div>
                                    
                        <!-- Botão de Filtrar -->
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary-custom text-white d-flex justify-content-between align-items-center">
                <span>Listagem de NDIs</span>
                <a href="<?= base_url('juridico/ndiControllers/novo') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Novo NDI
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="listNDI" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Assunto</th>
                                <th>Cliente</th>
                                <th>Processo</th>
                                <th>Fase</th>
                                <th>Status</th>
                                <th>Dt. Abertura</th>
                                <th>Responsável</th>
                                <th>Serviço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ndis) ) { ?>
                                <?php foreach ($ndis as $ndi) { ?>
                                    <tr class="<?= ($ndi->prioridade === 'U') ? 'table-danger' : '' ?>">
                                        <td><?= $ndi->id_ndi ?></td>
                                        <td><?= $ndi->assunto ?></td>
                                        <td><?= $ndi->cliente ?></td>
                                        <td class="processo-col"><?= $ndi->processo ?></td>
                                        <td><?= $ndi->nm_fase ?></td>
                                        <td><?= $ndi->nm_status ?></td>
                                        <td><?= date('d/m/Y', strtotime($ndi->dt_abertura)) ?></td>
                                        <td><?= $ndi->responsavel ?></td>
                                        <td><?= $ndi->nm_servico ?></td>
                                        <td>
                                            <a href="<?= base_url('juridico/ndiControllers/editar/' . $ndi->id_ndi) ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="confirmarExclusao('<?= base_url('juridico/ndiControllers/excluir/' . $ndi->id_ndi) ?>')"><i class="fa fa-trash-alt"></i></a>
                                            <a href="<?= base_url('juridico/ndiControllers/visualizar/' . $ndi->id_ndi) ?>" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center">Nenhum NDI encontrado.</td>
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

        <?php if (!empty($ndis) ) { ?>
            inicializarPaginator('#listNDI');
        <?php } ?>

        function aplicarMascaraProcesso(processo) {
            if (!processo || processo === '---') return '---';

            const somenteNumeros = processo.replace(/\D/g, '');

            return somenteNumeros.replace(/^(\d{7})(\d{2})(\d{4})(\d{1})(\d{2})(\d{4})$/, '$1-$2.$3.$4.$5.$6');
        }

        $('.processo-col').each(function () {
            const processoOriginal = $(this).text().trim();
            const processoMascarado = aplicarMascaraProcesso(processoOriginal);
            $(this).text(processoMascarado);
        });

        inicializarSelect2('#id_responsavel', 'Selecione um responsável.');
        inicializarSelect2('#id_cliente', 'Selecione um cliente.');
        inicializarSelect2('#id_servico', 'Selecione um serviço.');
        inicializarSelect2('#id_fase', 'Selecione a fase.');
        inicializarSelect2('#id_status', 'Selecione o status.');

        const idFase = "<?= isset($filtros['id_fase']) ? $filtros['id_fase'] : '' ?>";
        const idStatus = "<?= isset($filtros['id_status']) ? $filtros['id_status'] : '' ?>";

        if (idFase) {
            getStatus(idFase, idStatus);
        }

        $('#id_fase').change(function () {
            const selectedFase = $(this).val(); 
            $('#id_status').empty().append('<option value="">Carregando...</option>'); 

            if (selectedFase) {
                getStatus(selectedFase);
            } else {
                $('#id_status').empty().append('<option value="">Selecione uma fase primeiro</option>');
            }
        });
    });

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
                alert('Erro ao carregar os status. Tente novamente.');
                $('#id_status').empty();
                $('#id_status').append('<option value="">Erro ao carregar</option>');
            },
        });
    }

    function confirmarExclusao(url) {
        Swal.fire({
            title: 'Tem certeza?',
            text: 'Você não poderá reverter esta ação!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
