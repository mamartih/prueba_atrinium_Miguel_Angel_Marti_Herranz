<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\User;
use App\Entity\User_Sector;
use App\Form\User_SectorType;
use App\Form\UserEditType;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


use function PHPSTORM_META\type;

class SignupController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {   
        $form = $this->createForm(UserType::class,NULL,array(
            'action' => '/signup_submit',
            'method' => 'POST',));
        
        return $this->render('signup/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup_submit", name="signup-submit")
     */
    public function newUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {   
        if (isset($_POST['user'])) {
            $user = new User();
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user, $_POST['user']['password']));
            $user->setEmail($_POST['user']['email']);
            $em->persist($user);
            $em->flush();

            $id = $user->getId();
            //echo '<pre>'; var_dump($id); echo '</pre>'; die;

            foreach ($_POST['user']['sector'] as $key => $value) {
                $user_sector = New User_Sector();
                $sector = new Sector();
                $sector = $em->getRepository(Sector::class)->findOneBy(['id'=>$value]);
                $user_sector->setSector($sector);
                $user_sector->setUser($user);
                $em->persist($user_sector);
                $em->flush();
            } 
            
            //echo '<pre>'; var_dump($user); echo '</pre>'; die;
            
            $this->addFlash('success', $user::CORRECT_SIGNUP);
            return $this->redirect("/");
        }
        
        return $this->redirect("/signup");
    }

    /**
     * @Route("/user_list", name="user-list")
     */
    public function ListadoUsuarios(PaginatorInterface $paginator, Request $request)
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}


        /**Recogemos de la BBDD los datos de los usuarios. */
        $em=$this->getDoctrine()->getManager();
        $query = $em->getRepository(User::class)->BuscarUsuarios(null,$em);
    

         /* Listamos y paginamos los datos obtenidos de la BBDD. */
            $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        //Cargamos la vista.
        return $this->render('users/user_list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/edit_user/{id}", name="edit-usuario")
     */
    public function Edit(User $userform, Request $request, $id, PaginatorInterface $paginator)
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados con rol ADMIN, en caso contrario redirección a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        //Obtenemos los datos del usuario que vamos a editar.
        $em = $this->getDoctrine()->getManager();
        $user2 = $em->getRepository(User::class)->findOneBy(['id'=>$id]);
        $user2->setSector();
        //Creamos el formulario a partir de los datos encontrados.
        $form = $this->createForm(UserEditType::class,NULL,array(
            'action' => "/persist_edit_user/".$user2->getId(),
            'method' => 'POST',));
        
        $em=$this->getDoctrine()->getManager();
        $result = $em->getRepository(User::class)->BuscarUserSectores($id,$em);
        
        $sectores=[];
        foreach ($result as $key => $value) {
            array_push($sectores,$value['id']);
        }

        //Cargamos el formulario de edición de Usuario.
        return $this->render('users/user_edit.html.twig', [
            'user2' => $user2,
            'form' => $form->createView(),
            'sectores' => json_encode($sectores),
        ]);
    }

    /**
     * @Route("/persist_edit_user/{id}", name="persist-edit-usuario")
     */
    public function persistEdit(User $userform, Request $request, $id)
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados con rol ADMIN, en caso contrario redirección a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}
        
        //Recuperamos los datos del usuario a editar
        $user2 = new User();
        $em = $this->getDoctrine()->getManager();
        $user2 = $em->getRepository(User::class)->findOneBy(['id'=>$id]);
        
        if (isset($_POST['user_edit'])) {
            
            $user2->setEmail($_POST['user_edit']['email']);
            $roles=[$_POST['user_edit']['roles']];
            $user2->setRoles($roles);
            $em->persist($user2);
            $em->flush();
            
            //Elimino todos los sectores asociados
            $em->getRepository(User::class)->DeleteUserSectores($id,$em);
            
            foreach ($_POST['user_edit']['sector'] as $key => $value) {
                $user_sector = New User_Sector();
                $sector = new Sector();
                $sector = $em->getRepository(Sector::class)->findOneBy(['id'=>$value]);
                $user_sector->setSector($sector);
                $user_sector->setUser($user2);
                $em->persist($user_sector);
                $em->flush();
            }
            
            $this->addFlash('success', 'Correct Update');
            return $this->redirect("/user_list");
        }
        
        return $this->redirect("/user_list");
    }

    /**
     * @Route("/persist_delete_user/{id}", name="persist-delete-user")
     */
    public function persistDelete($id)
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        //Recuperamos los datos del usuario a eliminar
        $user2 = new User();
        $em = $this->getDoctrine()->getManager();
        $user2 = $em->getRepository(User::class)->findOneBy(['id'=>$id]);
        
        //Elimino todos los sectores asociados
        $em->getRepository(User::class)->DeleteUserSectores($id,$em);
        
        //Elimino el usuario
        $em->remove($user2);
        $em->flush();
        $this->addFlash('success', 'User Deleted!');
        
        /* Volvemos al listado de sectores */
        return $this->redirect('/user_list');
    }

    /**
     * @Route("/delete_user/{id}", name="delete-user")
     */
    public function Delete($id)
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        //Recuperamos los datos del usuario a eliminar
        $user2 = new User();
        $em = $this->getDoctrine()->getManager();
        $user2 = $em->getRepository(User::class)->findOneBy(['id'=>$id]);

        //Cargamos la vista.
        return $this->render('/users/user_delete.html.twig', [
                'id' => $id,
                'user' => $user2
        ]);
    }

    /**
     * @Route("/user_data", name="user-data")
     */
    public function userData()
    {
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/dashboard');}

        return $this->render('users/user_data.html.twig', [
        'user' => $user,
    ]);
        }
}

