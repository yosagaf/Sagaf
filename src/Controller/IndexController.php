<?php

namespace App\Controller;

use App\Entity\Work;
use App\Repository\WorkRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(WorkRepository $repo)
    {
        $works = $repo->findAll();
        return $this->render('index/index.html.twig', [
            'works' => $works
        ]);
    }

    /**
     * @Route("/work/{id}", name="index_work")
     */
    public function indexWork(Work $work)
    {
        return $this->render('index/work.html.twig', [
            'work' => $work
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('index/about.html.twig', [
            
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog()
    {
        return $this->render('index/blog.html.twig', [
            
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('index/contact.html.twig', [
            
        ]);
    }
}
