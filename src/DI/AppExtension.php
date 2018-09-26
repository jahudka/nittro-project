<?php

declare(strict_types=1);

namespace App\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;


class AppExtension extends CompilerExtension {

    private $srcDir;


    public function __construct(string $srcDir) {
        $this->srcDir = $srcDir;
    }


    public function loadConfiguration() : void {
        $builder = $this->getContainerBuilder();

        foreach ($this->findFactories() as $factory) {
            if (!$builder->getByType($factory)) {
                $builder->addDefinition($this->allocateName($builder, $factory))
                    ->setImplement($factory);
            }
        }

        foreach ($this->findCommands() as $command) {
            if (!$builder->getByType($command)) {
                $builder->addDefinition($this->allocateName($builder, $command))
                    ->setType($command)
                    ->setAutowired(false)
                    ->addTag('kdyby.console.command');
            }
        }
    }

    private function allocateName(ContainerBuilder $builder, string $class) : string {
        return count($builder->getDefinitions()) . '.' . preg_replace('#\W+#', '_', $class);
    }

    private function findFactories() : array {
        $loader = $this->createNamespaceLoader('Components');
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
        $loader = $this->createNamespaceLoader('Commands');
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

    private function createNamespaceLoader(string $namespace) : RobotLoader {
        /** @var \RecursiveDirectoryIterator[] $modules */
        $modules = Finder::findDirectories('*Module')->in($this->srcDir);
        $loader = new RobotLoader();

        foreach ($modules as $module) {
            if (is_dir($module->getPathname() . '/' . $namespace)) {
                $loader->addDirectory($module->getPathname() . '/' . $namespace);
            }
        }

        if (is_dir($this->srcDir . '/' . $namespace)) {
            $loader->addDirectory($this->srcDir . '/' . $namespace);
        }

        return $loader;
    }
}
