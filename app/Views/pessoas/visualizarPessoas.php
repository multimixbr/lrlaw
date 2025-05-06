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
                    <!-- Formulário para filtrar os dados -->
                    <form action="<?= base_url('pessoas/pessoasControllers') ?>" method="GET">
                        <div class="row g-3">
                            <!-- ID da Pessoa -->
                            <div class="col-md-2">
                                <label for="id_pessoa" class="form-label">ID da Pessoa</label>
                                <input type="text" name="id_pessoa" id="id_pessoa" value="<?= $filtros['id_pessoa'] ?>" class="form-control" placeholder="Ex: 123">
                            </div>
                            <!-- Nome -->
                            <div class="col-md-2">
                                <label for="nm_pessoa" class="form-label">Nome</label>
                                <input type="text" name="nm_pessoa" id="nm_pessoa" value="<?= $filtros['nm_pessoa'] ?>" class="form-control" placeholder="Ex: João Silva">
                            </div>
                            <!-- Tipo de Pessoa -->
                            <div class="col-md-2">
                                <label for="tp_pessoa" class="form-label">Tipo de Pessoa</label>
                                <select name="tp_pessoa" id="tp_pessoa" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="PF" <?= $filtros['tp_pessoa'] == 'PF' ? 'selected' : '' ?>>Física</option>
                                    <option value="PJ" <?= $filtros['tp_pessoa'] == 'PJ' ? 'selected' : '' ?>>Jurídica</option>
                                </select>
                            </div>
                            <!-- Documento -->
                            <div class="col-md-2">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="text" id="documento" name="documento" value="<?= $filtros['documento'] ?>" class="form-control" placeholder="Ex: 123.456.789-00">
                            </div>
                            <!-- Telefone -->
                            <div class="col-md-2">
                                <label for="tel_1" class="form-label">Telefone</label>
                                <input type="text" id="tel_1" name="tel_1" value="<?= $filtros['tel_1'] ?>" class="form-control" placeholder="Ex: (11) 99999-9999">
                            </div>
                            <!-- Email -->
                            <div class="col-md-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" id="email" value="<?= $filtros['email'] ?>" class="form-control" placeholder="Ex: exemplo@email.com">
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
                <span>Listagem de Pessoas Cadastradas</span>
                <a href="<?= base_url('pessoas/pessoasControllers/novo') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nova Pessoa
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="listPessoa" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo Pessoa</th>
                                <th>Documento</th>
                                <th>Tel Principal</th>
                                <th>Email</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pessoas)) { ?>
                                <?php foreach ($pessoas as $pessoa) { ?>
                                    <tr>
                                        <td><?= $pessoa->id_pessoa ?></td>
                                        <td><?= $pessoa->nm_pessoa ?></td>
                                        <td><?= $pessoa->tp_pessoa == 'PF' ? 'Física' : 'Jurídica' ?></td>
                                        <td><?= $pessoa->documento ?></td>
                                        <td><?= $pessoa->tel_1 ?></td>
                                        <td><?= $pessoa->email ?></td>
                                        <td>
                                            <a href="<?= base_url('pessoas/pessoasControllers/editar/' . $pessoa->id_pessoa) ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="confirmarExclusao('<?= base_url('pessoas/pessoasControllers/excluir/' . $pessoa->id_pessoa) ?>')"><i class="fa fa-trash-alt"></i></a>
                                            <a href="<?= base_url('pessoas/pessoasControllers/visualizar/' . $pessoa->id_pessoa) ?>" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma pessoa cadastrada.</td>
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
    $(document).ready(function() {
        inicializarPaginator('#listPessoa');
        $('#documento').inputmask({
            mask: ['999.999.999-99', '99.999.999/9999-99'],
            keepStatic: true
        });
        $('#tel_1').inputmask({
            mask: ['(99) 9999-9999', '(99) 99999-9999'],
            keepStatic: true
        });
    });
    function confirmarExclusao(url) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Você não poderá reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>