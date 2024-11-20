<div class="modal fade" id="modal-add-producto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-producto" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="pronombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="pronombre" name="pronombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="prodetalle" class="form-label">Detalle</label>
                        <textarea class="form-control" id="prodetalle" name="prodetalle" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="procantstock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="procantstock" name="procantstock" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>