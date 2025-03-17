// Configurações
const numCircles = 10; // Número de círculos
const colors = ['rgba(255, 255, 255, 0.2)', 'rgba(255, 255, 255, 0.5)', 'rgba(255, 255, 255, 0.7)'];
const safeZone = { top: 30, bottom: 70, left: 30, right: 70 }; // Zona segura para não criar círculos (em porcentagem da tela)

// Função para criar um círculo
function createCircle() {
    const circle = document.createElement('div');
    circle.classList.add('circle');

    // Define tamanho, posição, cor e animação aleatórios
    const size = Math.random() * 80 + 40; // Tamanho entre 40px e 120px
    const top = Math.random() * 100;
    const left = Math.random() * 100;

    // Evita que o círculo apareça dentro da "zona segura"
    if (top > safeZone.top && top < safeZone.bottom && left > safeZone.left && left < safeZone.right) {
        return; // Não cria o círculo se estiver dentro da zona segura
    }

    circle.style.width = `${size}px`;
    circle.style.height = `${size}px`;
    circle.style.background = colors[Math.floor(Math.random() * colors.length)];
    circle.style.top = `${top}vh`; // Posição aleatória, mas fora da zona segura
    circle.style.left = `${left}vw`; // Posição aleatória, mas fora da zona segura
    circle.style.animationDuration = `${Math.random() * 4 + 6}s`; // Entre 6s e 10s
    circle.style.animationDelay = `${Math.random() * 3}s`; // Entre 0s e 3s

    document.body.appendChild(circle);

    // Quando a animação do círculo terminar, cria um novo círculo
    circle.addEventListener('animationend', function () {
        document.body.removeChild(circle);  // Remove o círculo após a animação
        createCircle();  // Cria um novo círculo após a explosão
    });
}

// Cria múltiplos círculos
for (let i = 0; i < numCircles; i++) {
    createCircle();
}
