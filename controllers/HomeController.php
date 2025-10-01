<?php
class HomeController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index() {
        $page_title = "Dashboard";
        $active_page = "dashboard";
        $child_view = "views/dashboard/content.php";
        
        include("views/layouts/main.php");
    }
}