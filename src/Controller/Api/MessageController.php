<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Service\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/messages")
 */
class MessageController extends AbstractFOSRestController
{
    /**
     * @Route("/list", methods={"GET"})
     */
    public function listMessages(Normalizer $normalizer, EntityManagerInterface $entityManager): Response
    {
        $messages = $entityManager->getRepository(Message::class)->findAll();

        return $this->json([
           'messages' =>  $normalizer->normalizeArray($messages, [
               'id',
               'topic',
               'text',
               'recipient' => [
                   'id',
                   'name',
                   'surname',
                   'email',
               ],
               'sender' => [
                   'id',
                   'name',
                   'surname',
                   'email',
               ]
           ])
        ]);
    }
}
