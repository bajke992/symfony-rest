<?php

namespace Bajke\BookBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Base {

    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction(){
        $user = $this->checkUser();

        return array('user' => $user);
    }
}
