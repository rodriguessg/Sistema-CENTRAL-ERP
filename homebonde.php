<?php
// ******************************
//  viagens_completo.php
//  Tela de cadastramento, listagem, paginação e edição de viagens
//  Bonde de Santa Teresa – versão 2025‑06‑26
// ******************************

/* ------------------------------------------------------------------
   CONEXÃO COM O BANCO
   ------------------------------------------------------------------ */
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "gm_sicbd";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

/* ------------------------------------------------------------------
   DADOS PARA FORMULÁRIOS
   ------------------------------------------------------------------ */
$bondes           = $conn->query("SELECT id, modelo FROM bondes")->fetchAll(PDO::FETCH_ASSOC);
$destinos         = $conn->query("SELECT id, nome FROM destinos WHERE id IN (1, 2, 3)")->fetchAll(PDO::FETCH_ASSOC);
$destinos_retorno = $conn->query("SELECT id, nome FROM destinos WHERE id IN (4, 5)")->fetchAll(PDO::FETCH_ASSOC);
$maquinistas      = $conn->query("SELECT id, nome FROM maquinistas")->fetchAll(PDO::FETCH_ASSOC);
$agentes          = $conn->query("SELECT id, nome FROM agentes")->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------------------------------------------
   LISTAGEM DE VIAGENS
   ------------------------------------------------------------------ */
