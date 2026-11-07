<?php

class HomeController extends mainController
{
    /**
     * Halaman Utama / Landing Page
     * GET /
     */
    public function index($core)
    {
        $core->set('title', 'Framework-X | Clean Skeleton');
        $core->set('description', 'Kerangka mentah framework SIMA siap pakai untuk API, integrasi teknologi baru, dan template kustom.');
        $core->set('php_version', PHP_VERSION);
        $core->set('base_url', $core->get('API_HOST'));

        echo $this->view->render('home.php');
    }
}
