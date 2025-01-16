<?php

namespace App\Controllers\Juridico;

use App\Controllers\BaseController;
use App\Models\Juridico\MovimentoModels;
use App\Models\Juridico\AnexosModels;
use App\Models\Juridico\NDIModels;
use App\Models\Juridico\ServicosModels;
use App\Models\Juridico\FasesModels;
use App\Models\Juridico\StatusModels;
use App\Models\Enderecos\EnderecosModels;
use App\Models\Pessoas\PessoasModels;
use App\Models\UserModel;

class NDIControllers extends BaseController
{
    protected $ndiModels;
    protected $servicosModels;
    protected $enderecosModels;
    protected $pessoasModel;
    protected $userModel;
    protected $faseModel;
    protected $statusModel;
    protected $movimentoModel;
    protected $anexosModel;

    public function __construct()
    {
        $this->ndiModels = new NDIModels();
        $this->servicosModels = new ServicosModels();
        $this->enderecosModels = new EnderecosModels();
        $this->pessoasModel = new PessoasModels();
        $this->userModel = new UserModel();
        $this->faseModel = new FasesModels();
        $this->statusModel = new StatusModels();
        $this->movimentoModel = new MovimentoModels();
        $this->anexosModel = new AnexosModels();
    }

    private function loadDashboardView($viewName, $data = [])
    {
        // Carrega a dashboard como parte do layout e a view passada no parâmetro $viewName
        return view('dashboard/dashboard') . view($viewName, $data);
    }

    // Método index padrão
    public function index(): string
    {
        $dados['pessoas'] = $this->pessoasModel->getAllPessoas();
        // Corrigindo o nome da função e carregando a view correta
        return $this->loadDashboardView('juridico/ndi', $dados);
    }

    protected function prepareVisualizarNDI($ndis = [])
    {
        $session = session()->get();
    
        $dados = [
            'session'      => $session,
            'ndis'         => $ndis,
            'usersAtivos'  => $this->userModel->getAllUsersAtivos(),
            'clientes'     => $this->pessoasModel->getAllPessoas(),
            'servicos'     => $this->servicosModels->getAllServicos(),
            'fases'        => $this->faseModel->getFaseAtivo(),
            'status'       => $this->statusModel->getStatusAtivo(),
        ];
    
        return $dados;
    }

    public function filtrar()
    {
        // Captura os filtros da requisição GET
        $filtros = [
            'id_ndi'         => $this->request->getGet('id_ndi'),
            'assunto'        => $this->request->getGet('assunto'),
            'processo'       => $this->request->getGet('processo'),
            'id_cliente'     => $this->request->getGet('id_cliente'),
            'id_responsavel' => $this->request->getGet('id_responsavel'),
            'prioridade'     => $this->request->getGet('prioridade'),
            'dt_abertura'    => $this->request->getGet('dt_abertura'),
            'id_fase'        => $this->request->getGet('id_fase'),
            'id_status'      => $this->request->getGet('id_status'),
            'id_servico'     => $this->request->getGet('id_servico'),
        ];
    
        // Instância da model
        $ndiModel = new NDIModels();
    
        // Obtém os NDIs filtrados
        $ndis = $ndiModel->getNDIsFiltro($filtros);
    
        // Prepara os dados para a view
        $dados = $this->prepareVisualizarNDI($ndis);
        $dados['filtros'] = $filtros; // Passa os filtros para a view
    
        // Passa os dados para a view
        return $this->loadDashboardView('juridico/visualizarNDI', $dados);
    }
    
    public function visualizarNDI()
    {
        $filtros = [
            'id_ndi'         => '',
            'assunto'        => '',
            'processo'       => '',
            'id_cliente'     => '',
            'id_responsavel' => '',
            'prioridade'     => '',
            'dt_abertura'    => '',
            'id_fase'        => '',
            'id_status'      => '',
            'id_servico'     => ''
        ];

        // Obtém os NDIs ativos ou filtrados
        $ndis = !empty($filtros) ? $this->ndiModels->getNDIsFiltro($filtros) : $this->ndiModels->getVisualizarNDI();
    
        // Prepara os dados para a view
        $dados = $this->prepareVisualizarNDI($ndis);
        $dados['filtros'] = $filtros; // Passa os filtros para a view
    
        // Passa os dados para a view
        return $this->loadDashboardView('juridico/visualizarNDI', $dados);
    }

