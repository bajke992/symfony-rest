<?php

namespace Bajke\BookBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BookController extends Base {

    /**
     * @Route("/book", name="book_list")
     * @Template()
     */
    public function listAction(){
        $user = $this->checkUser();
        if(!$user){ return new RedirectResponse($this->generateUrl('index')); }

        $books = $user->getBooks();

        return array('books' => $books, 'user' => $user);
    }

    /**
     * @Route("/book/create", name="book_create")
     * @Template("BookBundle:Default:_form.html.twig")
     */
    public function createAction(Request $request){
        $user = $this->checkUser();
        if(!$user){ return new RedirectResponse($this->generateUrl('index')); }

        $em = $this->getDoctrine()->getManager();
        $book = new Book();

        $form = $this->createForm(new BookType(), $book, array('is_owner_disabled' => true));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $book->setTitle($data->getTitle());
            $book->setDescription($data->getDescription());
            $book->setOwner($user->getId());

            $em->persist($book);
            $em->flush();

            return new RedirectResponse($this->generateUrl('book_list'));
        }

        return array('create' => true, 'book' => $book, 'user' => $user, 'form' => $form->createView());
    }

    /**
     * @Route("/book/update")
     * @Template("BookBundle:Default:_form.html.twig")
     */
    public function updateAction(Request $request){
        $user = $this->checkUser();
        if(!$user){ return new RedirectResponse($this->generateUrl('index')); }

        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $book = $em->getRepository('BookBundle:Book')->find($id);

        if(!$book){
            throw $this->createNotFoundException('No Book fount for id: ' . $id);
        }

        $form = $this->createForm(new BookType(), $book, array('is_edit' => true, 'is_owner_disabled' => true));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $book->setTitle($data->getTitle());
            $book->setDescription($data->getDescription());

            $em->flush();

            return new RedirectResponse($this->generateUrl('book_list'));
        }

        return array('create' => false, 'book' => $book, 'user' => $user, 'form' => $form->createView());
    }

    /**
     * @Route("/book/delete")
     *
     */
    public function deleteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $book = $em->getRepository('BookBundle:Book')->find($id);

        if(!$book){
            throw $this->createNotFoundException('No Book fount for id: ' . $id);
        }

        $em->remove($book);
        $em->flush();

        return new RedirectResponse($this->generateUrl('book_list'));
    }

}