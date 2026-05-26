<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/DessertRepository.php';

class MaintenanceApiController
{
    private DessertRepository $repository;

    public function __construct()
    {
        $this->repository = new DessertRepository();
    }

    public function clientes(): void
    {
        $this->jsonOk($this->repository->clientes($this->search(), $this->page(), $this->perPage()));
    }

    public function createCliente(): void
    {
        try {
            $this->assertPost();
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $email = filter_var((string) ($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';

            if ($nombre === '' || $email === '') {
                throw new InvalidArgumentException('Nombre y email son obligatorios.');
            }

            $this->repository->createCliente($nombre, $email);
            $this->jsonMessage('Cliente creado correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo crear el cliente.', 500);
        }
    }

    public function updateCliente(): void
    {
        try {
            $this->assertPost();
            $id = (int) ($_POST['id'] ?? 0);
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $email = filter_var((string) ($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';

            if ($id <= 0 || $nombre === '' || $email === '') {
                throw new InvalidArgumentException('Datos invalidos para actualizar cliente.');
            }

            $this->repository->updateCliente($id, $nombre, $email);
            $this->jsonMessage('Cliente actualizado correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo actualizar el cliente.', 500);
        }
    }

    public function deleteCliente(): void
    {
        $this->deleteAction(fn(int $id) => $this->repository->deleteCliente($id), 'Cliente eliminado correctamente.');
    }

    public function productos(): void
    {
        $this->jsonOk($this->repository->productos($this->search(), $this->page(), $this->perPage()));
    }

    public function createProducto(): void
    {
        try {
            $this->assertPost();
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $precioRaw = (string) ($_POST['precio'] ?? '');
            $precio = is_numeric($precioRaw) ? (float) $precioRaw : -1;

            if ($nombre === '') {
                throw new InvalidArgumentException('El nombre es obligatorio.');
            }
            if ($precio <= 0) {
                throw new InvalidArgumentException('El precio debe ser numerico y mayor a 0.');
            }

            $this->repository->createProducto($nombre, $precio);
            $this->jsonMessage('Producto creado correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo crear el producto.', 500);
        }
    }

    public function updateProducto(): void
    {
        try {
            $this->assertPost();
            $id = (int) ($_POST['id'] ?? 0);
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $precioRaw = (string) ($_POST['precio'] ?? '');
            $precio = is_numeric($precioRaw) ? (float) $precioRaw : -1;

            if ($id <= 0 || $nombre === '' || $precio <= 0) {
                throw new InvalidArgumentException('Datos invalidos para actualizar producto.');
            }

            $this->repository->updateProducto($id, $nombre, $precio);
            $this->jsonMessage('Producto actualizado correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo actualizar el producto.', 500);
        }
    }

    public function deleteProducto(): void
    {
        $this->deleteAction(fn(int $id) => $this->repository->deleteProducto($id), 'Producto eliminado correctamente.');
    }

    public function ventas(): void
    {
        $this->jsonOk($this->repository->ventas($this->search(), $this->page(), $this->perPage()));
    }

    public function createVenta(): void
    {
        try {
            $this->assertPost();
            $clienteId = (int) ($_POST['cliente_id'] ?? 0);
            $fecha = trim((string) ($_POST['fecha'] ?? ''));
            $totalRaw = (string) ($_POST['total'] ?? '');
            $total = is_numeric($totalRaw) ? (float) $totalRaw : -1;

            if ($clienteId <= 0 || $fecha === '' || $total < 0) {
                throw new InvalidArgumentException('Datos invalidos para crear venta.');
            }

            $this->repository->createVenta($clienteId, $fecha, $total);
            $this->jsonMessage('Venta creada correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo crear la venta.', 500);
        }
    }

    public function updateVenta(): void
    {
        try {
            $this->assertPost();
            $id = (int) ($_POST['id'] ?? 0);
            $clienteId = (int) ($_POST['cliente_id'] ?? 0);
            $fecha = trim((string) ($_POST['fecha'] ?? ''));
            $totalRaw = (string) ($_POST['total'] ?? '');
            $total = is_numeric($totalRaw) ? (float) $totalRaw : -1;

            if ($id <= 0 || $clienteId <= 0 || $fecha === '' || $total < 0) {
                throw new InvalidArgumentException('Datos invalidos para actualizar venta.');
            }

            $this->repository->updateVenta($id, $clienteId, $fecha, $total);
            $this->jsonMessage('Venta actualizada correctamente.');
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo actualizar la venta.', 500);
        }
    }

    public function deleteVenta(): void
    {
        $this->deleteAction(fn(int $id) => $this->repository->deleteVenta($id), 'Venta eliminada correctamente.');
    }

    public function detalleVentas(): void
    {
        $this->jsonOk($this->repository->detalleVentas($this->search(), $this->page(), $this->perPage()));
    }

    public function createDetalleVenta(): void
    {
        try {
            $this->assertPost();
            $ventaId = (int) ($_POST['venta_id'] ?? 0);
            $productoId = (int) ($_POST['producto_id'] ?? 0);
            $cantidad = (int) ($_POST['cantidad'] ?? 0);

            if ($ventaId <= 0 || $productoId <= 0 || $cantidad <= 0) {
                throw new InvalidArgumentException('Datos invalidos para crear detalle de venta.');
            }

            $subtotal = $this->repository->createDetalleVenta($ventaId, $productoId, $cantidad);
            $this->jsonMessage('Detalle de venta creado correctamente.', ['subtotal' => $subtotal]);
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo crear el detalle de venta.', 500);
        }
    }

    public function updateDetalleVenta(): void
    {
        try {
            $this->assertPost();
            $id = (int) ($_POST['id'] ?? 0);
            $ventaId = (int) ($_POST['venta_id'] ?? 0);
            $productoId = (int) ($_POST['producto_id'] ?? 0);
            $cantidad = (int) ($_POST['cantidad'] ?? 0);

            if ($id <= 0 || $ventaId <= 0 || $productoId <= 0 || $cantidad <= 0) {
                throw new InvalidArgumentException('Datos invalidos para actualizar detalle de venta.');
            }

            $subtotal = $this->repository->updateDetalleVenta($id, $ventaId, $productoId, $cantidad);
            $this->jsonMessage('Detalle de venta actualizado correctamente.', ['subtotal' => $subtotal]);
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo actualizar el detalle de venta.', 500);
        }
    }

    public function deleteDetalleVenta(): void
    {
        $this->deleteAction(fn(int $id) => $this->repository->deleteDetalleVenta($id), 'Detalle de venta eliminado correctamente.');
    }

    public function optionsClientes(): void
    {
        $this->jsonOk(['rows' => $this->repository->clientesOptions()]);
    }

    public function optionsProductos(): void
    {
        $this->jsonOk(['rows' => $this->repository->productosOptions()]);
    }

    public function optionsVentas(): void
    {
        $this->jsonOk(['rows' => $this->repository->ventasOptions()]);
    }

    public function productoPrecio(): void
    {
        try {
            $productoId = (int) ($_GET['producto_id'] ?? 0);
            if ($productoId <= 0) {
                throw new InvalidArgumentException('Producto invalido.');
            }

            $this->jsonOk(['precio' => $this->repository->productoPrecio($productoId)]);
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo obtener el precio del producto.', 500);
        }
    }

    private function search(): string
    {
        return trim((string) ($_GET['search'] ?? ''));
    }

    private function page(): int
    {
        return max(1, (int) ($_GET['pageNumber'] ?? 1));
    }

    private function perPage(): int
    {
        return max(1, min(200, (int) ($_GET['perPage'] ?? 20)));
    }

    private function assertPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidArgumentException('Metodo no permitido.');
        }
    }

    private function deleteAction(callable $handler, string $successMessage): void
    {
        try {
            $this->assertPost();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new InvalidArgumentException('ID invalido.');
            }

            $handler($id);
            $this->jsonMessage($successMessage);
        } catch (InvalidArgumentException $error) {
            $this->jsonError($error->getMessage(), 422);
        } catch (Throwable $error) {
            $this->jsonError('No se pudo eliminar el registro.', 500);
        }
    }

    private function jsonOk(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['ok' => true], $data));
    }

    private function jsonMessage(string $message, array $extra = []): void
    {
        $this->jsonOk(array_merge(['message' => $message], $extra));
    }

    private function jsonError(string $message, int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode(['ok' => false, 'message' => $message]);
    }
}
