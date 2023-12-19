<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManagerInterface;

class IngredientController extends AbstractController
{
    /**
     * Route for fecthing all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $repository->findAll();
        if(!$ingredients){
            throw new NotFoundHttpException('No ingredients were found !');
        }
        
        $pagination = $paginator->paginate(
            $ingredients,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $pagination,
        ]);
    }

    /**
     * Route to create an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/new', name:'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $manager->persist($data);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a bien été ajouté !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('ingredient/edit/{id}', name:'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(IngredientRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
    {

        $ingredient = $repository->findOneBy(['id'=> $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $manager->persist($data);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès'
            );

            return $this->redirectToRoute('ingredient.index');
        }
        

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('ingredient/delete/{id}', name:'ingredient.delete', methods: ['GET'])]
    public function delete(IngredientRepository $repository, EntityManagerInterface $manager, int $id): Response
    {
        $ingredient = $repository->findOneBy(['id'=> $id]);
        if(!$ingredient){
            $this->addFlash(
                'Warning',
                'Votre ingrédient n\'a pas été trouvé'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
