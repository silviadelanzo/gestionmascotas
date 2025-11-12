<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main class="container">
  <h2>Prestadores</h2>
  <form id="filtro">
    <select id="provSel"></select>
    <select id="locSel"></select>
    <input type="text" id="q" placeholder="Buscar nombre o rubro">
    <button type="button" id="buscar">Buscar</button>
  </form>
  <div id="resultados"></div>
  <script src="/assets/js/app.js"></script>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>