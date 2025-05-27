// ========== CONTROLADOR SUAVE DO MODAL ========== //

class SmoothModalController {
  constructor() {
    this.modal = document.getElementById("perfilModal")
    this.isAnimating = false
    this.init()
  }

  init() {
    if (!this.modal) return

    // Event listeners para controle suave
    this.modal.addEventListener("show.bs.modal", (e) => this.handleShow(e))
    this.modal.addEventListener("hide.bs.modal", (e) => this.handleHide(e))
    this.modal.addEventListener("shown.bs.modal", () => this.handleShown())
    this.modal.addEventListener("hidden.bs.modal", () => this.handleHidden())

    // Interceptar cliques para animações suaves
    this.setupSmoothInteractions()
  }

  handleShow(event) {
    if (this.isAnimating) {
      event.preventDefault()
      return
    }

    this.isAnimating = true

    // Preparar modal para animação suave
    this.modal.style.display = "block"
    this.modal.classList.add("fade")

    // Forçar reflow para garantir animação suave
    this.modal.offsetHeight

    // Aplicar classe show com delay mínimo para suavidade
    requestAnimationFrame(() => {
      this.modal.classList.add("show")
      this.animateContentIn()
    })
  }

  handleHide(event) {
    if (this.isAnimating) {
      event.preventDefault()
      return
    }

    this.isAnimating = true
    this.animateContentOut()
  }

  handleShown() {
    this.isAnimating = false

    // Otimizar performance após animação
    const content = this.modal.querySelector(".modal-content")
    if (content) {
      content.style.willChange = "auto"
    }

    // Focar no primeiro elemento focável para acessibilidade
    this.focusFirstElement()
  }

  handleHidden() {
    this.isAnimating = false

    // Limpar estilos de animação
    this.modal.style.display = ""
    this.modal.classList.remove("fade", "show")
  }

  animateContentIn() {
    const elements = this.modal.querySelectorAll(".modal-header, .modal-body img, .modal-body p, .modal-footer")

    elements.forEach((element, index) => {
      element.style.opacity = "0"
      element.style.transform = "translateY(15px)"

      // Animação escalonada suave
      setTimeout(() => {
        element.style.transition = "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)"
        element.style.opacity = "1"
        element.style.transform = "translateY(0)"
      }, index * 50)
    })

    // Animar avatar separadamente para efeito especial
    this.animateAvatar()
  }

  animateContentOut() {
    const content = this.modal.querySelector(".modal-content")

    if (content) {
      content.style.transition = "all 0.25s ease-out"
      content.style.transform = "translateY(-10px) scale(0.98)"
      content.style.opacity = "0.8"
    }

    // Remover classes após animação
    setTimeout(() => {
      this.modal.classList.remove("show")

      setTimeout(() => {
        this.modal.style.display = "none"
        this.handleHidden()
      }, 250)
    }, 100)
  }

  animateAvatar() {
    const avatar = this.modal.querySelector(".modal-body img")

    if (avatar) {
      avatar.style.transform = "scale(0.8)"
      avatar.style.opacity = "0"

      setTimeout(() => {
        avatar.style.transition = "all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)"
        avatar.style.transform = "scale(1)"
        avatar.style.opacity = "1"
      }, 200)
    }
  }

  setupSmoothInteractions() {
    // Hover suave nos cards de informação
    const infoCards = this.modal.querySelectorAll(".modal-body p")

    infoCards.forEach((card) => {
      card.addEventListener("mouseenter", () => {
        card.style.transition = "all 0.2s ease-out"
        card.style.transform = "translateX(6px)"
      })

      card.addEventListener("mouseleave", () => {
        card.style.transform = "translateX(0)"
      })
    })

    // Botão fechar com rotação suave
    const closeBtn = this.modal.querySelector(".close")

    if (closeBtn) {
      closeBtn.addEventListener("mouseenter", () => {
        const span = closeBtn.querySelector("span")
        if (span) {
          span.style.transition = "transform 0.2s ease-out"
          span.style.transform = "rotate(90deg)"
        }
      })

      closeBtn.addEventListener("mouseleave", () => {
        const span = closeBtn.querySelector("span")
        if (span) {
          span.style.transform = "rotate(0deg)"
        }
      })
    }
  }

  focusFirstElement() {
    const focusableElements = this.modal.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
    )

    if (focusableElements.length > 0) {
      focusableElements[0].focus()
    }
  }

  // Método público para abrir modal suavemente
  show() {
    if (this.isAnimating) return

    // Usar Bootstrap modal se disponível, senão usar método customizado
    if (window.bootstrap && window.bootstrap.Modal) {
      const bsModal = new window.bootstrap.Modal(this.modal)
      bsModal.show()
    } else if (window.$ && window.$.fn.modal) {
      window.$(this.modal).modal("show")
    } else {
      this.handleShow({ preventDefault: () => {} })
    }
  }

  // Método público para fechar modal suavemente
  hide() {
    if (this.isAnimating) return

    if (window.bootstrap && window.bootstrap.Modal) {
      const bsModal = window.bootstrap.Modal.getInstance(this.modal)
      if (bsModal) bsModal.hide()
    } else if (window.$ && window.$.fn.modal) {
      window.$(this.modal).modal("hide")
    } else {
      this.handleHide({ preventDefault: () => {} })
    }
  }
}

// ========== INICIALIZAÇÃO AUTOMÁTICA ========== //

document.addEventListener("DOMContentLoaded", () => {
  // Aguardar um frame para garantir que o DOM esteja totalmente carregado
  requestAnimationFrame(() => {
    window.smoothModalController = new SmoothModalController()
  })
})

// ========== UTILITÁRIOS PARA PERFORMANCE ========== //

// Throttle para eventos de scroll/resize
function throttle(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Debounce para eventos de input
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// ========== MELHORIAS DE ACESSIBILIDADE ========== //

// Detectar preferência de movimento reduzido
const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)")

if (prefersReducedMotion.matches) {
  document.documentElement.style.setProperty("--transition-fast", "all 0.01ms")
  document.documentElement.style.setProperty("--transition-normal", "all 0.01ms")
  document.documentElement.style.setProperty("--transition-slow", "all 0.01ms")
}

// ========== OTIMIZAÇÕES DE PERFORMANCE ========== //

// Intersection Observer para animações apenas quando visível
const observerOptions = {
  threshold: 0.1,
  rootMargin: "50px",
}

const animationObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("animate-in")
    }
  })
}, observerOptions)

// Observar elementos que precisam de animação
document.addEventListener("DOMContentLoaded", () => {
  const animatableElements = document.querySelectorAll(".modal-body p, .modal-body img")
  animatableElements.forEach((el) => animationObserver.observe(el))
})
