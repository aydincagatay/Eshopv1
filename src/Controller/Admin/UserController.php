<?php

namespace App\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Form\UsersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
 * @Route("/admin/user", name="admin_user")
 */
    public function index()
    {
        $users=$this->getDoctrine()
            ->getRepository(Users::class)
            ->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/new", name="admin_user_new",methods="GET|POST")
     */
    public function new(Request $request):Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_user');
        }

        return $this->render('admin/user/create_form.html.twig', [
            'form'=> $form->createView(),
            ]);
    }
    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit",methods="GET|POST")
     */
    public function edit(Request $request, Users $users):Response
    {
        $form = $this->createForm(UsersType::class, $users);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/user/edit_form.html.twig', [
            'user' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete",methods="GET|POST")
     */
    public function delete(Request $request, Users $users):Response
    {
        $em =$this->getDoctrine()->getManager();
        $em->remove($users);
        $em->flush();
        return $this->redirectToRoute('admin_user');
    }
}
