<?php
// Configuração inicial
session_start();

// Classe para gerenciar dispositivos
class DeviceManager {
    private $status_cache = [];
    private $offline_times = [];

    public function __construct() {
        if (!isset($_SESSION['offline_times'])) {
            $_SESSION['offline_times'] = [];
        }
        $this->offline_times = &$_SESSION['offline_times'];
    }

    public function ping($ip) {
        if (isset($this->status_cache[$ip]) && time() - $this->status_cache[$ip]['timestamp'] < 60) {
            return $this->status_cache[$ip]['status'];
        }

        $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
            ? "ping -n 1 -w 500 $ip" 
            : "ping -c 1 -W 0.5 $ip";
        
        exec($command, $output, $status);
        $is_online = ($status === 0);

        $this->offline_times[$ip] = !$is_online ? ($this->offline_times[$ip] ?? 0) + 1 : 0;
        
        $this->status_cache[$ip] = [
            'status' => $is_online,
            'timestamp' => time()
        ];
        
        return $is_online;
    }

    public function checkPrinterStatus($ip) {
        if (!$this->ping($ip)) {
            return ["status" => "Com Falha", "toners" => []];
        }

        if (!function_exists('snmpget') || 
            (isset($this->status_cache[$ip]['printer']) && 
             time() - $this->status_cache[$ip]['printer']['timestamp'] < 60)) {
            return $this->status_cache[$ip]['printer'] ?? [
                "status" => "Disponível (SNMP não disponível)", 
                "toners" => []
            ];
        }

        $community = "public";
        $oids = [
            'device_status' => ".1.3.6.1.2.1.25.3.2.1.5.1",
            'printer_status' => ".1.3.6.1.2.1.25.3.5.1.1.1",
            'error_state' => ".1.3.6.1.2.1.25.3.5.1.2.1"
        ];

        $hrDeviceStatus = @snmpget($ip, $community, $oids['device_status']);
        $hrPrinterStatus = @snmpget($ip, $community, $oids['printer_status']);
        $hrPrinterDetectedErrorState = @snmpget($ip, $community, $oids['error_state']);

        $device_value = $hrDeviceStatus !== false ? (int)preg_replace('/[^0-9]/', '', $hrDeviceStatus) : 0;
        $printer_value = $hrPrinterStatus !== false ? (int)preg_replace('/[^0-9]/', '', $hrPrinterStatus) : 0;
        $error_hex = $hrPrinterDetectedErrorState !== false ? trim(preg_replace('/.*: /', '', $hrPrinterDetectedErrorState)) : '00';
        $error_hex = str_replace(' ', '', $error_hex);

        $has_error = !empty($error_hex) && $error_hex !== '00';
        $error_bits = str_split($error_hex, 2);
        if ($has_error && !empty($error_bits)) {
            $error_bits[0] = base_convert($error_bits[0], 16, 10);
            $has_error = ($error_bits[0] & 0x40) || ($error_bits[0] & 0x80);
        }

        $low_toners = [];
        if ($hrPrinterStatus !== false) {
            for ($i = 1; $i <= 4; $i++) {
                $level_oid = ".1.3.6.1.2.1.43.11.1.1.9.$i.1";
                $desc_oid = ".1.3.6.1.2.1.43.11.1.1.6.$i.1";
                $type_oid = ".1.3.6.1.2.1.43.11.1.1.1.$i.1";
                $color_oid = ".1.3.6.1.2.1.43.11.1.1.2.$i.1";

                $supply_level = @snmpget($ip, $community, $level_oid);
                if ($supply_level !== false) {
                    $toner_level = (int)preg_replace('/[^0-9]/', '', $supply_level);
                    if ($toner_level < 20 && $toner_level >= 0) {
                        $supply_desc = @snmpget($ip, $community, $desc_oid);
                        $desc = $supply_desc !== false ? trim(preg_replace('/.*: /', '', $supply_desc)) : "Toner $i";
                        $supply_type = @snmpget($ip, $community, $type_oid);
                        $type = $supply_type !== false ? (int)preg_replace('/[^0-9]/', '', $supply_type) : 0;
                        if ($type != 3) continue;
                        $supply_color = @snmpget($ip, $community, $color_oid);
                        $color = $supply_color !== false ? trim(preg_replace('/.*: /', '', $supply_color)) : "Desconhecido";
                        $low_toners[] = "$desc ($color): $toner_level%";
                    }
                }
            }
        }

        $status = match ($printer_value) {
            4 => "Imprimindo",
            5 => "Aquecendo",
            default => $has_error ? "Com Falha" : ($device_value == 3 ? "Aviso" : ($printer_value == 3 && $device_value == 2 ? "Em Repouso" : "Disponível"))
        };

        $result = ["status" => $status, "toners" => $low_toners];
        $this->status_cache[$ip]['printer'] = array_merge($result, ['timestamp' => time()]);
        return $result;
    }

