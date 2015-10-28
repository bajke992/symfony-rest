<?php

namespace Bajke\BookBundle\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Base extends Controller{

    protected function serialize($data, $format = 'json'){
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->container->get('jms_serializer')
            ->serialize($data, $format, $context);
    }

    protected function createApiResponse($data, $code = 200){
        $json = $this->serialize($data);

        return new Response($json, $code, array(
            'Content-Type' => 'application/json'
        ));
    }

    protected function checkUser(){
        $repo = $this->getDoctrine()->getRepository('BookBundle:User');

        if($this->getUser()) {
            $tmp = $this->getUser();
            $user = $repo->findOneBy(array('id' => $tmp->getId()));
        } else {
            $user = null;
        }

        return $user;
    }
}