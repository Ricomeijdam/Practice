<?php
namespace App\Controller;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
class CartController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(ProductRepository $productRepository)
    {
        $cart = $this->session->get("Cart", array());
        $Products = array();
        $totaal = 0;
        foreach ($cart as $id => $product) {
            array_push($Products, ["Aantal" => $product["Aantal"], "Product" => $productRepository->find($id)]);
            $totaal = $totaal + $product['Aantal'] * $productRepository->find($id)->getPrice();
            $tax = $totaal / 100 * $productRepository->find($id)->getTaxcode();
        }
        return $this->render('cart/index.html.twig', [
            "Products" => $Products,
            "totaal" => $totaal,
            "tax" => $tax,
        ]);
    }

    /**
     * @Route("/cart/{id}/remove", name="cart_remove")
     */
    public function removeAction(Product $product)
    {
        $id = $product->getId();
        $session = $this->get('request_stack')->getCurrentRequest()->getSession();
        $cart = $session->get('Cart', array());

        if (!$cart[$id]) {
            $this->redirectToRoute('cart');
        }
        if (isset($cart[$id])) {
            $cart[$id]["Aantal"]--;
            if ($cart[$id]["Aantal"] < 1) {
                unset($cart[$id]);
            }
        } else {
            return $this->redirect($this->generateUrl('cart'));
        }
        $session->set('Cart', $cart);
        return $this->redirect($this->generateUrl('cart'));
    }

    /**
     * @Route("/cart/{id}/add", name="cart_add")
     */
    public function addAction(Product $product)
    {
        $id = $product->getId();
        $session = $this->get('request_stack')->getCurrentRequest()->getSession();
        $cart = $session->get('Cart', array());

        if (isset($cart[$id])) {
            $cart[$id]["Aantal"]++;
        }
        $session->set('Cart', $cart);
        return $this->redirect($this->generateUrl('cart'));
    }
}