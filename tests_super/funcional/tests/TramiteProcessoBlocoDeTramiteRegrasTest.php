<?php

use Tests\Funcional\Sei\Fixtures\{ProtocoloFixture,ProcedimentoFixture,AtividadeFixture,ContatoFixture,ParticipanteFixture,RelProtocoloAssuntoFixture,AtributoAndamentoFixture,DocumentoFixture,AssinaturaFixture,AnexoFixture,AnexoProcessoFixture};

/**
 *
 * Execution Groups
 * @group execute_parallel_group1
 */
class TramiteProcessoBlocoDeTramiteRegrasTest extends FixtureCenarioBaseTestCase
{
    public static $remetente;
    public static $destinatario;
    public static $processoTeste;
    public static $documentoTeste;
    public static $objBlocoDeTramiteDTO;

    public static function setUpBeforeClass():void
    {
        $objBlocoDeTramiteFixture = new \BlocoDeTramiteFixture();
        self::$objBlocoDeTramiteDTO = $objBlocoDeTramiteFixture->carregar();
    }

    /**
     * Teste pra validar mensagem de documento n�o assinado ao ser inserido em bloco
     *
     * @group envio
     * @large
     *
     * @return void
     */
    public function test_validar_mensagem_de_documento_nao_assinado()
    {
        // Configura��o do dados para teste do cen�rio
        self::$remetente = $this->definirContextoTeste(CONTEXTO_ORGAO_A);
        self::$processoTeste = $this->gerarDadosProcessoTeste(self::$remetente);
        self::$documentoTeste = $this->gerarDadosDocumentoInternoTeste(self::$remetente);

        // Cadastrar novo processo de teste
        $objProtocoloDTO = $this->cadastrarProcessoFixture(self::$processoTeste);

        // Incluir e assinar documento no processo
        $dadosDocumentoDTO = [
            'IdProtocolo' => $objProtocoloDTO->getDblIdProtocolo(),
            'IdProcedimento' => $objProtocoloDTO->getDblIdProtocolo(),
            'Descricao' => self::$documentoTeste['DESCRICAO'],
            'IdHipoteseLegal' => self::$documentoTeste["HIPOTESE_LEGAL"],
            'StaNivelAcessoGlobal' => self::$documentoTeste["RESTRICAO"],
            'StaNivelAcessoLocal' => self::$documentoTeste["RESTRICAO"],
        ];

        if ($serieDTO = $this->buscarIdSerieDoDocumento(self::$documentoTeste['TIPO_DOCUMENTO'])) {
            $dadosDocumentoDTO['IdSerie'] = $serieDTO->getNumIdSerie();
        }

        $objDocumentoFixture = new DocumentoFixture();
        $objDocumentoDTO = $objDocumentoFixture->carregar($dadosDocumentoDTO);

        // Acessar sistema do this->REMETENTE do processo
        $this->acessarSistema(self::$remetente['URL'], self::$remetente['SIGLA_UNIDADE'], self::$remetente['LOGIN'], self::$remetente['SENHA']);

        $this->paginaBase->navegarParaControleProcesso();
        $this->paginaTramiteEmBloco->selecionarProcessos([$objProtocoloDTO->getStrProtocoloFormatado()]);
        $this->paginaTramiteEmBloco->selecionarTramiteEmBloco();
        $this->paginaTramiteEmBloco->selecionarBloco(self::$objBlocoDeTramiteDTO->getNumId());
        $this->paginaTramiteEmBloco->clicarSalvar();
        sleep(2);

        $mensagem = $this->paginaTramiteEmBloco->buscarMensagemAlerta();
        $this->assertStringContainsString(
            utf8_encode('N�o � poss�vel tramitar um processos com documentos gerados e n�o assinados'),
            $mensagem
        );
    }

