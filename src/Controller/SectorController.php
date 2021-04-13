<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Form\EditSectorType;
use App\Form\SectorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SectorController extends AbstractController
{
    /*Con la instrucción @IsGranted("ROLE_ADMIN") podemos evitar el acceso a la función
    a todos los usuarios que no sean ADMIN, pero para evitar la pagina de error
    redireccionaremos desde la propia función*/
    /**
     * @Route("/sector", name="sector")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request): Response
    {
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        //Creamos el formulario de Sector.
        $sector = new Sector();
        $form = $this->createForm(SectorType::class,$sector);
        $form->handleRequest($request);

        /*Si enviamos el formulario, y es valido creamos nuevo Sector
        y retornamos al listado de sectores*/
        if ($form->isSubmitted()) {
            if ($form->get('save')->isClicked()&&$form->isValid()) { 
            $em=$this->getDoctrine()->getManager();
            $em->persist($sector);
            $em->flush();
            return $this->redirect('/sector_list');
            }
            if ($form->get('cancel')->isClicked()) { 
                return $this->redirect('/sector_list');   
            }  
        }

        //Cargamos el formulario de creación de sector.
        return $this->render('sector/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit_sector/{id}", name="edit-sector")
     */
    public function Edit( Request $request, $id): Response
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}
        
        //Obtenemos los datos del sector que vamos a editar.
        $em = $this->getDoctrine()->getManager();
        $sector = $em->getRepository(Sector::class)->findOneBy(['id'=>$id]);

        //Creamos el formulario a partir de los datos encontrados.
        $form = $this->createForm(EditSectorType::class, $sector);
        $form->handleRequest($request);

        /*Si enviamos el formulario y es valido, actualizamos el registro
        y retornamos al listado de sectores*/
        if ($form->isSubmitted()) {
            if ($form->get('update')->isClicked()&&$form->isValid()) { 
                $em=$this->getDoctrine()->getManager();
                $em->persist($sector);
                $em->flush();
                $this->addFlash('success', 'Sector Updated! Inaccuracies squashed!');
                return $this->redirect('/sector_list');
            }else{}
            if ($form->get('cancel')->isClicked()) { 
                return $this->redirect('/sector_list');   
            }  
        }
        
        //Cargamos el formulario de edición de sector.
        return $this->render('sector/sector_edit.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sector_list", name="sector-list")
     */
    public function ListadoSectores(PaginatorInterface $paginator, Request $request)
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        
        /*Seguridad. Comprobamos Rol de usuario y definimos el acceso a los datos
        en función de dicho Rol*/
        if ($role[0] != 'ROLE_ADMIN') {

            /* En caso de ROL Cliente solo se recogerán de la BBDD las empresas asociadas a 
            los sectores que tenga asociado dicho usuario. */
            $id = $user->getId();
            $em=$this->getDoctrine()->getManager();
            $query = $em->getRepository(Sector::class)->BuscarSectores($id);
        } else {

            /*En caso de ROL ADMIN se recogerán todas las empresas. */
            $em=$this->getDoctrine()->getManager();
            $query = $em->getRepository(Sector::class)->BuscarSectores();
        }

        /* Listamos y paginamos los datos obtenidos de la BBDD. */
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        //Cargamos la vista.
        return $this->render('sector/sector_list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/delete_sector/{id}", name="delete-sector")
     */
    public function Delete($id): Response
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        /* Recuperamos los datos del Sector que queremos eliminar. */
        $em = $this->getDoctrine()->getManager();
        $sector = $em->getRepository(Sector::class)->findOneBy(['id'=>$id]);
        
        /* Eliminamos el Sector. */
        $em=$this->getDoctrine()->getManager();
        $em->remove($sector);
        $em->flush();
        $this->addFlash('success', 'Sector Deleted!');
        
        /* Volvemos al listado de sectores */
        return $this->redirect('/sector_list');
    }

    /**
     * @Route("/predelete_sector/{id}", name="predelete-sector")
     */
    public function PreDelete($id): Response
    {   
        /*Seguridad. Comprobamos Rol de usuario y permitimos acceso a los ADMIN
        en caso contrario retorno a dashboard*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {return $this->redirect('/');}

        /* Recuperamos los datos del Sector que queremos eliminar. */
        $em = $this->getDoctrine()->getManager();
        $sector = $em->getRepository(Sector::class)->findOneBy(['id'=>$id]);

        //Cargamos la vista.
        return $this->render('/sector/sector_delete.html.twig', [
                'id' => $id,
                'empresa' => $sector
        ]);
    }
   
}
