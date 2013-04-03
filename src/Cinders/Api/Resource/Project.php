<?php
namespace Cinders\Api\Resource;

class Project extends \Cinders\Api\Resource
{
    public function claimPathSegments()
    {
        return array('name'=>'#[^/]+#i');
    }

    public function handleGet(array $resource_arguments = array(), \Symfony\Component\HttpFoundation\Request $request)
    {
        $project_list = array();
        foreach ($this->getCinders()->getProjects() as $project) {
            if($project->meta()->project->name == $resource_arguments['name']){
                return $this->makeResponse(true, $project->meta()->project);
            }
        }

        return $this->makeResponse(false, null, 'Project not found');
    }

}
