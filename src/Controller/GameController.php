<?php

namespace App\Controller;

use App\Form\Type\GameType;
use App\Form\Type\EditGameType;
use App\Form\Type\SearchType;
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


    #[Route('/result/{name}', name: 're')]
    public function result(string $name, Request $request, GameLauncherRepository $g): Response
    {

        $d = $g->Search($name);
        // dd($s);
        if (count($d) == 1) {


            $id = $d[0]->getId();

            return $this->redirectToRoute('once', ['id' => $id]);
        }

        $searchform = $this->createForm(SearchType::class);
        $searchform->handleRequest($request);

        if ($searchform->isSubmitted() && $searchform->isValid()) {

            $search = $searchform->getData();



            $s = $g->Search2($search);


            return $this->render('request/searchResult.html.twig', ['controller_name' => 'GameController', 'search' => $searchform->createView(), 'searchResults' => $s]);
        }


        return $this->render('request/searchResult.html.twig', ['controller_name' => 'GameController', 'searchResults' => $d, 'search' => $searchform->createView()]);
    }



    #[Route('', name: 'allgame')]
    public function allgames(GameLauncherRepository $game, Request $request): Response
    {

        $searchform = $this->createForm(SearchType::class);
        $searchform->handleRequest($request);

        if ($searchform->isSubmitted() && $searchform->isValid()) {

            $search = $searchform->getData();

            return $this->redirectToRoute('re', ['name' => implode($search)]);
        }
        $all = $game->findAllOrderById();


        return $this->render('game/all.html.twig', [
            'controller_name' => 'GameController',  'outall' => $all, 'search' => $searchform->createView()
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
