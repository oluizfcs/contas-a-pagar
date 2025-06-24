<?php

namespace App\Models;

class Despesa
{
    private int $id;
    private string $descricao;
    private int $valor_em_centavos;
    private string $data_criacao;
    private string $data_edicao;
    private int $usuario_id;
    private int $conta_id;
    private int $centro_de_custo_id;
    private int $fornecedor_id;

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
     * Get the value of descricao
     */ 
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return self
     */ 
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

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
     * Get the value of usuario_id
     */ 
    public function getUsuario_id()
    {
        return $this->usuario_id;
    }

    /**
     * Set the value of usuario_id
     *
     * @return self
     */ 
    public function setUsuario_id($usuario_id)
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    /**
     * Get the value of conta_id
     */ 
    public function getConta_id()
    {
        return $this->conta_id;
    }

    /**
     * Set the value of conta_id
     *
     * @return self
     */ 
    public function setConta_id($conta_id)
    {
        $this->conta_id = $conta_id;

        return $this;
    }

    /**
     * Get the value of centro_de_custo_id
     */ 
    public function getCentro_de_custo_id()
    {
        return $this->centro_de_custo_id;
    }

    /**
     * Set the value of centro_de_custo_id
     *
     * @return self
     */ 
    public function setCentro_de_custo_id($centro_de_custo_id)
    {
        $this->centro_de_custo_id = $centro_de_custo_id;

        return $this;
    }

    /**
     * Get the value of fornecedor_id
     */ 
    public function getFornecedor_id()
    {
        return $this->fornecedor_id;
    }

    /**
     * Set the value of fornecedor_id
     *
     * @return self
     */ 
    public function setFornecedor_id($fornecedor_id)
    {
        $this->fornecedor_id = $fornecedor_id;

        return $this;
    }
}