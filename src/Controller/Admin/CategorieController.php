<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('amdin/categories', name: 'admin.categorie.')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $categorie =new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $entityManagerInterface->persist($categorie);
            $entityManagerInterface->flush();
            $this->addFlash(
               'success',
               'Categorie a bien été ajouter'
            );
            return $this->redirectToRoute('admin.categorie.index');
        }
        return $this->render('admin/categorie/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'edit', methods:['GET', 'POST'], requirements:['id' => Requirement::DIGITS])]
    public function edit(Categorie $categorie,Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $entityManagerInterface->flush();
            $this->addFlash(
               'success',
               'La categorie à bien été modifier'
            );
            return $this->redirectToRoute('admin.categorie.index');
        }
        return $this->render('admin/categorie/edit.html.twig', [
            'form' => $form,
            'categorie' => $categorie
        ]);
    }

    #[Route('/{id} ', name: 'delete')]
    public function delete(Categorie $categorie, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($categorie);
        $entityManagerInterface->flush();
        $this->addFlash(
           'success',
           'La categorie à bien été supprimer'
        );
        return $this->redirectToRoute('admin.categorie.index');
    }



}
