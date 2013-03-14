<?php

/**
 * BuildListener
 *
 * @author Stefan
 */
class BuildListener implements \BuildListener
{
        /**
     * Fired before any targets are started.
     *
     * @param BuildEvent $event The BuildEvent
     */
    public function buildStarted(\BuildEvent $event){

    }

    /**
     * Fired after the last target has finished.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent::getException()
     */
    public function buildFinished(\BuildEvent $event){

    }

    /**
     * Fired when a target is started.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent::getTarget()
     */
    public function targetStarted(\BuildEvent $event){

    }

    /**
     * Fired when a target has finished.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent#getException()
     */
    public function targetFinished(\BuildEvent $event){

    }

    /**
     * Fired when a task is started.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent::getTask()
     */
    public function taskStarted(\BuildEvent $event){

    }

    /**
     * Fired when a task has finished.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent::getException()
     */
    public function taskFinished(\BuildEvent $event){

    }

    /**
     * Fired whenever a message is logged.
     *
     * @param BuildEvent $event The BuildEvent
     * @see BuildEvent::getMessage()
     */
    public function messageLogged(\BuildEvent $event){

    }
}