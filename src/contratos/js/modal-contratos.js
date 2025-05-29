// Melhorias para o modal de contratos
document.addEventListener("DOMContentLoaded", () => {
  // Função para formatar valores monetários com animação
  function animateValue(element, finalValue) {
    if (!element || !finalValue.includes("R$")) return

    const numericValue = Number.parseFloat(finalValue.replace(/[^\d,]/g, "").replace(",", "."))
    const duration = 1000 // 1 segundo
    const steps = 30
    const increment = numericValue / steps
    let current = 0
    let step = 0

    const timer = setInterval(() => {
      current += increment
      step++

      element.textContent = current.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      })

      if (step >= steps) {
        clearInterval(timer)
        element.textContent = finalValue // Valor final exato
      }
    }, duration / steps)
  }

  // Função para adicionar indicadores visuais baseados no conteúdo
  function addContentIndicators() {
    const modal = document.getElementById("modalContrato")
    if (!modal) return

    // Verificar campos vazios e adicionar classes
    const fields = [
      "modalTituloContrato",
      "modalDescricao",
      "modalValidade",
      "modalSEI",
      "modalGestor",
      "modalFiscais",
      "modalValorContrato",
      "modalNumParcelas",
      "modalValorAditivo",
    ]

    fields.forEach((fieldId) => {
      const element = document.getElementById(fieldId)
      if (element) {
        const content = element.textContent.trim()

        // Remover classes anteriores
        element.classList.remove("empty-field", "filled-field")

        // Adicionar classe baseada no conteúdo
        if (!content || content === "N/A" || content === "R$ 0,00") {
          element.classList.add("empty-field")
        } else {
          element.classList.add("filled-field")

          // Animar valores monetários
          if (fieldId.includes("Valor") && content.includes("R$")) {
            setTimeout(() => animateValue(element, content), 300)
          }
        }
      }
    })
  }

  // Função para validar e formatar SEI
  function formatSEI(seiElement) {
    if (!seiElement) return

    const sei = seiElement.textContent.trim()
    if (sei && sei !== "N/A") {
      // Adicionar formatação visual para SEI válido
      seiElement.style.position = "relative"

      // Adicionar ícone de verificação
      const checkIcon = document.createElement("span")
      checkIcon.innerHTML = " ✓"
      checkIcon.style.color = "var(--doc-blue)"
      checkIcon.style.fontSize = "0.7rem"
      checkIcon.style.marginLeft = "0.3rem"

      if (!seiElement.querySelector("span")) {
        seiElement.appendChild(checkIcon)
      }
    }
  }

  // Função para formatar data de validade com indicador de status
  function formatValidadeStatus(validadeElement) {
    if (!validadeElement) return

    const dataText = validadeElement.textContent.trim()
    if (dataText && dataText !== "N/A") {
      try {
        // Tentar parsear a data (assumindo formato brasileiro)
        const parts = dataText.split("/")
        if (parts.length === 3) {
          const dataValidade = new Date(parts[2], parts[1] - 1, parts[0])
          const hoje = new Date()
          const diffTime = dataValidade - hoje
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

          // Remover indicadores anteriores
          const existingIndicator = validadeElement.parentNode.querySelector(".status-indicator")
          if (existingIndicator) {
            existingIndicator.remove()
          }

          // Adicionar indicador de status
          const statusIndicator = document.createElement("span")
          statusIndicator.className = "status-indicator"
          statusIndicator.style.marginLeft = "0.5rem"
          statusIndicator.style.fontSize = "0.6rem"
          statusIndicator.style.padding = "0.1rem 0.3rem"
          statusIndicator.style.borderRadius = "10px"
          statusIndicator.style.fontWeight = "bold"

          if (diffDays < 0) {
            statusIndicator.textContent = "VENCIDO"
            statusIndicator.style.background = "#fee2e2"
            statusIndicator.style.color = "#dc2626"
          } else if (diffDays <= 30) {
            statusIndicator.textContent = "PRÓXIMO AO VENCIMENTO"
            statusIndicator.style.background = "#fef3c7"
            statusIndicator.style.color = "#d97706"
          } else {
            statusIndicator.textContent = "VIGENTE"
            statusIndicator.style.background = "#dcfce7"
            statusIndicator.style.color = "#16a34a"
          }

          validadeElement.parentNode.appendChild(statusIndicator)
        }
      } catch (error) {
        console.log("Erro ao processar data de validade:", error)
      }
    }
  }

  // Observer para detectar quando o modal é aberto
  const modalElement = document.getElementById("modalContrato")
  if (modalElement) {
    modalElement.addEventListener("shown.bs.modal", () => {
      // Aguardar um pouco para garantir que os dados foram carregados
      setTimeout(() => {
        addContentIndicators()
        formatSEI(document.getElementById("modalSEI"))
        formatValidadeStatus(document.getElementById("modalValidade"))
      }, 100)
    })
  }
})

// Função auxiliar para a função openModal existente
function enhanceModalData() {
  // Esta função pode ser chamada após preencher os dados do modal
  const event = new CustomEvent("modalDataLoaded")
  document.dispatchEvent(event)
}

// Listener para quando os dados do modal são carregados
document.addEventListener("modalDataLoaded", () => {
  setTimeout(() => {
    const modal = document.getElementById("modalContrato")
    if (modal && modal.classList.contains("show")) {
      window.addContentIndicators()
      window.formatSEI(document.getElementById("modalSEI"))
      window.formatValidadeStatus(document.getElementById("modalValidade"))
    }
  }, 100)
})
