<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Command;

use Bldr\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class InitCommand extends Command
{
    /**
     * @var array $gitConfig
     */
    private $gitConfig;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription("Builds the project .bldr.yml file for a project.")
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the package')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Description of the package')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'Delete existing .bldr.yml')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> builds the .bldr.yml file in the root directory.

To use:

    <info>$ bldr %command.full_name% </info>

EOF
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $whitelist = ['name', 'description'];
        $options   = array_filter(array_intersect_key($input->getOptions(), array_flip($whitelist)));

        file_put_contents(getcwd() . '/.bldr.yml', Yaml::dump($options));
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dir = getcwd();
        if (file_exists($dir . '/.bldr.yml')) {
            if (!$input->getOption('delete')) {
                throw new \Exception(
                    "You already have a .bldr.yml file. Delete it first or run with the -d flag."
                );
            }

            unlink($dir . '/.bldr.yml');
        }

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $output->writeln(
            [
                '',
                $formatter->formatBlock('Welcome to the Bldr.io config generator', 'bg=blue;fg=white', true),
                ''
            ]
        );

        $output->writeln("Attempting to create a .bldr.yml file for you. Follow along!");

        $this->getNameOption($output, $input);

        $description = $input->getOption('description') ? : false;
        $input->setOption(
            'description',
            $dialog->ask($output, $dialog->getQuestion('Description', $description), $description)
        );
    }

    private function getNameOption(OutputInterface $output, InputInterface $input)
    {
        $git = $this->getGitConfig();
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()
            ->get('dialog');

        if (!$name = $input->getOption('name')) {
            $name = basename(getcwd());
            $name = preg_replace('{(?:([a-z])([A-Z])|([A-Z])([A-Z][a-z]))}', '\\1\\3-\\2\\4', $name);
            $name = strtolower($name);
            if (isset($git['github.user'])) {
                $name = $git['github.user'] . '/' . $name;
            } elseif (!empty($_SERVER['USERNAME'])) {
                $name = $_SERVER['USERNAME'] . '/' . $name;
            } elseif (get_current_user()) {
                $name = get_current_user() . '/' . $name;
            } else {
                // package names must be in the format foo/bar
                $name = $name . '/' . $name;
            }
        }

        $name = $dialog->ask($output, $dialog->getQuestion('Project Name (<vendor>/<name>)', $name), $name);
        $input->setOption('name', $name);
    }

    protected function getGitConfig()
    {
        if (null !== $this->gitConfig) {
            return $this->gitConfig;
        }

        $finder = new ExecutableFinder();
        $gitBin = $finder->find('git');

        $cmd = new Process(sprintf('%s config -l', escapeshellarg($gitBin)));
        $cmd->run();

        if ($cmd->isSuccessful()) {
            $this->gitConfig = array();
            preg_match_all('{^([^=]+)=(.*)$}m', $cmd->getOutput(), $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $this->gitConfig[$match[1]] = $match[2];
            }

            return $this->gitConfig;
        }

        return $this->gitConfig = array();
    }
}

