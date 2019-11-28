<?php

namespace App\Controller;

use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/works", name="admin_works")
     */
    public function index(WorkRepository $repo)
    {
        $works = $repo->findAll();
        return $this->render('admin/index.html.twig', [
            'works' => $works
        ]);
    }

    /**
     * @Route("/admin/works/new", name="admin_works_create")
     * @Route("/admin/works/{id}/edit", name="admin_works_edit")
     */
    public function manage(Request $request, ObjectManager $manager, Work $work = null, Filesystem $fileSystem)
    {
        if (!$work) {
            $work = new Work();
        }

        $editMode = $work->getId() != null;
        if($editMode){
            $name = $work->getData()->getName();
        }

        $form = $this->createForm(WorkType::class, $work);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noneChange = false;
            if($editMode && $work->getData()->getName() != null){
                $fileSystem->remove($this->getParameter('data_directory').'/'.$name);
            } elseif($editMode && $work->getData()->getName() == null){
                $work->getData()->setName($name);
                $noneChange = true;
            }

            if(!$noneChange){
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $work->getData()->getName();

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                $work->getData()->setExtension($file->guessExtension());
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('data_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_works');
                }
                $work->getData()->setName($fileName);
            }
            

            $manager->persist($work);
            $manager->flush();

            if (!$editMode) {
                $this->addFlash('success', 'Work créé');
            } else {
                $this->addFlash('success', 'Work modifié');
            }

            return $this->redirectToRoute('admin_works');
        }

        return $this->render('admin/manage.html.twig', [
            'form' => $form->createView(),
            'editMode' => $editMode,
            'work' => $work
        ]);
    }

    /**
     * @Route("/admin/works/{id}/delete", name="admin_works_delete")
     */
    public function delete(Work $work, ObjectManager $manager, Filesystem $fileSystem)
    {
        $fileSystem->remove($this->getParameter('data_directory').'/'.$work->getData()->getName());
        $manager->remove($work);
        $manager->flush();
        $this->addFlash('success', 'Work supprimé');
        return $this->redirectToRoute('admin_works');
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
