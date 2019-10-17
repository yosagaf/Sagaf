<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminTaxonomieController extends AbstractController
{
    /**
     * @Route("/admin/taxonomie", name="admin_taxonomie")
     */
    public function index()
    {
        return $this->render('admin_taxonomie/index.html.twig', [
            'controller_name' => 'AdminTaxonomieController',
        ]);
    }
}
