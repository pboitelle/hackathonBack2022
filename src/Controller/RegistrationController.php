<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;


class RegistrationController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    #[Route('/api/registration', name: 'api_register', methods: ['POST'])]
    public function __invoke(MailerInterface $mailer, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $user = new User();

        $password = 'loreal232';
        $user->setEmail('loreal@gmail.com');
        $hashedPassword = $this->hasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $email = (new Email())
        ->from('pi.boitelle@gmail.com')
        ->to('pi.boitelle@gmail.com')
        ->subject('LOGIN - L\'OREAL')
        ->html('<h1>Vos identifiants</h1><p><b>Email :</b> loreal@gmail.com</p><p><b>Password :</b> loreal232</p>');

        $mailer->send($email);
    }
}
