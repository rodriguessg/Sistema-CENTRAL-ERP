<?php
// Função para carregar dados do banco de dados
function carregarDados($pdo) {
    // Query planejamento table
    $stmt = $pdo->query("SELECT id, pe_code, titulo_oportunidade, setor, valor_estimado, prazo, status, descricao, project_plan, created_at FROM planejamento");
    $planejamento = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query macroetapas table
    $stmt = $pdo->query("SELECT id, planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao FROM macroetapas");
    $macroetapas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize macroetapas by planejamento_id
    $macro_by_id = [];
    foreach ($macroetapas as $me) {
        $pid = $me['planejamento_id'];
        if (!isset($macro_by_id[$pid])) {
            $macro_by_id[$pid] = [];
        }
        $macro_by_id[$pid][] = $me;
    }

    return ['planejamento' => $planejamento, 'macroetapas' => $macroetapas, 'macro_by_id' => $macro_by_id];
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gm_sicbd;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

$datos = carregarDados($pdo);
$planejamento = $datos['planejamento'];
$macroetapas = $datos['macroetapas'];
$macro_by_id = $datos['macro_by_id'];

// Calcular progresso por setor
$setores = array_unique(array_column($planejamento, 'setor'));
$setorProgress = [];
foreach ($setores as $setor) {
    $setorOportunidades = array_filter($planejamento, function($p) use ($setor) { return $p['setor'] === $setor; });
    $totalEtapasSetor = 0;
    $completedEtapasSetor = 0;
    foreach ($setorOportunidades as $op) {
        $etapas = $macro_by_id[$op['id']] ?? [];
        $totalEtapasSetor += count($etapas);
        $completedEtapasSetor += count(array_filter($etapas, function($me) { return $me['etapa_concluida'] === 'sim'; }));
    }
    $setorProgress[$setor] = $totalEtapasSetor > 0 ? ($completedEtapasSetor / $totalEtapasSetor) * 100 : 0;
}

// Calcular médias e medianas por setor
$setorStats = [];
foreach ($setores as $setor) {
    $oportunidadesSetor = array_filter($planejamento, function($p) use ($setor) { return $p['setor'] === $setor; });
    $progressos = [];
    foreach ($oportunidadesSetor as $op) {
        $etapas = $macro_by_id[$op['id']] ?? [];
        $total = count($etapas);
        $completed = count(array_filter($etapas, function($me) { return $me['etapa_concluida'] === 'sim'; }));
        $progressos[] = $total > 0 ? ($completed / $total) * 100 : 0;
    }
    sort($progressos);
    $count = count($progressos);
    $media = $count > 0 ? array_sum($progressos) / $count : 0;
    $mediana = $count > 0 ? ($count % 2 == 0 ? ($progressos[$count/2 - 1] + $progressos[$count/2]) / 2 : $progressos[($count-1)/2]) : 0;
    $setorStats[$setor] = ['media' => $media, 'mediana' => $mediana];
}

$mediaGlobal = count($setores) > 0 ? array_sum(array_map(function($s) { return $s['media']; }, $setorStats)) / count($setores) : 0;

$faseMap = [
    "PLANEJAMENTO PCA" => "Planejamento PCA",
    "FASE PREPARATÓRIA" => "Fase Preparatória",
    "FASE EXTERNA" => "Fase Externa",
    "FASE DE CONTRATAÇÃO" => "Fase Contratação"
    // Adicione mais mapeamentos conforme necessário baseados nos dados reais
];
$phases = array_values($faseMap);

// Para cada opportunity, calcular progress por phase baseado em nome_macroetapa e project_plan
include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Atividades - PN 2024/2027</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .progress-bar { height: 15px; background-color: #d1d5db; border-radius: 5px; overflow: hidden; }
        .progress-bar-fill { height: 100%; background-color: #047857; transition: width 0.3s ease; }
        .details-section { display: none; }
        .details-section.open { display: table-row-group; }
        .toggle-details { cursor: pointer; }
        thead th {
            position: sticky;
            top: 0;
            background-color: #065f46;
            color: white;
            z-index: 1;
        }
        tbody tr:hover { background-color: #f3f4f6; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-7xl bg-white shadow-2xl rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Relatório de Atividades - PN 2024/2027</h1>
        <button id="gerarRelatorio" class="mb-6 bg-green-700 text-white px-5 py-2 rounded-md hover:bg-green-800 transition duration-200">Atualizar Relatório</button>
        <table id="tabelaRelatorio" class="w-full border-collapse text-sm">
            <thead>
                <tr>
                    <th class="border p-2 text-left font-semibold">PE_CODE</th>
                    <th class="border p-2 text-left font-semibold">Realizado Tema % (Média)</th>
                    <th class="border p-2 text-left font-semibold">Realizado Tema % (Médiana)</th>
                    <th class="border p-2 text-left font-semibold">Realizado Ação % (Média)</th>
                    <th class="border p-2 text-left font-semibold">Realizado Ação % (Médiana)</th>
                    <th class="border p-2 text-left font-semibold">ATIVIDADE</th>
                    <th class="border p-2 text-left font-semibold">Tipo de atividade</th>
                    <th class="border p-2 text-left font-semibold">SEI</th>
                    <th class="border p-2 text-left font-semibold">Planejamento PCA</th>
                    <th class="border p-2 text-left font-semibold">Fase Preparatória</th>
                    <th class="border p-2 text-left font-semibold">Fase Externa</th>
                    <th class="border p-2 text-left font-semibold">Fase Contratação</th>
                    <th class="border p-2 text-left font-semibold">% REALIZADO MACRO - Atividade</th>
                </tr>
            </thead>
            <tbody id="relatorioBody">
                <?php foreach ($planejamento as $oportunidade): ?>
                    <?php
                    $pid = $oportunidade['id'];
                    $etapas = $macro_by_id[$pid] ?? [];
                    $totalEtapas = count($etapas);
                    $completedEtapas = count(array_filter($etapas, function($me) { return $me['etapa_concluida'] === 'sim'; }));
                    $progressAcao = $totalEtapas > 0 ? ($completedEtapas / $totalEtapas) * 100 : 0;
                    $setor = $oportunidade['setor'];

                    // Parse project_plan for total stages per phase
                    $project_plan = json_decode($oportunidade['project_plan'], true) ?? [];
                    $faseTotal = array_fill_keys($phases, 0);
                    foreach ($project_plan as $macro) {
                        $macro_name = $macro['name'] ?? $macro['nome_macroetapa'] ?? 'N/A';
                        $fase = $faseMap[$macro_name] ?? 'N/A';
                        if (in_array($fase, $phases)) {
                            $faseTotal[$fase] += count($macro['etapas'] ?? []);
                        }
                    }

                    // Completed from macroetapas, mapped to phases
                    $faseProgress = array_fill_keys($phases, 0);
                    foreach ($etapas as $me) {
                        $macro_name = $me['nome_macroetapa'];
                        $fase = $faseMap[$macro_name] ?? 'N/A';
                        if (in_array($fase, $phases) && $me['etapa_concluida'] === 'sim') {
                            $faseProgress[$fase]++;
                        }
                    }

                    // Calculate percentages
                    $fasePercent = [];
                    foreach ($phases as $phase) {
                        $t = $faseTotal[$phase];
                        $c = $faseProgress[$phase];
                        $fasePercent[$phase] = $t > 0 ? ($c / $t) * 100 : 0;
                    }

                    // Media and Mediana for tema (setor) and ação (opportunity)
                    $mediaTema = $setorStats[$setor]['media'] ?? 0;
                    $medianaTema = $setorStats[$setor]['mediana'] ?? 0;
                    $mediaAcao = $progressAcao; // Since single value for action
                    $medianaAcao = $progressAcao; // Same

                    // Tipo de atividade (simulated based on title)
                    $tipoAtividade = strpos($oportunidade['titulo_oportunidade'], 'Manutenção') !== false ? 'Manutenção' : 'Modernização';

                    // SEI (simulated)
                    $sei = 'SEI-' . str_pad($oportunidade['id'], 5, '0', STR_PAD_LEFT);
                    ?>
                    <tr class="toggle-details">
                        <td class="border p-2"><?php echo htmlspecialchars($oportunidade['pe_code'] ?? 'N/A'); ?></td>
                        <td class="border p-2"><?php echo number_format($mediaTema, 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($medianaTema, 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($mediaAcao, 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($medianaAcao, 1); ?>%</td>
                        <td class="border p-2"><?php echo htmlspecialchars($oportunidade['titulo_oportunidade']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($tipoAtividade); ?></td>
                        <td class="border p-2"><?php echo $sei; ?></td>
                        <td class="border p-2"><?php echo number_format($fasePercent['Planejamento PCA'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($fasePercent['Fase Preparatória'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($fasePercent['Fase Externa'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($fasePercent['Fase Contratação'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($progressAcao, 1); ?>%</td>
                    </tr>
                    <tr class="details-section details-<?php echo $oportunidade['id']; ?>">
                        <td colspan="13" class="border p-2 bg-gray-50">
                            <h4 class="font-semibold text-gray-700 mb-1">Etapas:</h4>
                            <ul class="list-disc pl-5 text-sm">
                                <?php foreach ($etapas as $me): ?>
                                    <li class="py-0.5">
                                        <span class="<?php echo $me['etapa_concluida'] === 'sim' ? 'line-through text-green-700' : 'text-gray-600'; ?>"><?php echo htmlspecialchars($me['etapa_nome']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.toggle-details').forEach(row => {
            row.addEventListener('click', () => {
                const details = document.querySelector(`.details-${row.cells[7].textContent.replace('SEI-', '')}`);
                details.classList.toggle('open');
                details.style.display = details.classList.contains('open') ? 'table-row-group' : 'none';
            });
        });

        document.getElementById('gerarRelatorio').addEventListener('click', () => location.reload());
    </script>
</body>
</html>
<?php
$pdo = null;
include 'footer.php';
?> ```