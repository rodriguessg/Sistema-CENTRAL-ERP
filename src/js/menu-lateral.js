$(document).ready(function () {
  // Alternar entre o menu minimizado e maximizado
  $("#sidebarToggle").click(function () {
    $("#sidebar").toggleClass("minimized");
    $("#content").toggleClass("sidebar-minimized");

    const isMinimized = $("#sidebar").hasClass("minimized");

    // Atualiza o ícone com base no estado
    $(this).html(isMinimized ? "&#8592;" : "&#8594;"); // Ícone de seta para a esquerda ou direita
  });

  // Alternar visibilidade do texto no menu
  $("#sidebar")
    .on("mouseenter", function () {
      if ($("#sidebar").hasClass("minimized")) {
        // Se o menu estiver minimizado, ele se expande ao passar o mouse
        $("#sidebar").removeClass("minimized");
        $("#content").removeClass("sidebar-minimized");
      }
    })
    .on("mouseleave", function () {
      if (!$("#sidebar").hasClass("minimized")) {
        // Se o menu não estiver minimizado, ele se minimiza ao sair o mouse
        $("#sidebar").addClass("minimized");
        $("#content").addClass("sidebar-minimized");
      }
    });

  // Se o menu estiver minimizado, esconder o texto
  $("#sidebar.minimized .components li a .menu-text").css("display", "none");

  // Garantir que o ícone da foto do perfil e os ícones do menu se ajustem ao passar o mouse
  $("#sidebar").hover(
    function () {
      if ($("#sidebar").hasClass("minimized")) {
        // Expande a foto do perfil e mostra ícones
        $(".profile-section img").css({ width: "40px", height: "40px" });
      }
    },
    function () {
      if ($("#sidebar").hasClass("minimized")) {
        $(".profile-section img").css({ width: "50px", height: "50px" });
      }
    }
  );
});
