<?php

class RoboFile extends Robo\Tasks
{

    function build()
    {
        $this->taskComposerUpdate('./composer.phar')
            ->preferDist()
            ->run();
    }

    function test()
    {
        $this->build();

        $this->taskPhpUnit()
            ->configFile('phpunit.xml')
            ->run();
    }

}