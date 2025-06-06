<?php if (!isset($_SESSION)) session_start(); ?>
<div class="carrito-desplegable" id="carritoPanel">
  <h3>Productos y precios</h3>
  <?php if (!empty($_SESSION['carrito'])): ?>
    <ul class="carrito-items">
      <?php foreach ($_SESSION['carrito'] as $id => $producto): ?>
        <li class="carrito-item">
          <img src="../<?php echo $producto['imagen']; ?>" alt="img">
          <span><?php echo $producto['nombre']; ?></span>
          <span><?php echo $producto['cantidad']; ?>x</span>
          <span><?php echo number_format($producto['precio'], 2); ?>€</span>
          <form action="carrito.php" method="post">
            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
            <input type="hidden" name="accion" value="eliminar">
            <button type="submit">Eliminar</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>

    <p><strong>Total: 
      <?php
        $total = 0;
        foreach ($_SESSION['carrito'] as $producto) {
          $total += $producto['precio'] * $producto['cantidad'];
        }
        echo number_format($total, 2);
      ?>€</strong></p>

    <form action="carrito.php" method="post" style="display:inline;">
      <input type="hidden" name="accion" value="vaciar">
      <button type="submit">Vaciar Cesta</button>
    </form>
    <form action="pago.php" method="post" style="display:inline;">
  <button type="submit" name="comprar" id="comprar-btn">Comprar</button>
</form>

  <?php else: ?>
    <p>Tu carrito está vacío.</p>
  <?php endif; ?>
</div>
