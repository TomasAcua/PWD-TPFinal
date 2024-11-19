<?php
include_once "../../configuracion.php";
$sesion = new Session();

header('Content-Type: application/json');

$respuesta = [
    'usuario' => null,
    'permisos' => [],
    'roles' => []
];

if ($sesion->activa()) {
    $rolActivo = $sesion->getRolActivo();
    $respuesta['usuario'] = [
        'nombre' => $sesion->getNombreUsuarioLogueado(),
        'rol' => $rolActivo['rol']
    ];
    
    // Obtener menús según el rol activo
    $objMenu = new abmMenu();
    $listaMenus = $objMenu->obtenerMenuRol($rolActivo['rol']);
    
    // Organizar menús en estructura jerárquica
    foreach ($listaMenus as $menu) {
        if ($menu->getObjMenuPadre() === null || $menu->getObjMenuPadre()->getID() === "") {
            $menuItem = [
                'id' => $menu->getID(),
                'menombre' => $menu->getMeNombre(),
                'medescripcion' => $menu->getMeDescripcion(),
                'hijos' => []
            ];
            
            // Buscar hijos
            foreach ($listaMenus as $posibleHijo) {
                if ($posibleHijo->getObjMenuPadre() !== null && 
                    $posibleHijo->getObjMenuPadre()->getID() === $menu->getID()) {
                    $menuItem['hijos'][] = [
                        'id' => $posibleHijo->getID(),
                        'menombre' => $posibleHijo->getMeNombre(),
                        'medescripcion' => $posibleHijo->getMeDescripcion()
                    ];
                }
            }
            
            $respuesta['permisos'][] = $menuItem;
        }
    }
    
    $respuesta['roles'] = $sesion->getRoles();
}

echo json_encode($respuesta);
exit; 