    /**
     * Teste pra validar mensagem de processo bloqueado ao ser inserido em bloco 
     *
     * @group envio
     * @large
     *
     * @return void
     */
    public function test_validar_mensagem_de_processo_bloqueado()
    {
        // Configura��o do dados para teste do cen�rio
        self::$remetente = $this->definirContextoTeste(CONTEXTO_ORGAO_A);
        self::$processoTeste = $this->gerarDadosProcessoTeste(self::$remetente);
        self::$documentoTeste = $this->gerarDadosDocumentoInternoTeste(self::$remetente);

        // Cadastrar novo processo de teste
        $objProtocoloDTO = $this->cadastrarProcessoFixture(self::$processoTeste);

        // Incluir e assinar documento no processo
        $this->cadastrarDocumentoInternoFixture(self::$documentoTeste, $objProtocoloDTO->getDblIdProtocolo());

        $bancoOrgaoA = new DatabaseUtils(CONTEXTO_ORGAO_A);
        $bancoOrgaoA->execute("update protocolo set sta_estado=? where id_protocolo=?;", array(4, $objProtocoloDTO->getDblIdProtocolo()));

        // Acessar sistema do this->REMETENTE do processo
        $this->acessarSistema(self::$remetente['URL'], self::$remetente['SIGLA_UNIDADE'], self::$remetente['LOGIN'], self::$remetente['SENHA']);

        $this->paginaBase->navegarParaControleProcesso();
        $this->paginaTramiteEmBloco->selecionarProcessos([$objProtocoloDTO->getStrProtocoloFormatado()]);
        $this->paginaTramiteEmBloco->selecionarTramiteEmBloco();
        $this->paginaTramiteEmBloco->selecionarBloco(self::$objBlocoDeTramiteDTO->getNumId());
        $this->paginaTramiteEmBloco->clicarSalvar();
        sleep(2);

        $mensagem = $this->paginaTramiteEmBloco->buscarMensagemAlerta();
        $this->assertStringContainsString(
            utf8_encode('Prezado(a) usu�rio(a), o processo ' . $objProtocoloDTO->getStrProtocoloFormatado() . ' encontra-se bloqueado. Dessa forma, n�o foi poss�vel realizar a sua inser��o no bloco selecionado.'),
            $mensagem
        );
    }

    /**
     * Teste pra validar a mensagem de processo aberto em mais de uma unidade ao ser inserido em bloco
     *
     * @group envio
     * @large
     *
     * @return void
     */
    public function test_validar_mensagem_de_processo_aberto_em_mais_de_uma_unidade()
    {
        // Configura��o do dados para teste do cen�rio
        self::$remetente = $this->definirContextoTeste(CONTEXTO_ORGAO_A);
        self::$processoTeste = $this->gerarDadosProcessoTeste(self::$remetente);
        self::$documentoTeste = $this->gerarDadosDocumentoInternoTeste(self::$remetente);

        // Cadastrar novo processo de teste
        $objProtocoloDTO = $this->cadastrarProcessoFixture(self::$processoTeste);

        // Incluir e assinar documento no processo
        $this->cadastrarDocumentoInternoFixture(self::$documentoTeste, $objProtocoloDTO->getDblIdProtocolo());

        // Acessar sistema do this->REMETENTE do processo
        $this->acessarSistema(self::$remetente['URL'], self::$remetente['SIGLA_UNIDADE'], self::$remetente['LOGIN'], self::$remetente['SENHA']);

        $this->abrirProcesso($objProtocoloDTO->getStrProtocoloFormatado());

        $this->tramitarProcessoInternamente(self::$remetente['SIGLA_UNIDADE_SECUNDARIA'], true);

        $this->paginaBase->navegarParaControleProcesso();
        $this->paginaTramiteEmBloco->selecionarProcessos([$objProtocoloDTO->getStrProtocoloFormatado()]);
        $this->paginaTramiteEmBloco->selecionarTramiteEmBloco();
        $this->paginaTramiteEmBloco->selecionarBloco(self::$objBlocoDeTramiteDTO->getNumId());
        $this->paginaTramiteEmBloco->clicarSalvar();
        sleep(2);

        $mensagem = $this->paginaTramiteEmBloco->buscarMensagemAlerta();
        $this->assertStringContainsString(
            utf8_encode('N�o � poss�vel tramitar um processo aberto em mais de uma unidade.'),
            $mensagem
        );
        $this->assertStringContainsString(
            utf8_encode('Processo ' . $objProtocoloDTO->getStrProtocoloFormatado() . ' est� aberto na(s) unidade(s): ' . self::$remetente['SIGLA_UNIDADE_SECUNDARIA']),
            $mensagem
        );
    }
}
