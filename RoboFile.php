<?php

class RoboFile extends Robo\Tasks
{

    public function install()
    {
        $this->taskComposerUpdate('./composer.phar')
            ->preferDist()
            ->run();
    }

    public function test()
    {
        $this->install();

        $this->taskPhpUnit()
            ->configFile('phpunit.xml')
            ->run();
    }

}