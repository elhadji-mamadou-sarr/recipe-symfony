<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategorieRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recettes', name:"admin.recipe.")]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $recipeRepository, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {  
        $recipes = $recipeRepository->findAll();
        return $this->render("admin/recipe/index.html.twig", [
            'recipes' => $recipes
        ]);
    }


    
    #[Route('/create', name:"create")]
    public function create(Request $request, EntityManagerInterface $em){
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette à bien été ajouter !');
            return $this->redirectToRoute('admin.recipe.index');
        }
        
        return $this->render('admin/recipe/create.html.twig',[
            'form' => $form,
        ]);
    }
    

    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => Requirement::DIGITS, 'slug' => Requirement::ASCII_SLUG])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);
        if ($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('admin.recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        return $this->render("admin/recipe/show.html.twig", 
        [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}', name: "edit", methods:['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em){
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var  \Symfony\Component\HttpFoundation\File\UploadedFile $file**/
            /*$file = $form->get('thumbnailFile')->getData();
            $fileName = $recipe->getId(). '.'. $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir').'/public/recttes/images', $fileName);
            $recipe->setThumbnail($fileName);*/
            $em->flush();
            $this->addFlash('success', 'La recette à bien été modifier !');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe'=> $recipe,
            'form'=> $form,
        ]);
    }


    
    #[Route('/{id}', name:"delete", methods:['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Recipe $recipe, EntityManagerInterface $em){
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette à bien été supprimer !');
        return $this->redirectToRoute('admin.recipe.index');
    }

    


}
