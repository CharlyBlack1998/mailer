<?php

namespace App\Controller\Message;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\MessageUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/message/create", name="message_create")
     */
    public function createMessage(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, ['without' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($user);
            $entityManager->persist($message);
            $entityManager->flush();
            $this->addFlash('success', 'Message success created');

            return $this->redirectToRoute('user_profile');
        }

        return $this->renderForm('message/form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/sentmessages/list", name="sent_messages_list")
     */
    public function listSentMessages(EntityManagerInterface $entityManager): Response
    {
        $sentMessages = $entityManager->getRepository(Message::class)->findBy(['sender' => $this->getUser()]);

        return $this->render('message/sentmessage/list.html.twig', [
            'sent_messages' => $sentMessages,
        ]);
    }

    /**
     * @Route("/receivedmessages/list", name="received_messages_list")
     */
    public function listReceivedMessages(EntityManagerInterface $entityManager): Response
    {
        $receivedMessages = $entityManager->getRepository(Message::class)->findBy(['recipient' => $this->getUser()]);

        return $this->render('message/receivedmessage/list.html.twig', [
            'received_messages' => $receivedMessages,
        ]);
    }

    /**
     * @Route("/sentmessages/update/{id}", name="sentmessages_update")
     */
    public function updateSentMessages(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        /** @var Message $sentMessage */
        $sentMessage = $entityManager->getRepository(Message::class)->find($id);
        $form = $this->createForm(MessageUpdateType::class, $sentMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sentMessage);
            $entityManager->flush();
            $this->addFlash('success', 'Sent message success updated');

            return $this->redirectToRoute('user_profile');
        }

        return $this->renderForm('message/form.html.twig', [
            'form' => $form,
            'color' => $sentMessage->getColor(),
        ]);
    }

    /**
     * @Route("/sentmessages/delete/{id}", name="sentmessages_delete")
     */
    public function deleteSentMessages(EntityManagerInterface $entityManager, int $id): Response
    {
        /** @var Message $sentMessage */
        $sentMessage = $entityManager->getRepository(Message::class)->find($id);
        $entityManager->remove($sentMessage);
        $entityManager->flush();
        $this->addFlash('success', 'Sent message success deleted');

        return $this->redirectToRoute('user_profile');
    }
}
