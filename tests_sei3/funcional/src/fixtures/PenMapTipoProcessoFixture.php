<?php

/**
 * Respons�vel por cadastrar novo mapeamento de tipo de processo
 */
class PenMapTipoProcessoFixture extends FixtureBase
{

    protected function inicializarObjInfraIBanco()
    {
        return \BancoSEI::getInstance();
    }

    public function cadastrar($dados = [])
    {

        $objPenMapTipoProcedimentoDTO = new \PenMapTipoProcedimentoDTO();
        $objPenMapTipoProcedimentoDTO->setNumIdMapOrgao($dados['IdMapOrgao']);
        $objPenMapTipoProcedimentoDTO->setNumIdTipoProcessoOrigem($dados['IdTipoProcessoOrigem'] ?: 100000256);
        $objPenMapTipoProcedimentoDTO->setNumIdTipoProcessoDestino($dados['IdTipoProcessoDestino'] ?: 100000256);
        $objPenMapTipoProcedimentoDTO->setStrNomeTipoProcesso($dados['NomeTipoProcesso'] ?: "Arrecada��o: Cobran�a");
        $objPenMapTipoProcedimentoDTO->setStrAtivo($dados['Ativo'] ?: 'S');
        $objPenMapTipoProcedimentoDTO->setNumIdUnidade($dados['IdUnidade'] ?: 110000001);
        $objPenMapTipoProcedimentoDTO->setDthRegistro($dados['Registro'] ?: \InfraData::getStrDataAtual());

        $objPenMapTipoProcedimentoBD = new \PenMapTipoProcedimentoBD(\BancoSEI::getInstance());
        $arrPenMapTipoProcedimentoDTO = $objPenMapTipoProcedimentoBD->cadastrar($objPenMapTipoProcedimentoDTO);
        return $arrPenMapTipoProcedimentoDTO;
    }
   
}
