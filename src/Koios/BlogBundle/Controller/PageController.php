<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Koios\BlogBundle\Entity\Enquiry;
use Koios\BlogBundle\Form\EnquiryType;
use Guzzle\Service\Description\ServiceDescription;

class PageController extends Controller
{
    public function indexAction()
    {
	  $client = $this->get('backend_client');
	  $command = $client->getCommand('GetBlogs');
	  $blogs = $client->execute($command);

	  return $this->render('KoiosBlogBundle:Page:index.html.twig', array('blogs' => $blogs));
    }

    public function aboutAction()
    {
        return $this->render('KoiosBlogBundle:Page:about.html.twig');
    }

    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact Enquiry')
                    ->setFrom('enquiries@koios.com')
                    ->setTo($this->container->getParameter('koios_blog.emails.contact_email'))
                    ->setBody($this->renderView('KoiosBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));

                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('blogger-notice', 'Your contact enquiry was successfully sent.  Thank you!');

                return $this->redirect($this->generateUrl('KoiosBlogBundle_contact'));
            }
        }

        return $this->render('KoiosBlogBundle:Page:contact.html.twig', array('form' => $form->createView()));
    }

    public function sidebarAction() {
	  $client = $this->get('backend_client');
	  $command = $client->getCommand('GetComments', array('limit' => $this->container->getParameter('koios_blog.comments.latest_comment_limit')));
	  $comments = $client->execute($command);

	  $command = $client->getCommand('GetTagWeights');
	  $tagWeights = $client->execute($command);

        return $this->render('KoiosBlogBundle:Page:sidebar.html.twig', array(
                'tags'           => $tagWeights,
                'latestComments' => $comments));
	}
}
