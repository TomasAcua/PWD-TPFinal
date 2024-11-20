<div class="modal fade" id="modal-editar-producto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-producto" enctype="multipart/form-data">
                    <input type="hidden" id="idproducto" name="idproducto">
                    <div class="mb-3">
                        <label for="edit_pronombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_pronombre" name="pronombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_prodetalle" class="form-label">Detalle</label>
                        <textarea class="form-control" id="edit_prodetalle" name="prodetalle" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_procantstock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="edit_procantstock" name="procantstock" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="edit_precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_imagen" class="form-label">Nueva Imagen (opcional)</label>
                        <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>