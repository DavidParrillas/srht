<?php
/**
 * Controlador Dashboard
 * Sistema de Reservas Hotel Torremolinos (SRHT)
 */

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Database.php';

class DashboardController {
    
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Obtener conexión usando Singleton
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    /**
     * Página principal del dashboard
     */
    public function index() {
        // Requerir autenticación
        AuthController::requerirAutenticacion();
        
        try {
            // Obtener estadísticas básicas del sistema
            $estadisticas = $this->obtenerEstadisticas();

            $page_title = "Dashboard";
            $active_page = "home";
            $child_view = __DIR__ . '/../views/dashboard/content.php';
            
            // Cargar vista
            require_once __DIR__ . '/../views/layouts/main.php';

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al cargar el dashboard: ' . $e->getMessage();
            // En caso de error, es mejor redirigir para evitar problemas de renderizado parcial.
            header('Location: index.php?controller=home&action=index');
            exit();
        }
    }
    
    /**
     * Obtener estadísticas del sistema
     * @return array Estadísticas generales
     */
    private function obtenerEstadisticas() {
        $estadisticas = [];
        
        try {
            // Total de usuarios
            $query = "SELECT COUNT(*) as total FROM Usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['total_usuarios'] = $stmt->fetch()['total'];
            
            // Total de clientes
            $query = "SELECT COUNT(*) as total FROM Cliente";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['total_clientes'] = $stmt->fetch()['total'];
            
            // Total de reservas
            $query = "SELECT COUNT(*) as total FROM Reserva";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['total_reservas'] = $stmt->fetch()['total'];
            
            // Reservas activas (Confirmadas)
            $query = "SELECT COUNT(*) as total FROM Reserva WHERE EstadoReserva = 'Confirmada'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['reservas_activas'] = $stmt->fetch()['total'];
            
            // Habitaciones disponibles
            $query = "SELECT COUNT(*) as total FROM Habitacion WHERE EstadoHabitacion = 'Disponible'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['habitaciones_disponibles'] = $stmt->fetch()['total'];
            
            // Habitaciones ocupadas
            $query = "SELECT COUNT(*) as total FROM Habitacion WHERE EstadoHabitacion = 'Ocupada'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $estadisticas['habitaciones_ocupadas'] = $stmt->fetch()['total'];
            
            return $estadisticas;
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_usuarios' => 0,
                'total_clientes' => 0,
                'total_reservas' => 0,
                'reservas_activas' => 0,
                'habitaciones_disponibles' => 0,
                'habitaciones_ocupadas' => 0
            ];
        }
    }
}
?>