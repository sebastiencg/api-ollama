<?php

namespace App\Controller;

use App\Service\Askia;
use App\Service\Chat;
use App\Service\Conversation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    #[Route('/chat/', name: 'app_chat', methods: ["POST"])]
    public function index(Chat $chatService ,Request $request,Conversation $conversation,SessionInterface $session): Response
    {

        $jsonData = json_decode($request->getContent(), true);

        $userMessage = $jsonData['message'] ?? null;


        if ($userMessage !== null) {
            $conversation->addMessageToConversation('user',$userMessage,$session);
            $response = $chatService->sendMessage($userMessage);
            $conversation->addMessageToConversation($response["role"],$response["content"],$session);
            return $this->json($response,Response::HTTP_OK);

        }

        return $this->json("bad request",Response::HTTP_BAD_REQUEST);
    }

    #[Route('/upload', name: 'app_upload_file')]
    public function uploadFile(Request $request): Response
    {
        // Récupérer le fichier de la requête
        $file = $request->files->get('document');

        // Vérifier si un fichier a été envoyé
        if ($file) {
            // Déplacez le fichier vers le répertoire de votre choix
            $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $file->move($uploadsDirectory, $file->getClientOriginalName());

            // Faites ce que vous voulez avec le fichier, par exemple, renvoyez une réponse JSON
            return $this->json(['message' => 'Fichier reçu avec succès', 200]);
        } else {
            return $this->json(['error' => 'Aucun fichier n a été envoyé'], 400);
        }
    }

    #[Route('/chat/messages', name: 'app_get_messages')]
    public function getMessages(): Response
    {
        // check user connected
        $user = $this->getUser();
        if (!$user) {return $this->json("No user connected", 200);}

        $messages = $user->getProfile()->getResponse();

        // Return messages
        return $this->json($messages, 200);
    }

    #[Route('/chat/ask/pdf', name: 'app_file_ask')]
    public function chat(Request $request, Askia $service): Response
    {
        // check user connected
        $user = $this->getUser()->getProfile();
        if (!$user) {return $this->json("No user connected", 200);}


        // Get Prompt, Check if prompt
        $jsonData = json_decode($request->getContent(), true);
        $userPrompt = $jsonData['prompt'] ?? null;
        if (!$userPrompt) {return $this->json("Pas de prompt reçu", 200);}


        // Call service
        $response = $service->sendPrompt($userPrompt);

        // Add question and response to DB
        $user->getProfile()->addConversation($userPrompt, $response); // Assurez-vous d'implémenter cette méthode dans votre entité User.

        // Réponse
        return $this->json($response, 200);
    }
}
