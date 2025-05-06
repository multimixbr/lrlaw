<style>
    .card-header.bg-primary-custom {
        background-color: #343a40;
        color: #ffffff;
    }
    
    .table td {
        text-align: center;
        vertical-align: middle;
    }

    .icon-status {
        font-size: 1.5rem;
    }
</style>

<div class="container-fluid mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
            <span>Gestão de Usuários</span>
            <a href="<?= base_url('pessoas/pessoasControllers/novo') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Novo Usuários
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelaUsuarios" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Permissões</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)) { 
                            foreach ($usuarios as $usuario) { ?>
                                <tr>
                                    <td><?= $usuario->id_usuario ?></td>
                                    <td><?= $usuario->username ?></td>
                                    <td><?= $usuario->email ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/permissaoControllers/alterar/' . $usuario->id_usuario) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-user-shield"></i> Editar Permissões
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('funcionarios/editar/' . $usuario->id_usuario) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('funcionarios/excluir/' . $usuario->id_usuario) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum Usuário encontrado.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        inicializarPaginator('#tabelaUsuarios');
    });
</script>