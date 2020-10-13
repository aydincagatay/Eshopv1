<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Orders;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("admin/orders/{slug}", name="admin_orders_index")
     */
    public function orders($slug, OrdersRepository $ordersRepository)
    {
        $orders = $ordersRepository->findBy(['status' => $slug]);
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("admin/orders/show/{id}", name="admin_orders_show", methods="GET")
     */
    public function show($id, OrderDetailRepository $orderDetailRepository, Orders $orders): Response
    {
        $orderdetail = $orderDetailRepository->findBy([
            'orderid' => $id
        ]);
        return $this->render('admin/orders/show.html.twig', [
            'orderdetail' => $orderdetail,
            'order' => $orders,
        ]);
    }


    /**
     * @Route("admin/orders/{id}/update", name="admin_orders_update", methods="POST")
     */
    public function order_update($id, Request $request, Orders $orders): Response
    {

        $shipinfo = $request->get("shipinfo");
        $note = $request->get("note");
        $status = $request->get("status");
        $em = $this->getDoctrine()->getManager();
        $sql = "UPDATE orders SET shipinfo=:shipinfo,note=:note,status=:status WHERE id=:id";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('shipinfo', $shipinfo);
        $statement->bindValue('note', $note);
        $statement->bindValue('status', $status);
        $statement->bindValue('id', $id);
        $statement->execute();
        $this->addFlash('success', "Sipariş bilgileri güncellenmiştir!");
        return $this->redirectToRoute('admin_orders_show', array('id' => $id,));
    }

    /**
     * @Route("admin/comment/{slug}", name="admin_comment_index", methods="GET")
     */
    public function comments($slug, CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findBy(['status' => $slug]);
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comment
        ]);
    }

    /**
     * @Route("admin/comment/{id}/update", name="admin_comment_update", methods="GET|POST")
     */
    public function comment_edit($id, Request $request, Comment $comment): Response
    {

        $em1 = $this->getDoctrine()->getManager();
        $sql1 = "SELECT u.name,u.email,p.title FROM comment c, user u,product p WHERE
                  c.userid = u.id AND
                  c.productid =p.id AND
                  c.id =" . $id;

        $statement1 = $em1->getConnection()->prepare($sql1);
        $statement1->execute();
        $comment2 = $statement1->fetchAll();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token3');
        if ($this->isCsrfTokenValid('form-comment', $submittedToken)) {

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'comment2' => $comment2,
            'form' => $form->createView(),
        ]);
    }
}
