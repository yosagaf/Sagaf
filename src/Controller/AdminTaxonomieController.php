<?php

namespace App\Controller;

use App\Entity\Taxonomie;
use App\Form\TaxonomieType;
use App\Repository\TaxonomieRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTaxonomieController extends AbstractController
{
    /**
     * @Route("/admin/about", name="admin_about")
     */
    public function about(Request $request, EntityManagerInterface $manager, TaxonomieRepository $repo, Filesystem $fileSystem)
    {
        $about = $repo->findOneBy(array('page' => 'about'));

        if(!$about){
            $about = new Taxonomie();
            $about->setPage('about')
                    ->setText('About me');
            
            $manager->persist($about);
            $manager->flush();
        }

        if($about->getData()){
            $name = $about->getData()->getName();
        } else {
            $name = null;
        }

        $form = $this->createForm(TaxonomieType::class, $about);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noneChange = false;
            if($about->getData()->getName() != null){
                if($name){
                    $fileSystem->remove($this->getParameter('data_directory').'/'.$name);
                }
                
            } elseif($about->getData()->getName() == null){
                $about->getData()->setName($name);
                $noneChange = true;
            }

            if(!$noneChange){
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $about->getData()->getName();

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                $about->getData()->setExtension($file->guessExtension());
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('data_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_about');
                }
                $about->getData()->setName($fileName);
            }

            $manager->persist($about);
            $manager->flush();

            $this->addFlash('success', 'About modifié');
            return $this->redirectToRoute('admin_about');

        }

        return $this->render('admin_taxonomie/about.html.twig', [
            'form' => $form->createView(),
            'about' => $about
        ]);
    }

    /**
     * @Route("/admin/parameters", name="admin_parameters")
     */
    public function parameters(Request $request, EntityManagerInterface $manager, TaxonomieRepository $repo)
    {
        $parameters = $repo->findOneBy(array('page' => 'parameters'));

        if(!$parameters){
            $parameters = new Taxonomie();
            $parameters->setPage('parameters')
                    ->setText('contact@sagaf-youssouf.com');
            
            $manager->persist($parameters);
            $manager->flush();
        }

        $form = $this->createForm(TaxonomieType::class, $parameters);

        $form->remove('data');
        $form->add('text', EmailType::class);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($parameters);
            $manager->flush();

            $this->addFlash('success', 'Parameters modifié');
            return $this->redirectToRoute('admin_parameters');

        }

        return $this->render('admin_taxonomie/parameters.html.twig', [
            'form' => $form->createView(),
            'parameters' => $parameters
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
