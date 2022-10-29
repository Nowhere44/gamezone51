<?php

namespace App\Controller;

use App\Form\Type\GameType;
use App\Form\Type\EditGameType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\GameLauncher;
use App\Repository\GameLauncherRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class GameController extends AbstractController
{
    #[Route('/product/new', name: 'app_game')]
    #[IsGranted('ROLE_USER')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $Manager = $doctrine->getManager();

        $game = new GameLauncher;

        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $game = $form->getData();

            $Manager->persist($game);

            $Manager->flush();
            return $this->redirectToRoute('admingame');
        }

        return $this->renderForm('game/index.html.twig', [
            'controller_name' => 'GameController', 'form' => $form
        ]);
    }




    #[Route('', name: 'allgame')]
    public function allgames(GameLauncherRepository $game): Response
    {


        $all = $game->findAllOrderById();


        return $this->render('game/all.html.twig', [
            'controller_name' => 'GameController',  'outall' => $all
        ]);
    }



    #[Route('/product/:{id}', name: 'once')]
    public function one(GameLauncher $game, GameLauncherRepository $games): Response
    {

        $id = $game->getId();
        $type = $game->getTypeOfGame();

        $jeuxSimilaires = $games->findAllIdentiqueThing($id, $type);



        return $this->render('game/onegame.html.twig', [
            'controller_name' => 'GameController', 'one' => $game, 'similaire' => $jeuxSimilaires
        ]);
    }



    #[Route('/admin', name: 'admingame')]
    #[IsGranted('ROLE_USER')]
    public function admin(GameLauncherRepository $game): Response
    {


        $all = $game->findAllOrderByPrice();


        return $this->render('game/admin.html.twig', [
            'controller_name' => 'GameController',  'all' => $all
        ]);
    }




    #[Route('/product/:{id}/update', name: 'gameedit')]
    #[IsGranted('ROLE_USER')]
    public function update(ManagerRegistry $doctrine, Request $request, GameLauncher $game, GameLauncher $gemme): Response
    {
        $Manager = $doctrine->getManager();

        $editform = $this->createForm(EditGameType::class, $game);

        $editform->handleRequest($request);

        if ($editform->isSubmitted() && $editform->isValid()) {

            $game = $editform->getData();

            $Manager->flush();
            return $this->redirectToRoute('admingame');
        }

        return $this->render('game/edit.html.twig', [
            'controller_name' => 'GameController', 'formedit' => $editform->createView(), 'une' => $gemme
        ]);
    }



    #[Route('/product/:{id}/delete', name: 'gamedel')]
    #[IsGranted('ROLE_USER')]
    public function delete(ManagerRegistry $doctrine, GameLauncher $game): Response
    {
        $Manager = $doctrine->getManager();

        $Manager->remove($game);
        $Manager->flush();

        return $this->redirectToRoute('admingame');
    }
}
