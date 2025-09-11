<?php
if (function_exists('snmpget')) {
    // Descrição do suprimento
    $supply_desc = snmpget('10.9.5.142', 'public', '.1.3.6.1.2.1.43.11.1.1.6.1.1'); // prtMarkerSuppliesDescription
    echo "Descrição do Suprimento: " . $supply_desc . "\n";
    
    // Tipo de suprimento (3 = toner)
    $supply_type = snmpget('10.9.5.142', 'public', '.1.3.6.1.2.1.43.11.1.1.1.1.1'); // prtMarkerSuppliesType
    echo "Tipo de Suprimento: " . $supply_type . "\n";
    
    // Estado do suprimento (1 = ok, 3 = vazio, 4 = baixo)
    $supply_state = snmpget('10.9.5.142', 'public', '.1.3.6.1.2.1.43.11.1.1.8.1.1'); // prtMarkerSuppliesState
    echo "Estado do Suprimento: " . $supply_state . "\n";
} else {
    echo "SNMP não disponível";
}
?>