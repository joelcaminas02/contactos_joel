<?php

namespace App\Controller;
use App\Entity\Contacto;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactoController extends AbstractController
{
    private $contactos = [

        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],

        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],

        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],

        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],

        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]

    ];  
       
    /**
     * @Route("/contacto/{codigo}", name="ficha_contacto")
     */

    // public function ficha_contacto($codigo) : Response{
      //  $resultado = ($this->contactos[$codigo] ?? null);

       // return $this-> render('/page/ficha_contacto.html.twig', ['contacto' => $resultado]);
     //}

     /**
     * @Route("/contacto/buscar/{texto}", name="texto")
     */

     public function texto($texto) : Response{
        $resultados = array_filter($this->contactos,function($contacto) use ($texto){
            return strpos($contacto["nombre"], $texto) !== FALSE;
        });

        return $this-> render('page/lista_contacto.html.twig', ['contactos' => $resultados]);
     }

    /**
    *@Route("contacto/insertar", name="insertar_contacto") 
    */
     public function insertar(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();
        foreach ($this->contactos as $c) {
            $contacto = new Contacto();
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);
        }

        try{
            $entityManager->flush();
            return new Response("Contactos insertados");
        }catch(Exception $e){
            return new Response("Error insertando objetos");
        }
     }
     /**
     * @Route("/contacto/buscar2/{codigo}", name="buscar_contacto")
     */
    public function buscar_contacto(ManagerRegistry $doctrine,$codigo): Response{
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('page/ficha_contacto.html.twig', ['contacto' => $contacto]);
    }
    
    /**
     * @Route("/contacto/buscar3/{texto}", name="buscar_contacto2")
     */
    public function buscar_contacto2(ManagerRegistry $doctrine,$texto): Response{
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->findByName($texto);

        return $this->render('page/ficha_contacto.html.twig', ['contacto' => $contacto]);
    }

    //Actualizar
    /**
     * @Route("/contacto/update/{id}/{texto}", name="buscar_contacto2")
     */
    public function update(ManagerRegistry $doctrine, $id, $texto) : Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
            if($contacto){
                $contacto->setNombre($texto);

                try{
                    $entityManager->flush();
                    return $this->render('page/ficha_contacto.html.twig', ['contacto' => $contacto]);
                } catch (\Exception $e){
                    return new Response ("error inseperado");
                }
            }else{
                return $this->render('page/ficha_contacto.html.twig', ['contacto' => null]);
            }
    }

    //Borrar
    /**
     * @Route("/contacto/borrar/{id}/{texto}", name="buscar_contacto2")
     */
    public function borrar(ManagerRegistry $doctrine, $id, $texto) : Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
            if($contacto){
                try{
                    $entityManager->remove($texto);
                    $entityManager->flush();
                    return $this->render('Contacto eliminado');
                } catch (\Exception $e){
                    return new Response ("error inseperado");
                }
            }else{
                return $this->render('page/ficha_contacto.html.twig', ['contacto' => null]);
            }
    }
}
