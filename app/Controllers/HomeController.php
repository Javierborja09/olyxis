<?php
namespace App\Controllers;

use Framework\Core\Controller;

class HomeController extends Controller {
    
    public function index($request) {
        return $this->view('home/index', [
            'title' => 'Bienvenido',
            'message' => '¡Tu framework está funcionando correctamente!'
        ], 'layouts/main');
    }
    
    public function about($request) {
        return $this->view('home/about', [
            'title' => 'Acerca de'
        ], 'layouts/main');
    }
    
    public function contact($request) {
        return $this->view('home/contact', [
            'title' => 'Contacto'
        ], 'layouts/main');
    }
    
    public function contactSubmit($request) {
        $data = $request->post();
        
        return $this->json([
            'success' => true,
            'message' => 'Mensaje recibido correctamente',
            'data' => $data
        ]);
    }
}