    public function checkComputerStatus($ip) {
        if (isset($this->status_cache[$ip]['computer']) && 
            time() - $this->status_cache[$ip]['computer']['timestamp'] < 60) {
            return $this->status_cache[$ip]['computer']['status'];
        }

        $status = $this->ping($ip) ? "Online" : "Offline";
        $this->status_cache[$ip]['computer'] = ['status' => $status, 'timestamp' => time()];
        return $status;
    }

    public function getOfflineTime($ip) {
        return $this->offline_times[$ip] ?? 0;
    }
}

// Classe para gerenciar dados da rede
class NetworkMonitor {
    public static function getNetworkUsage() {
        return rand(0, 100);
    }
}

// Dados dos dispositivos
$printers = [
    ['local' => 'DIRPLA', 'patrimonio' => 'MLP4620005', 'modelo' => 'MFPE5J2645', 'ip' => '10.9.5.141'],
    ['local' => 'DIREO', 'patrimonio' => 'MLC4620002', 'modelo' => 'C7030', 'ip' => '10.9.5.135'],
    ['local' => 'DIRAF', 'patrimonio' => 'MLP4620002', 'modelo' => 'MPFE5J2645', 'ip' => '10.9.5.140'],
    ['local' => 'GERGEP', 'patrimonio' => 'MLP4620004', 'modelo' => 'MPFE5J2645', 'ip' => '10.9.5.34'],
    ['local' => 'PRES', 'patrimonio' => 'MLC4620001', 'modelo' => 'C7030', 'ip' => '10.9.5.142'],
    ['local' => 'BONDE', 'patrimonio' => 'MLP4620001', 'modelo' => 'MPFE5J2645', 'ip' => '10.9.28.186']
];

$computers = [
    ['nome' => 'GETUWAY GERAL', 'ip' => '10.9.5.1'],
    ['nome' => 'BANCO DE DADOS TESTE', 'ip' => '10.9.5.48'],
    ['nome' => 'BANCO DE DADOS SISTEMA', 'ip' => '10.9.5.49'],
    ['nome' => 'COMPUTADOR', 'ip' => '10.9.5.50'],
    ['nome' => 'COMPUTADOR CCO', 'ip' => '10.9.28.140'],
    ['nome' => 'SERVIDOR', 'ip' => '10.11.92.131'],
    ['nome' => 'SERVIDOR', 'ip' => '10.11.92.132'],
    ['nome' => 'SERVIDOR', 'ip' => '10.11.92.6'],
    ['nome' => 'SERVIDOR', 'ip' => '10.11.92.4']
];

$deviceManager = new DeviceManager();