    public function cadastrarNDI(): string
    {
        $session = session()->get();

        $dados['session'] = $session;
        $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
        $dados['usersAtivos'] = $this->userModel->getAllUsersAtivos();
        $dados['clientes'] = $this->pessoasModel->getAllPessoas();
        $dados['pessoasParte'] = $this->pessoasModel->getPessoasParte();
        $dados['pessoasAdv'] = $this->pessoasModel->getPessoasAdv();
        $dados['servicos'] = $this->servicosModels->getAllServicos();

        return $this->loadDashboardView('juridico/cadastrarNDI', $dados);
    }

    public function saveNDI()
    {
        // Dados recebidos do formulário
        $data = [
            'assunto'               => $this->request->getPost('assunto'),
            'processo'              => $this->request->getPost('processo'),
            'id_responsavel'        => $this->request->getPost('id_responsavel'),
            'id_cliente'            => $this->request->getPost('id_cliente'),
            'uf'                    => $this->request->getPost('uf'),
            'cidade'                => $this->request->getPost('cidade'),
            'id_servico'            => $this->request->getPost('id_servico'),
            'dt_abertura'           => $this->request->getPost('dt_abertura'),
            'complexidade'          => $this->request->getPost('complexidade'),
            'prioridade'            => $this->request->getPost('prioridade'),
            'id_promovente'         => $this->request->getPost('id_promovente'),
            'id_advogado_autor'     => $this->request->getPost('id_advogado_autor'),
            'id_escritorio_autor'   => $this->request->getPost('id_escritorio_autor'),
            'id_promovido'          => $this->request->getPost('id_promovido'),
            'id_advogado_reu'       => $this->request->getPost('id_advogado_reu'),
            'id_escritorio_reu'     => $this->request->getPost('id_escritorio_reu'),
            'criado_por'            => session()->get('username'), // Obtém o nome do usuário da sessão
            'dt_criacao'            => date('Y-m-d H:i:s'),       // Data e hora de criação
            'is_ativo'              => 1,                        // Define como ativo por padrão
            'dt_alteracao'          => date('Y-m-d H:i:s'),      // Data e hora de alteração
            'alterado_por'          => session()->get('username') // Nome do usuário que alterou
        ];

        // Validação de campos obrigatórios
        $requiredFields = ['assunto', 'processo', 'id_responsavel', 'id_cliente', 'dt_abertura'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return redirect()->back()->with('warning', 'Todos os campos obrigatórios devem ser preenchidos.');
            }
        }

