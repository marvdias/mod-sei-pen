<?php

use PHPUnit\Extensions\Selenium2TestCase\Keys as Keys;

/**
 * Classe de teste da p�gina de tramite em bloco
 */
class PaginaTramiteEmBloco extends PaginaTeste
{
    const STA_ANDAMENTO_PROCESSAMENTO = "Aguardando Processamento";
    const STA_ANDAMENTO_CANCELADO = "Cancelado";
    const STA_ANDAMENTO_CONCLUIDO = "Conclu�do";

    /**
     * @inheritdoc
     */
  public function __construct($test)
    {
      parent::__construct($test);
  }

    /**
     * Selecionar processo
     * @param array $arrNumProtocolo
     * @return void
     */
  public function selecionarProcessos($arrNumProtocolo = array())
    {
    foreach ($arrNumProtocolo as $numProtocolo) {
        $chkProtocolo = $this->test->byXPath('//tr[contains(.,"'.$numProtocolo.'")]/td/div/label');
        $chkProtocolo->click();
    }
  }

    /**
     * Selecionar tramite em bloco
     * @return void
     */
  public function selecionarTramiteEmBloco()
    {
      $btnTramiteEmBloco = $this->test->byXPath(
          "//img[@alt='". mb_convert_encoding("Incluir Processos no Bloco de Tr�mite", 'UTF-8', 'ISO-8859-1') ."']"
      );
      $btnTramiteEmBloco->click();
  }

  /**
   * Seleciona um processo espec�fico com base no n�mero do protocolo formatado.
   *
   * Este m�todo busca o r�tulo que cont�m o n�mero do protocolo
   * fornecido e simula um clique sobre ele para selecionar o processo.
   *
   * @param string $numProtocoloFormatado O n�mero do protocolo formatado a ser selecionado.
   * @return void
   */
  public function selecionarProcesso($numProtocoloFormatado)
  {
    $btnTramiteEmBloco = $this->test->byXPath('//label[@title="' . $numProtocoloFormatado . '"]');
    $btnTramiteEmBloco->click();
  }

    /**
     * Selecionar bloco
     * @param string $selAndamento
     * @return void
     */
  public function selecionarBloco($selAndamento)
    {
      $select = $this->test->select($this->test->byId('selBlocos'));
      $select->selectOptionByValue($selAndamento);
  }

    /**
     * Clicar em salvar
     * @return void
     */
  public function clicarSalvar()
    {
      $btnSalvar = $this->test->byXPath("//button[@name='sbmCadastrarProcessoEmBloco']");
      $btnSalvar->click();
  }

    /**
     * Buscar mensagem de alerta da p�gina
     *
     * @return string
     */
  public function buscarMensagemAlerta()
    {
      $alerta = $this->test->byXPath("(//div[@id='divInfraMsg0'])[1]");
      return !empty($alerta->text()) ? $alerta->text() : "";
  }
}