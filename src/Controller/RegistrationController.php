<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
    public function __invoke(User $data, MailerInterface $mailer, ManagerRegistry $doctrine)
    {

        $entityManager = $doctrine->getManager();

        //$password = 'loreal232';
        //$user->setEmail('loreal@gmail.com');

        $passwordNotHashed = $data->getPassword();

        $hashedPassword = $this->hasher->hashPassword(
            $data,
            $passwordNotHashed
        );
        $data->setPassword($hashedPassword);

        $entityManager->persist($data);
        $entityManager->flush();

        $email = (new TemplatedEmail())
        ->from('pi.boitelle@gmail.com')
        ->to('pi.boitelle@gmail.com')
        ->subject('LOGIN - L\'OREAL')
        ->htmlTemplate('emails/register.html.twig')
        ->context([
            'emailUser' => $data->getEmail(),
            'passwordUser' => $passwordNotHashed,
        ]);

        $mailer->send($email);
    }
}
