<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
     <div class="header">
        <h1>Relatório de Acidentes</h1>
    </div>
    <div class="container">
        <?php if ($erro): ?>
            <p class="error"><?php echo $erro; ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?php echo $sucesso; ?></p>
        <?php endif; ?>

        <h2>Registrar Novo Acidente</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="data">Data do Acidente:</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição do Acidente:</label>
                <textarea id="descricao" name="descricao" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="produto_afetado">Produto Afetado:</label>
                <input type="text" id="produto_afetado" name="produto_afetado" required>
            </div>
            <div class="form-group">
                <label for="quantidade_afetada">Quantidade Afetada:</label>
                <input type="number" id="quantidade_afetada" name="quantidade_afetada" min="1" required>
            </div>
            <div class="form-group">
                <label for="localizacao">Localização:</label>
                <input type="text" id="localizacao" name="localizacao">
            </div>
            <button type="submit">Registrar Acidente</button>
        </form>

</body>
</html>