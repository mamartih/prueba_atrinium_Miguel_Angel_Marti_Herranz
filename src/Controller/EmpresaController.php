<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Entity\User;
use App\Form\EditEmpresaType;
use App\Form\EmpresaType;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EmpresaController extends AbstractController
{
    /**
     * @Route("/empresa", name="empresa")
     */
    public function index(Request $request): Response
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}

        /* Creamos el formulario de Empresa.  */
        $empresa = new Empresa();
        $form = $this->createForm(EmpresaType::class,$empresa);
        $form->handleRequest($request);

        /*Si enviamos el formulario, y es valido creamos nueva Empresa
        y retornamos al listado de Empresas*/
        if ($form->isSubmitted()) {
            if ($form->get('save')->isClicked()&&$form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($empresa);
            $em->flush();
            return $this->redirect('/empresa_list');
            }
            if ($form->get('cancel')->isClicked()) { 
                return $this->redirect('/empresa_list');   
            }
        }
        

        //Cargamos el formulario de creación de sector.
        return $this->render('empresa/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit_empresa/{id}", name="edit-empresa")
     */
    public function Edit(Empresa $empresa2, Request $request, $id): Response
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}

        //Obtenemos los datos de la empresa que vamos a editar.
        $em = $this->getDoctrine()->getManager();
        $empresa = $em->getRepository(Empresa::class)->findOneBy(['id'=>$id]);
        
        //Creamos el formulario a partir de los datos encontrados.
        $form = $this->createForm(EditEmpresaType::class, $empresa);
        $form->handleRequest($request);

        /* Si enviamos el formulario y es valido, actualizamos el registro
        y retornamos al listado de empresas. */
        if ($form->isSubmitted()) {
            if ($form->get('update')->isClicked()&&$form->isValid()) {
             $em=$this->getDoctrine()->getManager();
             $em->persist($empresa);
             $em->flush();
             $this->addFlash('success', 'Empresa Updated!');
             return $this->redirect('/empresa_list');
            }
            if ($form->get('cancel')->isClicked()) { 
                return $this->redirect('/empresa_list');   
            } 
        }
        
        //Cargamos el formulario de edición de Empresa.
        return $this->render('empresa/empresa_edit.html.twig', [
            'empresa' => $empresa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/empresa_list", name="empresa-list")
     */
    public function ListadoEmpresas(PaginatorInterface $paginator, Request $request)
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}

        /*Seguridad. Comprobamos Rol de usuario y definimos el acceso a los datos
        en función de dicho Rol*/
        $role = $user->getRoles();
        if ($role[0]!= 'ROLE_ADMIN') {
            /**En caso de ROL Cliente solo se recogerán de la BBDD las empresas asociadas a 
            los sectores que tenga asociado dicho usuario. */
            $em=$this->getDoctrine()->getManager();
            $query = $em->getRepository(Empresa::class)->BuscarEmpresas($user->getId());
        } else {
            /*En caso de ROL ADMIN se recogerán todas las empresas. */
            $em=$this->getDoctrine()->getManager();
            $query = $em->getRepository(Empresa::class)->BuscarEmpresas();
        }

         /* Listamos y paginamos los datos obtenidos de la BBDD. */
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        //Cargamos la vista.
        return $this->render('empresa/empresa_list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/delete_empresa/{id}", name="delete-empresa")
     */
    public function Delete(Empresa $empresa2, Request $request, $id): Response
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}
        
        /* Recuperamos los datos de la Empresa que queremos eliminar. */
        $em = $this->getDoctrine()->getManager();
        $empresa = $em->getRepository(Empresa::class)->findOneBy(['id'=>$id]);
        
        /* Eliminamos la Empresa. */
        $em=$this->getDoctrine()->getManager();
        $em->remove($empresa);
        $em->flush();
        $this->addFlash('success', 'Empresa Deleted!');
        
        /* Volvemos al listado de Empresas */
        return $this->redirect('/empresa_list');
    }

    /**
     * @Route("/predelete_empresa/{id}", name="predelete-empresa")
     */
    public function PreDelete(Empresa $empresa2, Request $request, $id): Response
    {   
        /*Seguridad. Permitimos acceso a los usuarios
        logueados, en caso contrario redirección a Login*/
        $user = $this->getUser();
        if ($user==null) {return $this->redirect('/login');}

        /* Recuperamos los datos de la Empresa que queremos eliminar. */
        $em = $this->getDoctrine()->getManager();
        $empresa = $em->getRepository(Empresa::class)->findOneBy(['id'=>$id]);

        // Cargamos la vista.
        return $this->render('/empresa/empresa_delete.html.twig', [
                'id' => $id,
                'empresa' => $empresa
        ]);
    }


}
