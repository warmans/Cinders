<?php
namespace Cinders\Api\Resource\Project;

/**
 * List
 *
 * @author warmans
 */
class Find  extends \Cinders\Api\Resource
{
    public function handleGet(array $resource_arguments = array(), \Symfony\Component\HttpFoundation\Request $request)
    {
        $project_list = array();
        foreach ($this->getCinders()->getProjects() as $project) {
            $project_list[] = $project->meta()->project;
        }

        return $this->makeResponse(true, $project_list);
    }
}
