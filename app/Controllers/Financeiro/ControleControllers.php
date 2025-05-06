<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Juridico\NDIModels;
use App\Models\Financeiro\LanParcelaModels;
use App\Models\Financeiro\AnexosModel;

class ControleControllers extends BaseController
{

    protected $lancamentosModels;
    protected $lanParcelaModels;
    protected $pessoasModel;
    protected $anexosModel;
    protected $ndiModels;

    public function __construct()
    {
        $this->lancamentosModels = new LancamentosModels();
        $this->lanParcelaModels = new LanParcelaModels();
        $this->pessoasModel = new PessoasModels();
        $this->anexosModel = new AnexosModel();
        $this->ndiModels = new NDIModels();
    }

    public function index(): string
    {
        $session = session()->get();
        $dados['session'] = $session;
    
        // Totais Gerais (sem filtro por conta)
        $dados['total_a_receber'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'A'); // Total a receber em aberto
        $dados['total_recebido'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'B');  // Total recebido
        $dados['total_a_pagar'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'A');   // Total a pagar em aberto
        $dados['total_pago'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'B');      // Total pago
        $dados['total_aberto'] = $this->lancamentosModels->getTotalByStatus('A');                // Total em aberto
        $dados['total_baixado'] = $this->lancamentosModels->getTotalByStatus('B');               // Total baixado
    
        // Totais Banco Inter (id_conta = 1)
        $dados['total_baixado_inter'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 1);
    
        // Totais Nubank (id_conta = 2)
        $dados['total_baixado_nubank'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 2);
    
        // Carregar a view com os dados
        return $this->render('financeiro/controleContas', $dados);
    }
    
}