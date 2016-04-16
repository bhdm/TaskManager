<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Project;
use ProjectBundle\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function projectListAction()
    {
        $projects = $this->getDoctrine()->getRepository('ProjectBundle:Project')->findBy(['enabled' => true]);
        return $this->render('@Project/Project/project_list.html.twig',['projects' => $projects]);
    }


    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $project = new Project();
        $form = $this->createForm($project, ProjectType::class);
        $form->add('submit', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $formData = $form->handleRequest($request);
        if ($formData->isValid()){
            $project = $formData->getData();

            $file = $project->getFile();
            $filename = time(). '.'.$file->guessExtension();
            $file->move(
                __DIR__.'/../../../web/upload/images/',
                $filename
            );
            $project->setFile(['path' => '/upload/images/'.$filename,'filename' => $filename ]);

            $em->persist($project);
            $em->flush();
            return $this->redirect($this->generateUrl('project'));
        }
    }
}
