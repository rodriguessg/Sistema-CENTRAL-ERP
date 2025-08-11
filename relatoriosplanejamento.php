<?php
// Função para carregar dados (simulada, substitua por consulta ao banco)
function carregarDados() {
    return [
        'planejamento' => [
            ['id' => 1, 'titulo_oportunidade' => 'Reestruturação da Via Permanente', 'setor' => 'Bondes de Santa Teresa', 'status' => 'planejamento', 'descricao' => 'Manutenção e conservação da via permanente e rede aérea.', 'created_at' => '2024-01-01'],
            ['id' => 2, 'titulo_oportunidade' => 'Melhorias Operacionais 2024-2025', 'setor' => 'Bondes de Santa Teresa', 'status' => 'andamento', 'descricao' => 'Reforma de bondes e extensões para Paula Mattos e Silvestre.', 'created_at' => '2024-02-01'],
            ['id' => 3, 'titulo_oportunidade' => 'Modernização Saracuruna/Guapimirim', 'setor' => 'Ferrovia', 'status' => 'planejamento', 'descricao' => 'Modernização da ligação via VLT.', 'created_at' => '2024-03-01']
        ],
        'macroetapas' => [
            ['id' => 1, 'planejamento_id' => 1, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Via Permanente', 'etapa_nome' => 'Manutenção da via permanente', 'etapa_concluida' => 'nao'],
            ['id' => 2, 'planejamento_id' => 1, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Via Permanente', 'etapa_nome' => 'Conservação dos trilhos', 'etapa_concluida' => 'nao'],
            ['id' => 3, 'planejamento_id' => 1, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Via Permanente', 'etapa_nome' => 'Manutenção da rede aérea', 'etapa_concluida' => 'nao'],
            ['id' => 4, 'planejamento_id' => 2, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Melhorias 2024', 'etapa_nome' => 'Reforma dos bondes', 'etapa_concluida' => 'sim'],
            ['id' => 5, 'planejamento_id' => 2, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Melhorias 2024', 'etapa_nome' => 'Reforma da estação Carioca', 'etapa_concluida' => 'nao'],
            ['id' => 6, 'planejamento_id' => 2, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Extensões 2025', 'etapa_nome' => 'Extensão para Paula Mattos', 'etapa_concluida' => 'nao'],
            ['id' => 7, 'planejamento_id' => 2, 'setor' => 'Bondes de Santa Teresa', 'nome_macroetapa' => 'Extensões 2025', 'etapa_nome' => 'Extensão para Silvestre', 'etapa_concluida' => 'nao'],
            ['id' => 8, 'planejamento_id' => 3, 'setor' => 'Ferrovia', 'nome_macroetapa' => 'Modernização', 'etapa_nome' => 'Contratação de empresa especializada', 'etapa_concluida' => 'nao'],
            ['id' => 9, 'planejamento_id' => 3, 'setor' => 'Ferrovia', 'nome_macroetapa' => 'Modernização', 'etapa_nome' => 'Apoio técnico da CENTRAL/RJ', 'etapa_concluida' => 'nao']
        ]
    ];
}

$datos = carregarDados();
$planejamento = $datos['planejamento'];
$macroetapas = $datos['macroetapas'];

// Calcular progresso por setor
$setores = array_unique(array_column($planejamento, 'setor'));
$setorProgress = [];
foreach ($setores as $setor) {
    $setorOportunidades = array_filter($planejamento, fn($p) => $p['setor'] === $setor);
    $totalEtapasSetor = count(array_filter($macroetapas, fn($me) => in_array($me['planejamento_id'], array_column($setorOportunidades, 'id'))));
    $completedEtapasSetor = count(array_filter($macroetapas, fn($me) => $me['etapa_concluida'] === 'sim' && in_array($me['planejamento_id'], array_column($setorOportunidades, 'id'))));
    $setorProgress[$setor] = $totalEtapasSetor > 0 ? ($completedEtapasSetor / $totalEtapasSetor) * 100 : 0;
}

// Calcular médias e medianas por setor
$setorStats = [];
foreach ($setores as $setor) {
    $oportunidadesSetor = array_filter($planejamento, fn($p) => $p['setor'] === $setor);
    $progressos = [];
    foreach ($oportunidadesSetor as $op) {
        $etapas = array_filter($macroetapas, fn($me) => $me['planejamento_id'] == $op['id']);
        $total = count($etapas);
        $completed = count(array_filter($etapas, fn($me) => $me['etapa_concluida'] === 'sim'));
        $progressos[] = $total > 0 ? ($completed / $total) * 100 : 0;
    }
    sort($progressos);
    $count = count($progressos);
    $media = $count > 0 ? array_sum($progressos) / $count : 0;
    $mediana = $count > 0 ? ($count % 2 == 0 ? ($progressos[$count/2 - 1] + $progressos[$count/2]) / 2 : $progressos[($count-1)/2]) : 0;
    $setorStats[$setor] = ['media' => $media, 'mediana' => $mediana];
}

$mediaGlobal = count($setores) > 0 ? array_sum(array_column($setorStats, 'media')) / count($setores) : 0;

$faseMap = [
    "Via Permanente" => "Planejamento PCA",
    "Melhorias 2024" => "Planejamento PCA",
    "Extensões 2025" => "Fase Preparatória",
    "Modernização" => "Fase Externa",
    "Contratação" => "Fase Contratação"
];
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
                    <th class="border p-2 text-left font-semibold">Referência</th>
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
                <?php foreach ($planejamento as $index => $oportunidade): ?>
                    <?php
                    $etapasRelacionadas = array_filter($macroetapas, fn($me) => $me['planejamento_id'] == $oportunidade['id']);
                    $totalEtapas = count($etapasRelacionadas);
                    $completedEtapas = count(array_filter($etapasRelacionadas, fn($me) => $me['etapa_concluida'] === 'sim'));
                    $progressAcao = $totalEtapas > 0 ? ($completedEtapas / $totalEtapas) * 100 : 0;
                    $progressPE = $setorProgress[$oportunidade['setor']] ?? 0;

                    // Calcular médias e medianas por ação no setor
                    $acoesProgress = [];
                    foreach (array_filter($planejamento, fn($p) => $p['setor'] === $oportunidade['setor']) as $acao) {
                        $etapasAcao = array_filter($macroetapas, fn($me) => $me['planejamento_id'] == $acao['id']);
                        $totalAcao = count($etapasAcao);
                        $completedAcao = count(array_filter($etapasAcao, fn($me) => $me['etapa_concluida'] === 'sim'));
                        $acoesProgress[] = $totalAcao > 0 ? ($completedAcao / $totalAcao) * 100 : 0;
                    }
                    sort($acoesProgress);
                    $countAcoes = count($acoesProgress);
                    $mediaAcao = $countAcoes > 0 ? array_sum($acoesProgress) / $countAcoes : 0;
                    $medianaAcao = $countAcoes > 0 ? ($countAcoes % 2 == 0 ? ($acoesProgress[$countAcoes/2 - 1] + $acoesProgress[$countAcoes/2]) / 2 : $acoesProgress[($countAcoes-1)/2]) : 0;

                    // Percentuais por fase
                    $faseProgress = ['Planejamento PCA' => 0, 'Fase Preparatória' => 0, 'Fase Externa' => 0, 'Fase Contratação' => 0];
                    $totalFases = 0;
                    foreach ($etapasRelacionadas as $me) {
                        $fase = $faseMap[$me['nome_macroetapa']] ?? 'N/A';
                        if (array_key_exists($fase, $faseProgress)) {
                            $faseProgress[$fase]++;
                            $totalFases++;
                        }
                    }
                    foreach ($faseProgress as $fase => &$percent) {
                        $percent = $totalFases > 0 ? ($percent / $totalFases) * 100 : 0;
                    }
                    unset($percent);

                    // Tipo de atividade (simulado)
                    $tipoAtividade = strpos($oportunidade['titulo_oportunidade'], 'Manutenção') !== false ? 'Manutenção' : 'Modernização';

                    // Gerar referência
                    $referencia = "PE " . $oportunidade['id'];
                    if ($totalEtapas > 0) {
                        $referencia .= "." . $totalEtapas . "." . ($index + 1);
                    }
                    ?>
                    <tr class="toggle-details">
                        <td class="border p-2"><?php echo $referencia; ?></td>
                        <td class="border p-2"><?php echo number_format($setorStats[$oportunidade['setor']]['media'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($setorStats[$oportunidade['setor']]['mediana'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($mediaAcao, 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($medianaAcao, 1); ?>%</td>
                        <td class="border p-2"><?php echo $oportunidade['titulo_oportunidade']; ?></td>
                        <td class="border p-2"><?php echo $tipoAtividade; ?></td>
                        <td class="border p-2">SEI-<?php echo str_pad($oportunidade['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="border p-2"><?php echo number_format($faseProgress['Planejamento PCA'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($faseProgress['Fase Preparatória'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($faseProgress['Fase Externa'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($faseProgress['Fase Contratação'], 1); ?>%</td>
                        <td class="border p-2"><?php echo number_format($progressAcao, 1); ?>%</td>
                    </tr>
                    <tr class="details-section details-<?php echo $oportunidade['id']; ?>">
                        <td colspan="13" class="border p-2 bg-gray-50">
                            <h4 class="font-semibold text-gray-700 mb-1">Etapas:</h4>
                            <ul class="list-disc pl-5 text-sm">
                                <?php foreach ($etapasRelacionadas as $me): ?>
                                    <li class="py-0.5">
                                        <span class="<?php echo $me['etapa_concluida'] === 'sim' ? 'line-through text-green-700' : 'text-gray-600'; ?>"><?php echo $me['etapa_nome']; ?></span>
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