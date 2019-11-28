<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin", name="security_login")
     */
    public function login(UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){
        /*$user = new User();
        $user->setPassword('');
        $hash = $encoder->encodePassword($user, $user->getPassword());
        $user->setUsername("sagaf")
            ->setPassword($hash);
        $manager->persist($user);
        $manager->flush();*/
        return $this->render('security/index.html.twig');
    }

    /**
     * @Route("/admin/deconnexion", name="security_logout")
     */
    public function logout(){}
}
