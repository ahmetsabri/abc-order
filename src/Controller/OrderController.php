<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order", name="order", methods={"POST"})
     */
    public function store(Request $request): JsonResponse
    {   
        $user = $this->getCurrentUser();

        $order = new Order();

        $order->setOrderCode($request->get('order_code'));
        $order->setAddress($request->get('address'));
        $order->setProductId($request->get('product_id'));
        $order->setQuantity($request->get('quantity'));
        $order->setShippingData($request->get('shipping_data'));
        $order->setUser($user);

        $em = $this->getDoctrine()->getManager();
        
        $em->persist($order);

        $em->flush();

        return new JsonResponse(['msg'=>'Order created', 'order'=>$this->serialize($order)]);
    }

    /**
     * @Route("/api/order/{id}/", name="order.update", methods={"PUT"})
    */
    public function update(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);

        $em = $this->getDoctrine()->getManager();

        if ($order->getUser() == $user && $order->getShippingData() != 'Deliverd') {

            $order->setOrderCode($request->get('order_code'));
            $order->setAddress($request->get('address'));
            $order->setProductId($request->get('product_id'));
            $order->setQuantity($request->get('quantity'));
            $order->setShippingData($request->get('shipping_data'));
            $em->persist($order);
            $em->flush();
     
           return new JsonResponse('Order updated', 200);

        } 

        return new JsonResponse('Not Authorized', 403);
    }

    /**
     * @Route("/api/order/{id}", name="order.show", methods={"GET"})
    */
    public function show($id)
    {
        $user = $this->getCurrentUser();
        
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        if ($order) {
            if ($order->getUser() == $user) {
                $order = $this->serialize($order);
                return new JsonResponse(compact('order'), 200);
            }
        } else {
            return new JsonResponse('not found', 404);
        }
        return new JsonResponse('Not Authorized', 403);
    }


    /**
     * @Route("/api/order/", name="order.index", methods={"GET"})
    */
    public function index()
    {
        $user = $this->getCurrentUser();
        
        $orders = $user->getOrders();
        
        $data = [];
        foreach ($orders as $order) {
            $data[] = $this->serialize($order);
        }
        
        return new JsonResponse(compact('data'), 200);
        
    }

    protected function getCurrentUser()
    {

        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The Security Bundle is not registered in your application.');
        }
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }
        if (!is_object($user = $token->getUser())) {
            return;
        }
        return $user;
    }


    private function serialize(Order $order) {

        return [
            "id" => $order->getId(),
            "order_code"=>$order->getOrderCode(),
            "product_id"=>$order->getProductId(),
            "quantity"=>$order->getQuantity(),
            "address"=>$order->getAddress(),
            "shipping_data"=>$order->getShippingData()
        ];
    }
}
