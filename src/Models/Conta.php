<?php

class Conta
{
    private int $id;
    private string $nome;
    private int $saldo_em_centavos;
    private string $data_criacao;
    private string $data_edicao;

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
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of saldo_em_centavos
     */ 
    public function getSaldo_em_centavos()
    {
        return $this->saldo_em_centavos;
    }

    /**
     * Set the value of saldo_em_centavos
     *
     * @return self
     */ 
    public function setSaldo_em_centavos($saldo_em_centavos)
    {
        $this->saldo_em_centavos = $saldo_em_centavos;

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
}