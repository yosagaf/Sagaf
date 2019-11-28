<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Work;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Repository\BlogRepository;
use App\Repository\WorkRepository;
use App\Repository\TaxonomieRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
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
    public function about(EntityManagerInterface $manager, TaxonomieRepository $repo)
    {
        $about = $repo->findOneBy(array('page' => 'about'));

        if(!$about){
            $about = new Taxonomie();
            $about->setPage('about')
                    ->setText('About me');
            
            $manager->persist($about);
            $manager->flush();
        }

        return $this->render('index/about.html.twig', [
            'about' => $about
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(BlogRepository $repo)
    {
        $blogs = $repo->findAll();
        return $this->render('index/blog.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_details")
     */
    public function blogDetails(Request $request, EntityManagerInterface $manager, Blog $blog)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $comment->setDate(new \DateTime('now'));
            $comment->setBlog($blog);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Comment create');

            return $this->redirectToRoute('blog_details', ['id' => $blog->getId()]);
        }

        return $this->render('index/blog_details.html.twig', [
            'form' => $form->createView(),
            'blog' => $blog
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, EntityManagerInterface $manager, TaxonomieRepository $repo, \Swift_Mailer $mailer)
    {
        $parameters = $repo->findOneBy(array('page' => 'parameters'));

        if(!$parameters){
            $parameters = new Taxonomie();
            $parameters->setPage('parameters')
                    ->setText('contact@sagaf-youssouf.com');
            
            $manager->persist($parameters);
            $manager->flush();
        }

        $data = ['name' => null, 'email' => null, 'text' => null];
        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $email = $form->get('email')->getData();
            $text = $form->get('text')->getData();

            $message = (new \Swift_Message('Contact sagaf-youssouf.com'))
                ->setFrom('support@sagaf-youssouf.com')
                ->setTo($parameters->getText())
                ->setBody(
                    $this->renderView('index/email.html.twig',[
                        'name' => $name,
                        'email' => $email,
                        'text' => $text
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success', 'Email sended');
            return $this->redirectToRoute('contact');

        }

        return $this->render('index/contact.html.twig', [
            'form' => $form->createView(),
            'parameters' => $parameters
        ]);
    }
}
