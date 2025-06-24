<?php

namespace App\Models;

class Parcela
{
    private int $id;
    private int $numero_parcela;
    private int $valor_em_centavos;
    private string $data_vencimento;
    private string $data_pagamento;
    private string $data_criacao;
    private string $data_edicao;
    private int $despesa_id;
    private int $status_id;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of numero_parcela
     */ 
    public function getNumero_parcela()
    {
        return $this->numero_parcela;
    }

    /**
     * Set the value of numero_parcela
     *
     * @return self
     */ 
    public function setNumero_parcela($numero_parcela)
    {
        $this->numero_parcela = $numero_parcela;

        return $this;
    }

    /**
     * Get the value of valor_em_centavos
     */ 
    public function getValor_em_centavos()
    {
        return $this->valor_em_centavos;
    }

    /**
     * Set the value of valor_em_centavos
     *
     * @return self
     */ 
    public function setValor_em_centavos($valor_em_centavos)
    {
        $this->valor_em_centavos = $valor_em_centavos;

        return $this;
    }

    /**
     * Get the value of data_vencimento
     */ 
    public function getData_vencimento()
    {
        return $this->data_vencimento;
    }

    /**
     * Set the value of data_vencimento
     *
     * @return self
     */ 
    public function setData_vencimento($data_vencimento)
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    /**
     * Get the value of data_pagamento
     */ 
    public function getData_pagamento()
    {
        return $this->data_pagamento;
    }

    /**
     * Set the value of data_pagamento
     *
     * @return self
     */ 
    public function setData_pagamento($data_pagamento)
    {
        $this->data_pagamento = $data_pagamento;

        return $this;
    }

    /**
     * Get the value of data_criacao
     */ 
    public function getData_criacao()
    {
        return $this->data_criacao;
    }

    /**
     * Set the value of data_criacao
     *
     * @return self
     */ 
    public function setData_criacao($data_criacao)
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    /**
     * Get the value of data_edicao
     */ 
    public function getData_edicao()
    {
        return $this->data_edicao;
    }

    /**
     * Set the value of data_edicao
     *
     * @return self
     */ 
    public function setData_edicao($data_edicao)
    {
        $this->data_edicao = $data_edicao;

        return $this;
    }

    /**
     * Get the value of despesa_id
     */ 
    public function getDespesa_id()
    {
        return $this->despesa_id;
    }

    /**
     * Set the value of despesa_id
     *
     * @return self
     */ 
    public function setDespesa_id($despesa_id)
    {
        $this->despesa_id = $despesa_id;

        return $this;
    }

    /**
     * Get the value of status_id
     */ 
    public function getStatus_id()
    {
        return $this->status_id;
    }

    /**
     * Set the value of status_id
     *
     * @return self
     */ 
    public function setStatus_id($status_id)
    {
        $this->status_id = $status_id;

        return $this;
    }
}
