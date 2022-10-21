<?php

namespace App\Controller;
use App\Entity\Contacto;
use App\Form\ContactoType;
use Doctrine\DBAL\Types\TextType as TypesTextType;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
     * @Route("/contacto/borrar/{id}/{texto}", name="borrar")
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

    //Esta nueva funcion nos va a permitir crear un formulario para crear nuevos usuarios.
    //Este formulario solo envia datos en forma de POST es decir que no inserta nada
    /**
     * @Route("/contacto/nuevo", name="nuevo_contacto")
     */
    public function nuevo(){
        $contacto = new Contacto();

        //creamos la variable $formulario en la cual almacenaremos este objeto, es decir el usuario que est
        //amos creando, con la funcion createFormBuilder y de parametro del objeto que queremos crear.
       
        $formulario = $this->createFormBuilder($contacto)

        //con add, pasamos el nombre de la variable y el tipo que es en el formulario,(textType es bloque de texto)
        //y asi el nombre que introducimos ahi será el valor que tendra nombre para ese objeto
       
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class)

        //aqui es lo mismo solo que añadimos la fucnion array, que nos permite modificar el nombre de la etiqueta 
        //asi no sale el nombre de la variable, esto es solo algo visual.

            ->add('email', TextType::class, array('label' => 'Correo electronico'))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
        return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }




    //este formulatio insertará los datos en la base de datos.
    /**
     * @Route("/contacto/nuevo2", name="nuevo_contacto")
     */
    public function nuevo2(ManagerRegistry $doctrine, Request $request){
        $contacto = new Contacto();

        //creamos la variable $formulario en la cual almacenaremos este objeto, es decir el usuario que est
        //amos creando, con la funcion createFormBuilder y de parametro del objeto que queremos crear.
       
        $formulario = $this->createFormBuilder($contacto)

        //con add, pasamos el nombre de la variable y el tipo que es en el formulario,(textType es bloque de texto)
        //y asi el nombre que introducimos ahi será el valor que tendra nombre para ese objeto
       
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class)

        //aqui es lo mismo solo que añadimos la fucnion array, que nos permite modificar el nombre de la etiqueta 
        //asi no sale el nombre de la variable, esto es solo algo visual.

            ->add('email', TextType::class, array('label' => 'Correo electronico'))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            // En primer lugar, el controlador recibirá un objeto Request, 
            //que contendrá los datos del formulario enviado (en el caso de que se haya enviado): 
            $formulario->handleRequest($request);
        

            if($formulario->isSubmitted() && $formulario->isValid()){
                $contacto = $formulario->getData();
                //conecta con la base de datos
                $entityManager = $doctrine->getManager();
                //persist inserta los datos en la tabla de la bases de datos
                $entityManager->persist($contacto);
                //fluch guarda los cambios insertados en la tabla
                $entityManager->flush();
                //te redirige a una nueva ruta, le paso la funcion buscar_contacto y el campo codigo buscando la id del contacto insertado
                return $this->redirectToRoute('buscar_contacto',['codigo' => $contacto->getId()]);
            }
        
        return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }



    //editar con el formulario
    /**
     * @Route("/contacto/editarForm/{%codigo}", name="editarForm", requirements={"$codigo"="\d+"})
     */
    public function editarForm(ManagerRegistry $doctrine, Request $request, $codigo){
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto= $repositorio->find($codigo);

        //creamos la variable $formulario en la cual almacenaremos este objeto, es decir el usuario que est
        //amos creando, con la funcion createFormBuilder y de parametro del objeto que queremos crear.
       
        $formulario = $this->createForm(ContactoType::class, $contacto);

            // En primer lugar, el controlador recibirá un objeto Request, 
            //que contendrá los datos del formulario enviado (en el caso de que se haya enviado): 
            $formulario->handleRequest($request);
        

            if($formulario->isSubmitted() && $formulario->isValid()){
                $contacto = $formulario->getData();
                //conecta con la base de datos
                $entityManager = $doctrine->getManager();
                //persist inserta los datos en la tabla de la bases de datos
                $entityManager->persist($contacto);
                //fluch guarda los cambios insertados en la tabla
                $entityManager->flush();
                //te redirige a una nueva ruta, le paso la funcion buscar_contacto y el campo codigo buscando la id del contacto insertado
                return $this->redirectToRoute('buscar_contacto',['codigo' => $contacto->getId()]);
            }
        
        return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }



    //este formulatio insertará los datos en la base de datos.
    /**
     * @Route("/contacto/nuevo3", name="nuevo3")
     */
    public function nuevo3(ManagerRegistry $doctrine, Request $request){
        $contacto = new Contacto();

        //creamos la variable $formulario en la cual almacenaremos este objeto, es decir el usuario que est
        //amos creando, con la funcion createFormBuilder y de parametro del objeto que queremos crear.
       
        $formulario = $this->createForm(ContactoType::class, $contacto);
            // En primer lugar, el controlador recibirá un objeto Request, 
            //que contendrá los datos del formulario enviado (en el caso de que se haya enviado): 
            $formulario->handleRequest($request);
        

            if($formulario->isSubmitted() && $formulario->isValid()){
                $contacto = $formulario->getData();
                //conecta con la base de datos
                $entityManager = $doctrine->getManager();
                //persist inserta los datos en la tabla de la bases de datos
                $entityManager->persist($contacto);
                //fluch guarda los cambios insertados en la tabla
                $entityManager->flush();
                //te redirige a una nueva ruta, le paso la funcion buscar_contacto y el campo codigo buscando la id del contacto insertado
                return $this->redirectToRoute('buscar_contacto',['codigo' => $contacto->getId()]);
            }
        
        return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }
}
