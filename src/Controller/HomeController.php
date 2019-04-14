<?php

namespace App\Controller;

use App\Repository\Admin\CategoryRepository;
use App\Repository\Admin\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository,CategoryRepository $categoryRepository)
    {
        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE status='True' ORDER BY ID DESC LIMIT 4";
        $statement = $em->getConnection()->prepare($sql);
        //  $statement->bindValue('parentid',$parent);
        $statement->execute();
        $sliders=$statement->fetchAll();
        //dump($sliders);
        //die();

        $cats=$this->categorytree();
        $cats[0]='<ul id="menu-v">';
        //print_r($cats);
        //die();
        return $this->render('home/index.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'sliders' => $sliders,
        ]);
    }
    /**
     * @Route("/hakkimizda", name="hakkimizda")
     */
    public function hakkimizda(SettingRepository $settingRepository)
    {
        $data=$settingRepository->findAll();
        return $this->render('home/hakkimizda.html.twig', [
            'data' => $data,
        ]);
    }
    /**
     * @Route("/iletisim", name="iletisim")
     */
    public function iletisim(SettingRepository $settingRepository)
    {
        $data=$settingRepository->findAll();
        return $this->render('home/iletisim.html.twig', [
            'data' => $data,
        ]);
    }
    /**
     * @Route("/referans", name="referans")
     */
    public function referans(SettingRepository $settingRepository)
    {
        $data=$settingRepository->findAll();
        return $this->render('home/referans.html.twig', [
            'data' => $data,
        ]);
    }
    public function categorytree($parent=0,$user_tree_array='')
    {
        if(!is_array($user_tree_array))
            $user_tree_array=array();

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM category WHERE status='True' AND parentid=".$parent;
        $statement = $em->getConnection()->prepare($sql);
      //  $statement->bindValue('parentid',$parent);
        $statement->execute();
        $result=$statement->fetchAll();
        if(count($result)>0){
            $user_tree_array[]="<ul>";
            foreach ($result as $row) {
                $user_tree_array[] = "<li><a href='/category/".$row['id']."'>".$row['title']."</a>";
                $user_tree_array = $this->categorytree($row['id'], $user_tree_array);
            }
            $user_tree_array[]="</li></ul>";

            }
            return $user_tree_array;
        }

    /**
     * @Route("/category/{catid}", name="category_products", methods="GET")
     */
    public function CategoryProducts($catid, CategoryRepository $categoryRepository)
    {
        $cats=$this->categorytree();
        $data=$categoryRepository->findBy(
            ['id' => $catid]
        );

        //dump($data);
        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE status='True' AND category_id=".$catid;
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('catid',$catid);
        $statement->execute();
        $products=$statement->fetchAll();
        //dump($products);
        //die();
        return $this->render('home/products.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'products' => $products,
        ]);
    }
}


















