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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
     * @Route("/admin/cv", name="admin_cv")
     */
    public function cv(Request $request, EntityManagerInterface $manager, TaxonomieRepository $repo, Filesystem $fileSystem)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cv = $repo->findOneBy(array('page' => 'cv'));

        if(!$cv){
            $cv = new Taxonomie();
            $cv->setPage('cv');
            
            $manager->persist($cv);
            $manager->flush();
        }

        if($cv->getData()){
            $name = $cv->getData()->getName();
        } else {
            $name = null;
        }

        $form = $this->createForm(TaxonomieType::class, $cv);

        $form->remove('text');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noneChange = false;
            if($cv->getData()->getName() != null){
                if($name){
                    $fileSystem->remove($this->getParameter('data_directory').'/'.$name);
                }
                
            } elseif($cv->getData()->getName() == null){
                $cv->getData()->setName($name);
                $noneChange = true;
            }

            if(!$noneChange){
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $cv->getData()->getName();

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                $cv->getData()->setExtension($file->guessExtension());
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('data_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_cv');
                }
                $cv->getData()->setName($fileName);
            }

            $manager->persist($cv);
            $manager->flush();

            $this->addFlash('success', 'CV modifié');
            return $this->redirectToRoute('admin_cv');

        }

        return $this->render('admin_taxonomie/cv.html.twig', [
            'form' => $form->createView(),
            'cv' => $cv
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
