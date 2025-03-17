// Definindo o mês e ano atual
let currentDate = new Date();
let currentMonth = currentDate.getMonth(); // Mês atual (0-11)
let currentYear = currentDate.getFullYear(); // Ano atual

// Lista de compromissos simulados (pode ser substituída por dados reais do banco)
const events = {
    "2025-03-01": [{ name: "Confirmação da Unificação", description: "Evento de unificação da equipe." }],
    "2025-03-02": [{ name: "Apresentação DRE", description: "Apresentação de demonstração de resultados." }],
    "2025-03-10": [{ name: "Audiencia", description: "Audiência importante para o caso." }],
    "2025-03-12": [{ name: "Cadastro processo", description: "Cadastro de novos processos no sistema." }],
};

// Função para gerar o calendário do mês
function generateCalendar(month, year) {
    // Definindo o mês e ano no título
    document.getElementById("month-year").textContent = `${new Date(year, month).toLocaleString('default', { month: 'long' })} ${year}`;

    // Calculando o número de dias no mês
    const firstDay = new Date(year, month, 1);
    const daysInMonth = new Date(year, month + 1, 0).getDate(); // Último dia do mês

    // Calculando o primeiro dia da semana do mês
    const firstDayOfWeek = firstDay.getDay(); // 0 - Domingo, 1 - Segunda...

    let calendarBody = document.getElementById("calendar-body");
    calendarBody.innerHTML = ""; // Limpar o corpo do calendário

    let day = 1;
    let rows = Math.ceil((daysInMonth + firstDayOfWeek) / 7); // Número de linhas necessárias

    for (let i = 0; i < rows; i++) {
        let row = document.createElement("tr");

        // Adicionar células de dias
        for (let j = 0; j < 7; j++) {
            let cell = document.createElement("td");

            // Verifica se o dia está dentro do mês
            if (i === 0 && j < firstDayOfWeek) {
                cell.innerHTML = "";
            } else if (day <= daysInMonth) {
                let dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                cell.innerHTML = `<span class='day-number'>${day}</span>`;

                // Verifica se há eventos para esse dia
                if (events[dateStr]) {
                    cell.innerHTML += `<div class='event'>${events[dateStr][0].name}</div>`;
                }

                // Adicionar classe de destaque se for o dia atual
                if (new Date(year, month, day).toDateString() === new Date().toDateString()) {
                    cell.classList.add('highlight');
                }

                day++;
            }

            row.appendChild(cell);
        }
        calendarBody.appendChild(row);
    }
}

// Função para exibir compromissos ao clicar no dia
document.getElementById("calendar-body").addEventListener("click", function(event) {
    if (event.target.classList.contains("day-number")) {
        let selectedDay = event.target.innerText;
        let selectedDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(selectedDay).padStart(2, '0')}`;

        let eventList = document.getElementById("event-list");
        eventList.innerHTML = ""; // Limpar a lista de eventos

        if (events[selectedDate]) {
            events[selectedDate].forEach(event => {
                let eventItem = document.createElement("div");
                eventItem.classList.add("event-item");
                eventItem.innerHTML = `<span>${event.name}</span><br><span>${event.description}</span>`;
                eventList.appendChild(eventItem);
            });
        } else {
            eventList.innerHTML = "<p>Nenhum evento para este dia.</p>";
        }
    }
});

// Função para navegar para o mês anterior
document.getElementById("prev-month").addEventListener("click", function() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
});

// Função para navegar para o próximo mês
document.getElementById("next-month").addEventListener("click", function() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
});

// Gerar o calendário do mês atual ao carregar a página
generateCalendar(currentMonth, currentYear);