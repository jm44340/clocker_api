<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Users;
use App\Entity\Projects;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    /**
     * @Route("/user/login", name="user_login", methods="POST")
     */
    public function login(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "username": "user",
            "password": "password"
        }
        */
        $session = new Session();
        $request_data = json_decode($request->getContent(), true);
        
        $username = $request_data["username"] ?? "";
        $password = $request_data["password"] ?? "";

        $password_sha = hash("sha256", $password);

        $user = $doctrine->getRepository(Users::class)->findOneBy(['username' => $username]);

        if(!$user) {
            return $this->json([
                'message' => "User not found",
            ]);
        }

        if($password_sha != $user->getPassword()){
            return $this->json([
                'message' => "Wrong password",
            ]);
        }

        $session->set("user_id", $user->getId());

        return $this->json([
            'message' => "OK",
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'type' => $user->getType(),
        ]);
    }

    /**
     * @Route("/user/add", name="user_add", methods="POST")
     */
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "username": "New user",
            "password": "User password",
            "groups": "aaa;bbb"
        }
        */
        $session = new Session();
        $id = $session->get("user_id");

        if(!$id) {
            return $this->json([
                'message' => "Not logged in",
            ]);
        }

        $user = $doctrine->getRepository(Users::class)->find($id);

        if(!$user) {
            return $this->json([
                'message' => "User not found",
            ]);
        }

        if($user->getType() != 2) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }

        $request_data = json_decode($request->getContent(), true);
        
        $username = $request_data["username"];
        $password = $request_data["password"];
        $groups = $request_data["groups"];

        $password_sha = hash("sha256", $password);
        
        $new_user = new Users();
        $new_user->setUsername($username);
        $new_user->setPassword($password_sha);
        $new_user->setGroups($groups);
        $new_user->setType(0);

        $doctrine_manager = $doctrine->getManager();
        $doctrine_manager->persist($new_user);
        $doctrine_manager->flush();

        return $this->json([
            'message' => "OK",
        ]);
    }


    /**
     * @Route("/user/edit", name="user_edit", methods="POST")
     */
    public function edit(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "user_id": 2,
            "username": "test_user",
            "password": "",
            "groups": "aaa;bbb",
            "type": 0
        }
        */
        $session = new Session();
        $id = $session->get("user_id");

        if(!$id) {
            return $this->json([
                'message' => "Not logged in",
            ]);
        }

        $user = $doctrine->getRepository(Users::class)->find($id);

        if(!$user) {
            return $this->json([
                'message' => "User not found",
            ]);
        }

        if($user->getType() != 2) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }

        $request_data = json_decode($request->getContent(), true);
        
        $user_id = $request_data["user_id"];
        $username = $request_data["username"];
        $password = $request_data["password"];
        $groups = $request_data["groups"];
        $type = $request_data["type"];

        $password_sha = hash("sha256", $password);
        
        $edited_user = $doctrine->getRepository(Users::class)->find($user_id);
        if(!$edited_user) {
            return $this->json([
                'message' => "Edited user not found",
            ]);
        }

        $edited_user->setUsername($username);
        if($password != ""){
            $edited_user->setPassword($password_sha);
        }
        $edited_user->setGroups($groups);
        $edited_user->setType($type);

        $doctrine_manager = $doctrine->getManager();
        $doctrine_manager->flush();

        return $this->json([
            'message' => "OK",
        ]);
    }
    
    /**
     * @Route("/user/delete", name="user_delete", methods="POST")
     */
    public function delete(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "user_id": 2,
        }
        */
        $session = new Session();
        $id = $session->get("user_id");

        if(!$id) {
            return $this->json([
                'message' => "Not logged in",
            ]);
        }

        $user = $doctrine->getRepository(Users::class)->find($id);

        if(!$user) {
            return $this->json([
                'message' => "User not found",
            ]);
        }

        if($user->getType() != 2) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }

        $request_data = json_decode($request->getContent(), true);
        
        $user_id = $request_data["user_id"];
        $edited_user = $doctrine->getRepository(Users::class)->find($user_id);
        if(!$edited_user) {
            return $this->json([
                'message' => "Edited user not found",
            ]);
        }

        $doctrine_manager = $doctrine->getManager();
        $doctrine_manager->remove($edited_user);
        $doctrine_manager->flush();

        return $this->json([
            'message' => "OK",
        ]);
    }

        /**
     * @Route("/user/list", name="user_list")
     */
    public function list(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "user_id": 2,
        }
        */
        $session = new Session();
        $id = $session->get("user_id");

        if(!$id) {
            return $this->json([
                'message' => "Not logged in",
            ]);
        }

        $user = $doctrine->getRepository(Users::class)->find($id);

        if(!$user) {
            return $this->json([
                'message' => "User not found",
            ]);
        }

        if($user->getType() != 2) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }


        $users = $doctrine->getRepository(Users::class)->findAll();

        $output_array = array();
        foreach ($users as &$user) {
            array_push($output_array, [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "groups" => $user->getGroups(),
                "type" => $user->getType()
            ]);
        }

        return $this->json([
            'message' => "OK",
            'users' => $output_array
        ]);
    }

}
