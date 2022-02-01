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
use App\Entity\Entries;
use Doctrine\ORM\Mapping\Entity;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="project")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProjectController.php',
        ]);
    }

    /**
     * @Route("/project/add", name="project_add", methods="POST")
     */
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "name": "My New Project",
            "desc": "description aaaaaa",
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

        if($user->getType() != 0 && $user->getType() != 2) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }

        $request_data = json_decode($request->getContent(), true);
        
        $name = $request_data["name"];
        $desc = $request_data["desc"];
        $groups = $request_data["groups"];
        
        $new_project = new Projects();
        $new_project->setName($name);
        $new_project->setDescription($desc);
        $new_project->setGroups($groups);

        $doctrine_manager = $doctrine->getManager();
        $doctrine_manager->persist($new_project);
        $doctrine_manager->flush();

        return $this->json([
            'message' => "OK",
        ]);
    }

    /**
     * @Route("/project/list", name="project_list")
     */
    public function list(ManagerRegistry $doctrine, Request $request): Response
    {
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

        $user_groups = explode(";", $user->getGroups());
        $project_ids = array();
        $projects = $doctrine->getRepository(Projects::class)->findAll();
        $output_array = array();

        foreach ($projects as &$project) {
            $project_groups = explode(";", $project->getGroups());
            foreach ($project_groups as &$project_group) {
                foreach ($user_groups as &$user_group) {
                    if($project_group == $user_group){
                        if (in_array($project->getId(), $project_ids)) {
                            continue;
                        }
                        $last_user_array = array();
                        $entries = $project->getEntries();
                        $entries_array = array();

                        foreach ($entries as &$entrie) {
                            $e_user = $entrie->getUser();
                            $type = "start";
                            if(!isset($last_user_array[$e_user->getId()])){
                                $last_user_array[$e_user->getId()] = 1;
                                $type = "start";
                            }else{
                                if($last_user_array[$e_user->getId()] == 1){
                                    $last_user_array[$e_user->getId()] = 0;
                                    $type = "stop";
                                }else{
                                    $last_user_array[$e_user->getId()] = 1;
                                    $type = "start";
                                }
                            }

                            array_push($entries_array, [
                                "user" => $e_user->getUsername(),
                                "type" => $type,
                                "date" => $entrie->getDatetime()
                            ]);
                        }

                        array_push($output_array, [
                            "id" => $project->getId(),
                            "name" => $project->getName(),
                            "desc" => $project->getDescription(),
                            "entries" => $entries_array
                        ]);

                        array_push($project_ids,  $project->getId());
                    }
                }
            }
        }

        return $this->json([
            'message' => "OK",
            'projects' => $output_array
        ]);
    }

    /**
     * @Route("/project/entrie", name="entrie_list", methods="POST")
     */
    public function entrie(ManagerRegistry $doctrine, Request $request): Response
    {
        /*
        {
            "project_id": 1,
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

        if($user->getType() == 1) {
            return $this->json([
                'message' => "Invalid permissions",
            ]);
        }

        $request_data = json_decode($request->getContent(), true);
        $project_id = $request_data["project_id"];

        $project = $doctrine->getRepository(Projects::class)->find($project_id);
        if(!$project) {
            return $this->json([
                'message' => "Project not found",
            ]);
        }

        $new_entry = new Entries();
        $new_entry->setUser($user);
        $new_entry->setProject($project);
        $new_entry->setDatetime(new \DateTime(date("Y-m-d H:i:s")));

        $doctrine_manager = $doctrine->getManager();
        $doctrine_manager->persist($new_entry);
        $doctrine_manager->flush();

        return $this->json([
            'message' => "OK"
        ]);
    }
}