if (isset($_GET['get_bandwidth'])) {
    header('Content-Type: application/json');
    echo json_encode(['usage' => NetworkMonitor::getNetworkUsage()]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento TIC - Monitoramento</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background-color: #1e1e1e; color: #fff; }
        h1, h2 { text-align: center; color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 8px; text-align: left; }
        th { background-color: #333; font-weight: bold; }
        .online { color: green; font-weight: bold; }
        .offline { color: red; font-weight: bold; }
        .printing { color: #FFA500; font-weight: bold; }
        .available { color: green; font-weight: bold; }
        .failed { color: red; font-weight: bold; }
        .warning { color: #FF8C00; font-weight: bold; }
        .warming { color: #FFA500; font-weight: bold; }
        .resting { color: #4682B4; font-weight: bold; }
        .toner-low { color: #FF4500; font-size: 0.9em; }
        .gauge-container { display: flex; justify-content: space-around; margin-top: 20px; }
        .gauge { width: 300px; height: 200px; }
    </style>
</head>
<body>
    <h1>Monitoramento - TIC</h1>

    <h2>Monitoramento de Rede</h2>
    <div class="gauge-container">
        <div>
            <h3>Velocidade da Internet (Mbps)</h3>
            <canvas id="speedGauge" class="gauge"></canvas>
        </div>
        <div>
            <h3>Utilização da Banda (%)</h3>
            <canvas id="bandwidthGauge" class="gauge"></canvas>
        </div>
    </div>

    <h2>Monitoramento de Impressoras</h2>
    <table>
        <tr><th>Local</th><th>Patrimônio</th><th>Modelo</th><th>IP</th><th>Status</th><th>Tempo Offline (min)</th><th>Status Específico</th></tr>
        <?php foreach ($printers as $printer): 
            $ip = $printer['ip'];
            $is_online = $deviceManager->ping($ip);
            $printer_info = $deviceManager->checkPrinterStatus($ip);
            $status = $printer_info['status'];
            $toners_low = $printer_info['toners'];
            $status_class = $is_online ? 'online' : 'offline';
            $printer_class = strtolower(str_replace(' ', '', $status));
        ?>
            <tr>
                <td><?= htmlspecialchars($printer['local']) ?></td>
                <td><?= htmlspecialchars($printer['patrimonio']) ?></td>
                <td><?= htmlspecialchars($printer['modelo']) ?></td>
                <td><?= htmlspecialchars($ip) ?></td>
                <td class="<?= $status_class ?>"><?= $is_online ? 'Online' : 'Offline' ?></td>
                <td><?= $deviceManager->getOfflineTime($ip) ?> min</td>
                <td class="<?= $printer_class ?>">
                    <?php if ($is_online): ?>
                        <strong><?= htmlspecialchars($status) ?></strong>
                        <?php if (!empty($toners_low)): ?>
                            <br><span class="toner-low">Toners Baixos: <?= implode(', ', array_map('htmlspecialchars', $toners_low)) ?></span>
                        <?php endif; ?>
                    <?php else: ?>-<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Monitoramento de Computadores e Servidores</h2>
    <table>
        <tr><th>Nome</th><th>IP</th><th>Status</th><th>Tempo Offline (min)</th></tr>
        <?php foreach ($computers as $computer): 
            $ip = $computer['ip'];
            $status = $deviceManager->checkComputerStatus($ip);
            $status_class = $status === 'Online' ? 'online' : 'offline';
        ?>
            <tr>
                <td><?= htmlspecialchars($computer['nome']) ?></td>
                <td><?= htmlspecialchars($ip) ?></td>
                <td class="<?= $status_class ?>"><?= $status ?></td>
                <td><?= $deviceManager->getOfflineTime($ip) ?> min</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        // Função para criar velocímetro com ponteiro
        function createSpeedGauge(canvasId, value, maxValue, label) {
            return new Chart(document.getElementById(canvasId).getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [value, maxValue - value],
                        backgroundColor: ['rgba(0, 191, 255, 0.7)', 'rgba(0, 0, 0, 0)'],
                        borderWidth: 0,
                        needleValue: value,
                        needleColor: 'rgba(255, 0, 0, 0.9)'
                    }]
                },
                options: {
                    circumference: 180,
                    rotation: -90,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        beforeDraw: function(chart) {
                            var ctx = chart.ctx;
                            var width = chart.width;
                            var height = chart.height;
                            var centerX = width / 2;
                            var centerY = height / 2;
                            var radius = Math.min(width, height) / 2.5;

                            // Desenhar marcações
                            ctx.save();
                            ctx.translate(centerX, centerY);
                            ctx.rotate(-Math.PI / 2);
                            for (var i = 0; i <= maxValue; i += maxValue / 5) {
                                var angle = Math.PI * (i / maxValue);
                                ctx.beginPath();
                                ctx.moveTo(radius * 0.9 * Math.cos(angle), radius * 0.9 * Math.sin(angle));
                                ctx.lineTo(radius * 0.95 * Math.cos(angle), radius * 0.95 * Math.sin(angle));
                                ctx.strokeStyle = '#fff';
                                ctx.lineWidth = 2;
                                ctx.stroke();

                                ctx.textAlign = 'center';
                                ctx.fillStyle = '#fff';
                                ctx.font = '12px Arial';
                                ctx.fillText(i, radius * 0.75 * Math.cos(angle), radius * 0.75 * Math.sin(angle) + 5);
                            }
                            ctx.restore();

                            // Desenhar ponteiro
                            var angle = Math.PI * (value / maxValue);
                            ctx.save();
                            ctx.translate(centerX, centerY);
                            ctx.rotate(angle - Math.PI / 2);
                            ctx.beginPath();
                            ctx.moveTo(0, 10);
                            ctx.lineTo(-8, radius * 0.7);
                            ctx.lineTo(8, radius * 0.7);
                            ctx.fillStyle = chart.data.datasets[0].needleColor;
                            ctx.fill();
                            ctx.beginPath();
                            ctx.arc(0, 0, 10, 0, Math.PI * 2);
                            ctx.fillStyle = 'rgba(255, 215, 0, 0.9)';
                            ctx.fill();
                            ctx.restore();
                        }
                    },
                    title: {
                        display: true,
                        text: `${label}: ${value} Mbps`,
                        position: 'bottom',
                        color: '#fff',
                        font: { size: 14 }
                    }
                }
            });
        }

        // Função para criar gauge de utilização da banda
        function createBandwidthGauge(canvasId, value, maxValue, label) {
            return new Chart(document.getElementById(canvasId).getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: [label, 'Restante'],
                    datasets: [{
                        data: [value, maxValue - value],
                        backgroundColor: ['#FF6384', '#E0E0E0'],
                        borderColor: ['#FF6384', '#E0E0E0'],
                        borderWidth: 1
                    }]
                },
                options: {
                    circumference: 180,
                    rotation: -90,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        title: {
                            display: true,
                            text: `${label}: ${value}%`,
                            position: 'bottom',
                            color: '#fff',
                            font: { size: 14 }
                        }
                    }
                }
            });
        }

        // Função para testar a velocidade da internet
        async function testInternetSpeed() {
            const testFile = '/speedtest/100MB.bin';
            const startTime = performance.now();
            try {
                const response = await fetch(testFile, { cache: 'no-store' });
                const data = await response.blob();
                const endTime = performance.now();
                const duration = (endTime - startTime) / 1000;
                const fileSize = 100 * 8;
                const speedMbps = Math.min((fileSize / duration).toFixed(2), 100);
                createSpeedGauge('speedGauge', speedMbps, 100, 'Velocidade');
            } catch (error) {
                console.error('Erro ao testar velocidade:', error);
                createSpeedGauge('speedGauge', 0, 100, 'Erro ao medir velocidade');
            }
        }

        // Função para atualizar a utilização da banda
        async function updateBandwidthGauge() {
            try {
                const response = await fetch('?get_bandwidth=1');
                const data = await response.json();
                createBandwidthGauge('bandwidthGauge', data.usage, 100, 'Utilização');
            } catch (error) {
                console.error('Erro ao obter uso da banda:', error);
                createBandwidthGauge('bandwidthGauge', 0, 100, 'Erro ao medir uso');
            }
        }

        // Atualizar dados a cada 30 segundos
        async function refreshData() {
            await Promise.all([testInternetSpeed(), updateBandwidthGauge()]);
            setTimeout(() => location.reload(), 30000);
        }

        window.onload = refreshData;
    </script>
</body>
</html>