<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Juridico\NDIModels;
use App\Models\Financeiro\LanParcelaModels;
use App\Models\Financeiro\AnexosModel;

class RotinaFinanceiraControllers extends BaseController
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
    
        $dados['total_a_receber'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'A'); 
        $dados['total_recebido'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'B'); 
        $dados['total_a_pagar'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'A'); 
        $dados['total_pago'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'B'); 
        $dados['total_aberto'] = $this->lancamentosModels->getTotalByStatus('A'); 
        $dados['total_baixado'] = $this->lancamentosModels->getTotalByStatus('B'); 
    
        $dados['total_baixado_inter'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 1);
    
        $dados['total_baixado_nubank'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 2);
    
        return $this->render('financeiro/rotinaFinanceira', $dados);
    }
}