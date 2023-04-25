<?php

namespace App\Controller;

use App\Entity\Alimentos;
use App\Form\Alimentos1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/ali")
 */

class AliController extends AbstractController
{

    /**
     * @Route("/", name="app_ali_index", methods={"GET","POST"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */

    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Obtengo el término de búsqueda enviado desde el formulario
        $busqueda = $request->get('buscador');
        if ($request->request->has('buscar')) {
            // Si se ha enviado el formulario, realizo la búsqueda
            $alimento = null;
            // Si el término de búsqueda es un número, busco por ID
            if (is_numeric($busqueda)) {
                $alimento = $entityManager
                    ->getRepository(Alimentos::class)
                    ->find($busqueda);
            } else {
                // Si el término de búsqueda no es un número, busco por nombre
                $alimento = $entityManager
                    ->getRepository(Alimentos::class)
                    ->findOneBy(['nombre' => $busqueda]);
            }
            // Si se ha encontrado un alimento, lo muestro en la vista
            if ($alimento) {
                return $this->render('ali/index.html.twig', [
                    'alimentos' => [$alimento],
                ]);
            } else {
                // Si no se ha encontrado ningún alimento, muestra un mensaje en la vista
                return $this->render('ali/index.html.twig', [
                    'alimentos' => [],
                ]);
            }
        } else {
            // Si no se ha enviado el formulario, se muestra todos los alimentos en la vista
            $alimentos = $entityManager
                ->getRepository(Alimentos::class)
                ->findAll();
            return $this->render('ali/index.html.twig', [
                'alimentos' => $alimentos,
            ]);
        }
    }
    /**
     * @Route("/new", name="app_ali_new", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alimento = new Alimentos();
        $form = $this->createForm(Alimentos1Type::class, $alimento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($alimento);
            $entityManager->flush();

            return $this->redirectToRoute('app_ali_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ali/new.html.twig', [
            'alimento' => $alimento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_ali_show", methods={"GET"})
     * @param Alimentos $alimento
     * @return Response
     */

    public function show(Alimentos $alimento): Response
    {
        return $this->render('ali/show.html.twig', [
            'alimento' => $alimento,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_ali_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Alimentos $alimento
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function edit(Request $request, Alimentos $alimento, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Alimentos1Type::class, $alimento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ali_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ali/edit.html.twig', [
            'alimento' => $alimento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_ali_delete", methods={"POST"})
     * @param Request $request
     * @param Alimentos $alimento
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function delete(Request $request, Alimentos $alimento, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alimento->getId(), $request->request->get('_token'))) {
            $entityManager->remove($alimento);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ali_index', [], Response::HTTP_SEE_OTHER);
    }
}
