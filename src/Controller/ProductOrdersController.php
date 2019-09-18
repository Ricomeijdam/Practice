<?php

namespace App\Controller;

use App\Entity\ProductOrders;
use App\Form\ProductOrdersType;
use App\Repository\ProductOrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/productorders")
 */
class ProductOrdersController extends AbstractController
{
    /**
     * @Route("/", name="product_orders_index", methods={"GET"})
     */
    public function index(ProductOrdersRepository $productOrdersRepository): Response
    {
        return $this->render('product_orders/index.html.twig', [
            'product_orders' => $productOrdersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_orders_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $productOrder = new ProductOrders();
        $form = $this->createForm(ProductOrdersType::class, $productOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productOrder);
            $entityManager->flush();

            return $this->redirectToRoute('product_orders_index');
        }

        return $this->render('product_orders/new.html.twig', [
            'product_order' => $productOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_orders_show", methods={"GET"})
     */
    public function show(ProductOrders $productOrder): Response
    {
        return $this->render('product_orders/show.html.twig', [
            'product_order' => $productOrder,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_orders_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProductOrders $productOrder): Response
    {
        $form = $this->createForm(ProductOrdersType::class, $productOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_orders_index');
        }

        return $this->render('product_orders/edit.html.twig', [
            'product_order' => $productOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_orders_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProductOrders $productOrder): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productOrder->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($productOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_orders_index');
    }
}