$viagens = $conn->query("SELECT v.id, b.modelo AS bonde,
                                d1.nome AS subindo,
                                d2.nome AS retorno,
                                m.nome  AS maquinista,
                                a.nome  AS agente,
                                v.hora, v.pagantes, v.gratuidade, v.moradores,
                                (v.pagantes + v.moradores + v.gratuidade) AS passageiros,
                                v.viagem
                         FROM viagens v
                         JOIN bondes     b ON v.bonde_id     = b.id
                         JOIN destinos   d1 ON v.subindo_id     = d1.id
                         LEFT JOIN destinos d2 ON v.retorno_id = d2.id
                         JOIN maquinistas m ON v.maquinista_id = m.id
                         JOIN agentes     a ON v.agente_id     = a.id
                         ORDER BY v.hora DESC")
                         ->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------------------------------------------
   TOTAIS – SUBIDA VS RETORNO
   ------------------------------------------------------------------ */
$totais = [
    'subindo' => ['pagantes'=>0,'gratuitos'=>0,'moradores'=>0,'passageiros'=>0,'bonds'=>0],
    'retorno' => ['pagantes'=>0,'gratuitos'=>0,'moradores'=>0,'passageiros'=>0,'bonds'=>0]
];

foreach ($viagens as $v) {
    $key = $v['retorno'] === null ? 'subindo' : 'retorno';
    $totais[$key]['pagantes']    += $v['pagantes'];
    $totais[$key]['gratuitos']   += $v['gratuidade'];
    $totais[$key]['moradores']   += $v['moradores'];
    $totais[$key]['passageiros'] += $v['passageiros'];
    $totais[$key]['bonds']++;
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Transações - Bonde de Santa Teresa</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:20px;}
        .container{max-width:1000px;margin:auto;}
        .header{display:flex;justify-content:space-between;background:#f0f0f0;padding:10px;border-radius:5px;}
        .section{background:#fff;padding:15px;margin-top:10px;border-radius:5px;box-shadow:0 0 5px rgba(0,0,0,.1);}        
        .form-group{margin-bottom:10px;}
        label{display:inline-block;width:100px;font-weight:bold;}
        input,select{width:150px;padding:5px;margin-right:10px;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #ddd;padding:8px;text-align:left;color:#000;}
        th{background:#f2f2f2;}
        .buttons{margin-top:10px;}
        button{padding:10px 20px;margin-right:10px;border:none;border-radius:5px;cursor:pointer;}
        #adicionar{background:#ff4444;color:#fff;}
        #limpar{background:#ff8800;color:#fff;}
        #excluir{background:#ffbb33;color:#fff;}
        #alterar{background:#4444ff;color:#fff;}
        #limparTransacoes{background:#6666ff;color:#fff;}
        .progress-container{margin-bottom:20px;}
        .progress-bar{width:100%;background:#e0e0e0;border-radius:5px;overflow:hidden;height:20px;}
        .progress-fill{height:100%;background:#4caf50;width:0;transition:width .3s ease;text-align:center;line-height:20px;color:#fff;font-size:12px;}
        .message{padding:10px;margin-bottom:10px;border-radius:4px;text-align:center;}
        .success{background:#dff0d8;color:#3c763d;}
        .error{background:#f2dede;color:#a94442;}
        .no-data{color:#000;text-align:center;padding:10px;}

        /* PAGINAÇÃO */
        .pagination{margin-top:10px;text-align:center;}
        .pagination button{margin:0 2px;padding:6px 10px;border:1px solid #ccc;background:#fff;cursor:pointer;border-radius:4px;}
        .pagination .active{background:#4444ff;color:#fff;}
        .pagination .arrow{font-weight:bold;}

        /* MODAL DE EDIÇÃO */
        .modal{display:none;position:fixed;z-index:999;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.5);}
        .modal-content{background:#fff;margin:10% auto;padding:20px;border-radius:8px;max-width:400px;box-shadow:0 0 10px #0003;position:relative;}
        .modal-content .close{position:absolute;right:15px;top:10px;font-size:20px;font-weight:bold;cursor:pointer;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>CADASTRAMENTO DE TRANSAÇÕES</h2>
            <h2>TOTAL BONDES <img src="Bondes Santa Teresa Logo.png" alt="Logo" width="20"></h2>
        </div>

        <div class="message" style="display:none;"></div>

        <!-- ======================== FORMULÁRIO NOVA VIAGEM =================== -->
        <div class="section">
            <form id="viagemForm" method="POST" action="add_viagem.php">
                <div class="form-group">
                    <label>Bonde:</label>
                    <select name="bonde_id" id="bonde_id" onchange="handleBondeSelection()">
                        <option value="">Selecione</option>
                        <?php foreach($bondes as $b){echo "<option value='{$b['id']}'>".htmlspecialchars($b['modelo'])."</option>";} ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Saída:</label>
                    <select name="subindo_id" id="subindo_id" disabled>
                        <option value="">Selecione</option>
                        <?php foreach($destinos as $d){echo "<option value='{$d['id']}'>".htmlspecialchars($d['nome'])."</option>";} ?>
                    </select>
                    <label>Retorno:</label>
                    <select name="retorno_id" id="retorno_id" disabled>
                        <option value="">Selecione</option>
                        <?php foreach($destinos_retorno as $d){echo "<option value='{$d['id']}'>".htmlspecialchars($d['nome'])."</option>";} ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Maquinistas:</label>
                    <select name="maquinista_id" id="maquinista_id" disabled>
                        <option value="">Selecione</option>
                        <?php foreach($maquinistas as $m){echo "<option value='{$m['id']}'>".htmlspecialchars($m['nome'])."</option>";} ?>
                    </select>
                    <label>Agentes:</label>
                    <select name="agente_id" id="agente_id" disabled>
                        <option value="">Selecione</option>
                        <?php foreach($agentes as $a){echo "<option value='{$a['id']}'>".htmlspecialchars($a['nome'])."</option>";} ?>
                    </select>
                    <label>Hora:</label>
                    <input type="text" name="hora" id="hora" value="<?php echo date('H:i:s'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Pagantes:</label>
                    <input type="number" name="pagantes" id="pagantes" min="0" max="32" required disabled oninput="updateProgressBar()">
                    <label>Moradores:</label>
                    <input type="number" name="moradores" id="moradores" min="0" max="32" required disabled oninput="updateProgressBar()">
                    <label>Grat. Pcd/Idoso:</label>
                    <input type="number" name="gratuidade" id="gratuidade" min="0" max="32" required disabled oninput="updateProgressBar()">
                </div>
                <div class="form-group">
                    <label>Gratuidade:</label>
                    <input type="number" name="gratuidade_total" id="gratuidade_total" readonly>
                    <label>Passageiros:</label>
                    <input type="number" name="passageiros" id="passageiros" readonly>
                    <label>Viagem:</label>
                    <input type="number" name="viagem" id="viagem" value="1" readonly>
                </div>
                <div class="form-group">
                    <label>Data:</label>
                    <input type="text" name="data" id="data" value="<?php echo date('d/m/Y'); ?>" readonly>
                </div>

                <div class="progress-container" id="progressContainer" style="display:none;">
                    <label>Capacidade do Bonde (Máx.: 32 passageiros)</label>
                    <div class="progress-bar"><div class="progress-fill" id="progressFill">0%</div></div>
                </div>

                <div class="buttons">
                    <button type="submit"  id="adicionar"       disabled>Adicionar</button>
                    <button type="button" id="limpar">Limpar</button>
                    <button type="button" id="excluir">Excluir</button>
                    <button type="button" id="alterar">Alterar</button>
                    <button type="button" id="limparTransacoes">Limpar Transações</button>
                </div>
            </form>

            <!-- =========== TABELA DE VIAGENS =========== -->
            <div class="section">
                <h3>Registro de Viagens</h3>
                <?php if(empty($viagens)): ?>
                    <p class="no-data">Nenhum registro de viagem encontrado.</p>
                <?php else: ?>
                    <table id="viagens">
                        <thead>
                            <tr>
                                <th>ID-M</th>
                                <th>Bondes</th>
                                <th>subindo</th>
                                <th>Retorno</th>
                                <th>Maquinista</th>
                                <th>Agente</th>
                                <th>Hora</th>
                                <th>Pagantes</th>
                                <th>Gratuidade</th>
                                <th>Moradores</th>
                                <th>Passageiros</th>
                                <th>Viagem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($viagens as $v): ?>
                                <tr>
                                    <td><?= $v['id']; ?></td>
                                    <td><?= htmlspecialchars($v['bonde']); ?></td>
                                    <td><?= htmlspecialchars($v['subindo_id']); ?></td>
                                    <td><?= htmlspecialchars($v['retorno'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($v['maquinista']); ?></td>
                                    <td><?= htmlspecialchars($v['agente']); ?></td>
                                    <td><?= date('H:i:s',strtotime($v['hora'])); ?></td>
                                    <td><?= $v['pagantes']; ?></td>
                                    <td><?= $v['gratuidade']; ?></td>
                                    <td><?= $v['moradores']; ?></td>
                                    <td><?= $v['passageiros']; ?></td>
                                    <td><?= $v['viagem']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="pagination" class="pagination"></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- =========== TOTAIS =========== -->
        <div class="totals" style="display:flex;justify-content:space-between;flex-wrap:wrap;">
            <div class="section" style="width:48%;min-width:300px;">
                <h3>TOTAL BONDES SUBINDO</h3>
                <p>Pagantes:    <?= $totais['subindo']['pagantes']; ?></p>
                <p>Gratuitos:   <?= $totais['subindo']['gratuitos']; ?></p>
                <p>Moradores:   <?= $totais['subindo']['moradores']; ?></p>
                <p>Passageiros: <?= $totais['subindo']['passageiros']; ?></p>
                <p>Bondes Saída:<?= $totais['subindo']['bonds']; ?></p>
            </div>
            <div class="section" style="width:48%;min-width:300px;">
                <h3>TOTAL BONDES RETORNO</h3>
                <p>Pagantes:    <?= $totais['retorno']['pagantes']; ?></p>
                <p>Gratuitos:   <?= $totais['retorno']['gratuitos']; ?></p>
                <p>Moradores:   <?= $totais['retorno']['moradores']; ?></p>
                <p>Passageiros: <?= $totais['retorno']['passageiros']; ?></p>
                <p>Bondes Retorno: <?= $totais['retorno']['bonds']; ?></p>
            </div>
        </div>
    </div>

    <!-- =========== MODAL DE RETORNO =========== -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Registrar retorno para Estação Carioca</h3>
            <form id="retornoForm">
                <input type="hidden" name="viagem_id" id="modal_viagem_id">
                <div class="form-group">
                    <label>Pagantes:</label>
                    <input type="number" name="pagantes" min="0" max="32" required>
                </div>
                <div class="form-group">
                    <label>Moradores:</label>
                    <input type="number" name="moradores" min="0" max="32" required>
                </div>
                <div class="form-group">
                    <label>Gratuidade:</label>
                    <input type="number" name="gratuidade" min="0" max="32" required>
                </div>
                <button type="submit" style="background:#4444ff;color:#fff;border:none;border-radius:5px;padding:8px 20px;cursor:pointer">Salvar retorno</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    /* ===== BLOCO 1 – Utilitários de formulário ========================= */
    function handleBondeSelection(){
        const bondeId = $('#bonde_id').val();
        const campos  = ['#subindo_id','#retorno_id','#maquinista_id','#agente_id','#pagantes','#moradores','#gratuidade','#adicionar'];
        if(bondeId){
            $('#progressContainer').show();
            campos.forEach(c=>$(c).prop('disabled',false));
            updateProgressBar();
        }else{
            $('#progressContainer').hide();
            campos.forEach(c=>$(c).prop('disabled',true));
            $('#progressFill').css('width','0%').text('0%');
        }
    }

    function updateProgressBar(){
        const pagantes   = parseInt($('#pagantes').val()   || 0);
        const moradores  = parseInt($('#moradores').val()  || 0);
        const gratuidade = parseInt($('#gratuidade').val() || 0);
        const total = pagantes + moradores + gratuidade;
        const pct   = Math.min((total/32)*100,100);
        $('#progressFill').css('width', pct+'%').text(Math.round(pct)+'% ('+total+'/32)');
        $('#passageiros').val(total);
        $('#gratuidade_total').val(gratuidade);
    }

    /* ===== BLOCO 2 – Envio AJAX do formulário de nova viagem =========== */
    $(function(){
        $('#viagemForm').on('submit', function(e){
            e.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function(resp){
                resp = JSON.parse(resp);
                const msg = $('.message');
                if(resp.success){
                    msg.removeClass('error').addClass('success').text('Viagem cadastrada com sucesso!').show();
                    setTimeout(()=>location.reload(),700);
                }else{
                    msg.removeClass('success').addClass('error').text(resp.message).show();
                }
                setTimeout(()=>msg.hide(),4000);
            }).fail(()=>{
                $('.message').removeClass('success').addClass('error').text('Erro ao processar a requisição.').show();
                setTimeout(()=>$('.message').hide(),4000);
            });
        });

        $('#limpar').click(()=>{
            $('#viagemForm')[0].reset();
            $('.message').hide();
            updateProgressBar();
            handleBondeSelection();
        });

        /* ===== BLOCO 3 – Paginação da tabela =========================== */
        function paginateTable(){
            const rows    = $('#viagens tbody tr');
            if(!rows.length) return;
            const perPage = 3;
            const pages   = Math.ceil(rows.length/perPage);
            let current   = 1;

            function renderRows(){
                rows.hide().slice((current-1)*perPage, current*perPage).show();
            }
            function renderButtons(){
                const pag = $('#pagination').empty();
                const maxBtns = 5;
                let start = Math.max(1, current-Math.floor(maxBtns/2));
                let end   = Math.min(pages, start+maxBtns-1);
                if(end-start < maxBtns-1) start = Math.max(1, end-maxBtns+1);

                if(start>1) pag.append('<button class="arrow" data-p="'+(start-1)+'">&laquo;</button>');
                for(let p=start; p<=end; p++){
                    pag.append('<button data-p="'+p+'" class="'+(p===current?'active':'')+'">'+p+'</button>');
                }
                if(end<pages) pag.append('<button class="arrow" data-p="'+(end+1)+'">&raquo;</button>');
            }
            $(document).on('click','#pagination button',function(){
                current = +$(this).data('p');
                renderRows(); renderButtons();
            });
            renderRows(); renderButtons();
        }
        paginateTable();

        /* ===== BLOCO 4 – Relógio em tempo real ========================= */
        setInterval(()=>$('#hora').val(new Date().toLocaleTimeString('pt-BR',{hour12:false})),1000);

        /* ===== BLOCO 5 – Modal para registrar retorno ================= */
        $('#viagens tbody').on('click','tr',function(){
            $('#modal_viagem_id').val($(this).children().first().text());
            $('#editModal').show();
        });
        $('.close').click(()=>$('#editModal').hide());
        $(window).on('click',e=>{ if(e.target.id==='editModal') $('#editModal').hide(); });
        $('#retornoForm').on('submit',function(e){
            e.preventDefault();
            $.post('update_passageiros.php', $(this).serialize(), r=>{
                r = JSON.parse(r);
                if(r.success){
                    alert('Retorno registrado!');
                    location.reload();
                }else{
                    alert('Erro: '+r.message);
                }
            });
        });
    });
    </script>
</body>
</html>


<!-- ====================================================================
     SEGUNDO ARQUIVO NECESSÁRIO: update_passageiros.php
     Coloque este conteúdo em arquivo separado se ainda não existir.
     ==================================================================== -->

<?php /*
// update_passageiros.php
require 'conexao.php';          // ou re‑use o bloco de conexão acima
$id         = (int)$_POST['viagem_id'];
$pagantes   = (int)$_POST['pagantes'];
$moradores  = (int)$_POST['moradores'];
$gratuidade = (int)$_POST['gratuidade'];
$retornoId  = 4;                // id da Estação Carioca

try{
    $sql = "UPDATE viagens
               SET retorno_id = :retorno,
                   pagantes   = :pagantes,
                   moradores  = :moradores,
                   gratuidade = :gratuidade
             WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':retorno'   => $retornoId,
        ':pagantes'  => $pagantes,
        ':moradores' => $moradores,
        ':gratuidade'=> $gratuidade,
        ':id'        => $id
    ]);
    echo json_encode(['success'=>true]);
}catch(PDOException $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
*/?>
