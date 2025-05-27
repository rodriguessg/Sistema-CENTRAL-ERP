<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastramento de Certidões</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" href="src/contratos/style/certidoes.css">
          <!-- <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css"> -->
</head>
<body class="caderno" >
    <div class="form-container">
        <h2 class="mb-4"><i class="fas fa-certificate"></i> Cadastramento de Certidões</h2>
        <form id="certidaoForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="documento" class="form-label">Documento <span class="text-danger">*</span></label>
                    <select id="documento" name="documento" class="form-select" required>
                        <option value="">Selecione o tipo de certidão</option>
                        <option value="CND">CND</option>
                        <option value="CRF">CRF</option>
                        <option value="FGTS">FGTS</option>
                        <option value="INSS">INSS</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_vencimento" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                    <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome/Descrição <span class="text-danger">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: Certidão Negativa de Débitos" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fornecedor" class="form-label">Fornecedor <span class="text-danger">*</span></label>
                    <input type="text" id="fornecedor" name="fornecedor" class="form-control" placeholder="Ex.: Empresa XYZ" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="responsavel" class="form-label">Responsável <span class="text-danger">*</span></label>
                    <input type="text" id="responsavel" name="responsavel" class="form-control" placeholder="Ex.: João Silva" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="arquivo" class="form-label">Arquivo (PDF)</label>
                    <input type="file" id="arquivo" name="arquivo" class="form-control" accept=".pdf">
                </div>
                <div class="col-md-6 mb-3">
                    <button type="button" class="btn btn-secondary" id="vincularContrato"><i class="fas fa-link"></i> Vincular Contrato</button>
                    <div class="contrato-select-container mt-2">
                        <label for="contrato_id" class="form-label">Contrato</label>
                        <select id="contrato_id" name="contrato_id" class="form-select">
                            <option value="">Selecione um contrato</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Certidão</button>
        </form>
    </div>

    <div class="certidoes-container">
        <h3 class="mb-3">Certidões Cadastradas</h3>
        <div class="search-container">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchCertidao" class="form-control" placeholder="Pesquisar certidão...">
            </div>
        </div>
        <div id="certidoesList">
            <!-- Cards serão adicionados dinamicamente -->
        </div>
        <div id="feedback" class="alert alert-info hidden mt-3" role="alert"></div>
    </div>

    <!-- Modal para Editar Certidão -->
    <div class="modal fade" id="editCertidaoModal" tabindex="-1" aria-labelledby="editCertidaoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCertidaoModalLabel">Editar Certidão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCertidaoForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_certidao_id" name="certidao_id">
                        <div class="mb-3">
                            <label for="edit_documento" class="form-label">Documento <span class="text-danger">*</span></label>
                            <select id="edit_documento" name="documento" class="form-select" required>
                                <option value="">Selecione o tipo de certidão</option>
                                <option value="CND">CND</option>
                                <option value="CRF">CRF</option>
                                <option value="FGTS">FGTS</option>
                                <option value="INSS">INSS</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_data_vencimento" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                            <input type="date" id="edit_data_vencimento" name="data_vencimento" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nome" class="form-label">Nome/Descrição <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_fornecedor" class="form-label">Fornecedor <span class="text-danger">*</span></label>
                            <input type="text" id="edit_fornecedor" name="fornecedor" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_responsavel" class="form-label">Responsável <span class="text-danger">*</span></label>
                            <input type="text" id="edit_responsavel" name="responsavel" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_arquivo" class="form-label">Arquivo (PDF)</label>
                            <input type="file" id="edit_arquivo" name="arquivo" class="form-control" accept=".pdf">
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="edit_vincularContrato"><i class="fas fa-link"></i> Vincular Contrato</button>
                            <div class="contrato-select-container mt-2">
                                <label for="edit_contrato_id" class="form-label">Contrato</label>
                                <select id="edit_contrato_id" name="contrato_id" class="form-select">
                                    <option value="">Selecione um contrato</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="btn-modal-edit">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditedCertidao()">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./src/contratos/js/certidoes.js"></script>
    <?php
include 'footer.php'
?>

</body>
</html>