        // Tenta salvar os dados no banco
        if ($this->ndiModels->save($data)) {
            $idNDI = $this->ndiModels->insertID(); // Obtém o ID do NDI salvo
            session()->setFlashdata('success', "NDI de número {$idNDI} salvo com sucesso!");
            return redirect()->to('juridico/ndiControllers'); // Redireciona para a listagem ou outra rota
        } else {
            // Retorna erros de validação, caso existam
            return redirect()->back()->with('error', $this->ndiModels->errors());
        }
    }

    public function visualizar($id)
    {
        $ndi = $this->ndiModels->getDetalharNDI($id);

        if ($ndi) {
            $dados['ndi'] = $ndi;
            $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
            $dados['usersAtivos'] = $this->userModel->getAllUsersAtivos();
            $dados['clientes'] = $this->pessoasModel->getAllPessoas();
            $dados['pessoasParte'] = $this->pessoasModel->getPessoasParte();
            $dados['pessoasAdv'] = $this->pessoasModel->getPessoasAdv();
            $dados['servicos'] = $this->servicosModels->getAllServicos();

            $movimentacoes = $this->movimentoModel->getMovimentoByNDI($id);

            if (!empty($movimentacoes)) {
                foreach ($movimentacoes as $key => $movimentacao) {
                    // Busca os anexos para cada movimentação
                    $movimentacoes[$key]->anexos = $this->anexosModel->getAnexosByMovimento($movimentacao->id_movimento);
                }
            }
            $dados['movimentacoes'] = $movimentacoes;
            $dados['fases'] = $this->faseModel->getFaseAtivo();
            $dados['usersAtivos'] = $this->userModel->getAllUsersAtivos();
            return $this->loadDashboardView('juridico/visualizar', $dados);
        } else {
            return redirect()->to(base_url('juridico/ndiControllers'))->with('error', 'Ofício não encontrado.');
        }
    }
    
    public function editar($id)
    {
        $ndi = $this->ndiModels->getndiByID($id);
        
        if ($ndi) {
            $dados['ndi'] = $ndi;
            $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
            $dados['usersAtivos'] = $this->userModel->getAllUsersAtivos();
            $dados['clientes'] = $this->pessoasModel->getAllPessoas();
            $dados['pessoasParte'] = $this->pessoasModel->getPessoasParte();
            $dados['pessoasAdv'] = $this->pessoasModel->getPessoasAdv();
            $dados['servicos'] = $this->servicosModels->getAllServicos();
            return $this->loadDashboardView('juridico/editar', $dados);
        } else {
            return redirect()->to(base_url('juridico/ndiControllers'))->with('error', 'Ofício não encontrado.');
        }
    }
    public function updateNDI($id)
    {
        // Verifica se o ID foi passado e é válido
        if (empty($id)) {
            return redirect()->back()->with('warning', 'ID do NDI não fornecido.');
        }

        // Dados recebidos do formulário
        $data = [
            'assunto'               => $this->request->getPost('assunto'),
            'processo'              => $this->request->getPost('processo'),
            'id_responsavel'        => $this->request->getPost('id_responsavel'),
            'id_cliente'            => $this->request->getPost('id_cliente'),
            'uf'                    => $this->request->getPost('uf'),
            'cidade'                => $this->request->getPost('cidade'),
            'id_servico'            => $this->request->getPost('id_servico'),
            'dt_abertura'           => $this->request->getPost('dt_abertura'),
            'complexidade'          => $this->request->getPost('complexidade'),
            'prioridade'            => $this->request->getPost('prioridade'),
            'id_promovente'         => $this->request->getPost('id_promovente'),
            'id_advogado_autor'     => $this->request->getPost('id_advogado_autor'),
            'id_escritorio_autor'   => $this->request->getPost('id_escritorio_autor'),
            'id_promovido'          => $this->request->getPost('id_promovido'),
            'id_advogado_reu'       => $this->request->getPost('id_advogado_reu'),
            'id_escritorio_reu'     => $this->request->getPost('id_escritorio_reu'),
            'alterado_por'          => session()->get('username'), // Nome do usuário que alterou
            'dt_alteracao'          => date('Y-m-d H:i:s'),        // Data e hora de alteração
        ];

        // Validação de campos obrigatórios
        $requiredFields = ['assunto', 'processo', 'id_responsavel', 'id_cliente', 'dt_abertura'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return redirect()->back()->with('warning', 'Todos os campos obrigatórios devem ser preenchidos.');
            }
        }

        // Tenta atualizar os dados no banco
        if ($this->ndiModels->update($id, $data)) {
            session()->setFlashdata('success', "NDI de número {$id} atualizado com sucesso!");
            return redirect()->to('juridico/ndiControllers'); // Redireciona para a listagem ou outra rota
        } else {
            // Retorna erros de validação, caso existam
            return redirect()->back()->with('error', $this->ndiModels->errors());
        }
    }

    public function excluir($id)
    {
        // Verifica se o ofício existe
        $ndi = $this->ndiModels->find($id);

        if ($ndi) {
            // Atualiza o campo 'is_ativo' para 0
            $data = [
                'is_ativo' => 0,
                'dt_alteracao' => date('Y-m-d H:i:s'),
                'alterado_por' => session()->get('username')
            ];

            $this->ndiModels->update($id, $data);
            return redirect()->to(base_url('juridico/ndiControllers'))->with('success', 'Ofício inativado com sucesso.');
        } else {
            return redirect()->to(base_url('juridico/ndiControllers'))->with('error', 'Ofício não encontrado.');
        }
    }

    public function buscarEscritorio($idAdvogado)
    {
        // Verifica se o ID do advogado foi passado
        if (empty($idAdvogado)) {
            return $this->response->setJSON(['error' => 'ID do advogado não fornecido.'])->setStatusCode(400);
        }

        // Carrega o modelo de pessoas (ajuste conforme sua estrutura de modelos)
        $pessoaModel = new PessoasModels();

        // Busca o advogado pelo ID
        $advogado = $pessoaModel->getEnderecoByPessoaID($idAdvogado);

        if (!$advogado) {
            return $this->response->setJSON(['error' => 'Advogado não encontrado.'])->setStatusCode(404);
        }

        $escritorio = ($advogado->rua . ' ' . $advogado->numero) ?? null;

        // Retorna o nome do escritório, ou mensagem caso não exista
        return $this->response->setJSON([
            'id_endereco' => $advogado->id_endereco ?? 'Id não encontrado.',
            'escritorio' => $escritorio ?? 'Escritório não cadastrado.'
        ]);
    }

    public function getStatusPorFase()
    {
        $id_fase = $this->request->getPost('id_fase');
    
        if ($id_fase) {

            $status = new StatusModels();

            $status = $status->getStatusByFase($id_fase);
        
            echo json_encode($status);
        } else {
            echo json_encode([]);
        }
    }

    public function adicionarMovimentacao($id_ndi = null)
    {
        if (empty($id_ndi)) {
            $id_ndi = $this->request->getPost('id_ndi');
        }

        if (empty($id_ndi)) {
            return redirect()->back()->with('warning', 'ID do NDI não fornecido.');
        }
    
        $dataMov = [
            'dt_movimento' => date('Y-m-d H:i:s'),
            'id_usuario'   => session()->get('id_usuario'),
            'descricao'    => $this->request->getPost('comentario'),
            'id_ndi'       => $id_ndi,
            'id_fase'      => $this->request->getPost('id_fase'),
            'id_status'    => $this->request->getPost('id_status'),
            'id_responsavel' => $this->request->getPost('id_responsavel'),
        ];

        $dataPrazo = $this->request->getPost('dt_prazo');
        if (!empty($dataPrazo)) {
            $dataMov['dt_prazo'] = $dataPrazo;
        }

        try {
            $this->movimentoModel->insert($dataMov);
            $id_movimento = $this->movimentoModel->insertID();

            $dataNdiUpdate = [
                'id_fase'        => $this->request->getPost('id_fase'),
                'id_status'      => $this->request->getPost('id_status'),
                'id_responsavel' => $this->request->getPost('id_responsavel'),
                'dt_prazo'       => $dataPrazo,
                'dt_alteracao'   => date('Y-m-d H:i:s'), // Data e hora de alteração
                'alterado_por'   => session()->get('username'), // Nome do usuário que alterou
            ];

            $this->ndiModels->update($id_ndi, $dataNdiUpdate);

            $anexosModel = new \App\Models\Juridico\AnexosModels();
            $arquivos = $this->request->getFiles();

            if (!empty($arquivos['anexos'])) {
                foreach ($arquivos['anexos'] as $file) {
                    if ($file->isValid()) {
                        $novoNome = $file->getRandomName();
                        $file->move(FCPATH . 'storage/NDI', $novoNome);

                        $extensao = $file->getClientExtension();
                        $dataAnexo = [
                            'id_movimento'   => $id_movimento,
                            'caminho_server' => 'storage/NDI/' . $novoNome,
                            'nm_arquivo'     => $file->getClientName(),
                            'extensao'       => $extensao,
                            'file_server'    => $novoNome,
                            'dt_criacao'     => date('Y-m-d H:i:s'),
                        ];

                        $anexosModel->insert($dataAnexo);
                    }
                }
            }

            return redirect()->back()->with('success', "Movimentação #{$id_movimento} adicionada com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao adicionar movimentação: ' . $e->getMessage());
        }
    }
}
