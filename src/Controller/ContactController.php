<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailerInterface): Response
    {
        $data = new ContactDTO();
        $data->name = "John Deo";
        $data->email = "John@deo.fr";
        $data->message = "Super site";
        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $email = (new TemplatedEmail())
                ->subject("Demande de contact")
                ->to($data->service)
                ->from($data->email)
                ->htmlTemplate("mails/contact.html.twig")
                ->context(['data' => $data]);

                $mailerInterface->send($email);
            } catch (\Throwable $th) {
                $this->addFlash(
                   'danger',
                   'Impossible d\'envoyer ce email'
                );
            }

            $this->addFlash(
               'success',
               'Votre email à bien été envoyé'
            );

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
