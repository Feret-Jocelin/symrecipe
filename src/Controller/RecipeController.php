<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe.index', methods: ['GET'])]
    /**
     * Route for fecthin all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $repository->findAll();
        if(!$recipes) {
            throw new NotFoundHttpException('No recipes were found !');
        }

        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page',1),
            10
        );


        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $pagination,
        ]);
    }

    /**
     * Route for creating a recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('recipe/new', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a bien été ajoutée'
            );

            return $this->redirectToRoute('recipe.index');
        }
        
        return $this->render('pages/recipe/new.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('recipe/edit/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    /**
     * Route for editing a recipe
     *
     * @param RecipeRepository $repository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(RecipeRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form'=> $form,
        ]);
    }

    #[Route('recipe/delete/{id}', name: 'recipe.delete', methods: ['GET'])]
    /**
     * Route for deleting a recipe
     *
     * @param RecipeRepository $repository
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(RecipeRepository $repository, int $id, EntityManagerInterface $manager): Response
    {
        $recipe = $repository->findOneBy(['id'=> $id]);
        if(!$recipe){
            $this->addFlash(
                'warning', 
                'La recette n\'a pas été trouvée'
            );
            $this->redirectToRoute('recipe.index');
        }

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès'
        );
        
        return $this->redirectToRoute('recipe.index');
    }
}
