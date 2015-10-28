<?php

namespace Bajke\BookBundle\AuthProviders;

use Bajke\BookBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;

class OAuthProvider extends OAuthUserProvider {

    protected $session, $doctrine, $admins;

    public function __construct($session, $doctrine, $service_container){
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    public function loadUserByUsername($username){
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('BookBundle:User', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $username)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        if(count($result)){
            return $result[0];
        } else {
            return new User();
        }
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response){
        $google_id = $response->getUsername();
        $email = $response->getEmail();
        $nickname = $response->getNickname();
        $realname = $response->getRealName();
        $avatar = $response->getProfilePicture();

        $this->session->set('email', $email);
        $this->session->set('nickname', $nickname);
        $this->session->set('realname', $realname);
        $this->session->set('avatar', $avatar);

        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('BookBundle:User', 'u')
            ->where('u.googleId = :gid')
            ->setParameter('gid', $google_id)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        if(!count($result)){
            $user = new User();
            $user->setUsername($google_id);
            $user->setRealname($realname);
            $user->setNickname($nickname);
            $user->setEmail($email);
            $user->setGoogleId($google_id);

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword(md5(uniqid(), $user->getSalt()));
            $user->setPassword($password);

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result[0];
        }

        $this->session->set('id', $user->getId());

        return $this->loadUserByUsername($response->getUsername());
    }

    public function supportsClass($class){
        return $class === 'Bajke\\BookBundle\\Entity\\User';
    }

}