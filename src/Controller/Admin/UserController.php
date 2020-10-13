<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
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
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/new", name="admin_user_new",methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token2');
        if ($this->isCsrfTokenValid('form-unew', $submittedToken)) {
            if ($form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('admin_user');
            }
        }

        return $this->render('admin/user/create_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit",methods="GET|POST")
     */
    public function edit(UserRepository $userRepository, Request $request, User $user): Response
    {
        $userid = $user->getId();
        $userdetail = $userRepository->findBy(
            ['id' => $userid]
        );
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token1');
            if ($this->isCsrfTokenValid('form-uedit', $submittedToken)) {
                $user->setName($request->request->get("name"));
                $user->setAddress($request->request->get("address"));
                $user->setCity($request->request->get("city"));
                $user->setPhone($request->request->get("phone"));
                $em = $this->getDoctrine()->getManager();
                $em->persist($userdetail);
                $em->flush();
                return $this->redirectToRoute('admin_user');
            }
        }
        return $this->render('admin/user/edit_form.html.twig', [
            'userdetail' => $userdetail,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete",methods="GET|POST")
     */
    public function delete(Request $request, User $users): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($users);
        $em->flush();
        return $this->redirectToRoute('admin_user');
    }
}
