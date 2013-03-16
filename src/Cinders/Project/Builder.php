<?php
namespace Cinders\Project;

/**
 * Builder
 *
 * @author Stefan
 */
abstract class Builder
{
    abstract public function build(\Cinders\Project $project, \Cinders\Project\Build $build);
}