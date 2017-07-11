<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class CategoryController extends Controller {
  /**
   * @Route("/categories", name="categories_list")
   */
  public function indexAction(Request $request) {
    // Get all category data from database.
    $categories = $this->getDoctrine()
      ->getRepository('AppBundle:Category')
      ->findAll();

    // Render template, pass variable.
    return $this->render('category/index.html.twig', [
      'categories' => $categories,
    ]);
  }

  /**
   * @Route("/category/create", name="categories_create")
   */
  public function createAction(Request $request) {
    $category = new Category();

    // Build form
    $form = $this->createFormBuilder($category)
      ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
      ->add('save', SubmitType::class, array('label' => 'Create category', 'attr' => array('class' => 'btn btn-primary')))
      ->getForm();

    // Handle submission request
    $form->handleRequest($request);

    // Submission check and write to database
    if($form->isSubmitted() && $form->isValid()) {

      // Get name that was submited
      $name = $form['name']->getData();

      // Get current date and time
      $now = new \DateTime("now");

      $category->setName($name);
      $category->setCreateDate($now);

      // Data to database.
      $em = $this->getDoctrine()->getManager();
      $em->persist($category);
      $em->flush();

      // Feedback message
      $this->addFlash(
        'notice',
        'Category saved'
      );

      // Use route name to redirect
      return $this->redirectToRoute('categories_list');
    }

    // Render template, pass variable.
    return $this->render('category/create.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/category/edit/{id}", name="categories_edit")
   */
  public function editAction($id, Request $request) {
    // Find category by the id of the url
    $category = $this->getDoctrine()
    ->getRepository('AppBundle:Category')
    ->find($id);

    if(!$category) {
      throw $this->createNotFoundException(
        'No category found for id ' . $id
      );
    }

    $category->setName($category->getName());

    // Build form
    $form = $this->createFormBuilder($category)
      ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
      ->add('save', SubmitType::class, array('label' => 'Update category', 'attr' => array('class' => 'btn btn-primary')))
      ->getForm();

    // Handle submission request
    $form->handleRequest($request);

    // Submission check and write to database
    if($form->isSubmitted() && $form->isValid()) {

      // Get name that was submited
      $name = $form['name']->getData();


      // Data to database.
      $em = $this->getDoctrine()->getManager();
      $category = $em->getRepository('AppBundle:Category')->find($id);

      $category->setName($name);
      $em->flush();

      // Feedback message
      $this->addFlash(
        'notice',
        'Category updated'
      );

      // Use route name to redirect
      return $this->redirectToRoute('categories_list');
    }

    // Render template, pass variable.
    return $this->render('category/edit.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/category/delete/{id}", name="categories_delete")
   */
  public function deleteAction($id) {
    $em = $this->getDoctrine()->getManager();
    $category = $em->getRepository('AppBundle:Category')->find($id);

    if(!$category) {
      throw $this->createNotFoundException(
        'No category found with id of ' . $id
      );


  }
    $em->remove($category);
    $em->flush();

    $this->addFlash(
      'notice',
      'Category deleted'
    );

    return $this->redirectToRoute('categories_list');
  }
}
