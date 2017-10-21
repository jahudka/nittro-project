<?php

declare(strict_types=1);

namespace App\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Finder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;


class AppExtension extends CompilerExtension {

    public function loadConfiguration() : void {
        $builder = $this->getContainerBuilder();

        foreach ($this->findFactoryInterfaces() as $interface) {
            if (!$builder->getByType($interface)) {
                $builder->addDefinition($this->allocateName($builder, $interface))
                    ->setImplement($interface);
            }
        }

        foreach ($this->findCommands() as $class) {
            if (!$builder->getByType($class)) {
                $builder->addDefinition($this->allocateName($builder, $class))
                    ->setType($class)
                    ->setAutowired(false)
                    ->addTag('kdyby.console.command');
            }
        }
    }

    private function allocateName(ContainerBuilder $builder, string $class) : string {
        return count($builder->getDefinitions()) . '.' . preg_replace('#\W+#', '_', $class);
    }

    private function findFactoryInterfaces() : array {
        /** @var \RecursiveDirectoryIterator[] $modules */
        $modules = Finder::findDirectories('*Module/Factories')
            ->from(__DIR__ . '/../');

        $loader = new RobotLoader();

        foreach ($modules as $module) {
            $loader->addDirectory($module->getPathname());
        }

        $loader->rebuild();
        $interfaces = [];

        foreach ($loader->getIndexedClasses() as $class => $file) {
            $reflection = new \ReflectionClass($class);

            if ($reflection->isInterface() && count($reflection->getMethods()) === 1 && $reflection->hasMethod('create')) {
                $interfaces[] = $class;
            }
        }

        return $interfaces;
    }

    private function findCommands() : array {
        /** @var \RecursiveDirectoryIterator[] $modules */
        $modules = Finder::findDirectories('*Module/Commands')
            ->from(__DIR__ . '/../');

        $loader = new RobotLoader();

        foreach ($modules as $module) {
            $loader->addDirectory($module->getPathname());
        }

        $loader->rebuild();
        $commands = [];

        foreach ($loader->getIndexedClasses() as $class => $file) {
            $reflection = new \ReflectionClass($class);

            if ($reflection->isSubclassOf(Command::class) && !$reflection->isAbstract()) {
                $commands[] = $class;
            }
        }

        return $commands;
    }

}
