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

use Bldr\Config;
use Bldr\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class InitCommand extends AbstractCommand
{
    /**
     * @var array $gitConfig
     */
    private $gitConfig;

    /**
     * @var array $tasks
     */
    private $tasks = [];

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription("Builds the project config file for a project.")
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the package')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Description of the package')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, "Delete existing config")
            ->addOption('dist', null, InputOption::VALUE_NONE, "Create config .dist file")
            ->addOption(
                'extension',
                'e',
                InputOption::VALUE_REQUIRED,
                "Format for the config (" . implode(', ', Config::$TYPES) . ")",
                Config::$DEFAULT_TYPE
            )
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> builds the config file in the root directory.

To use:

    <info>$ bldr %command.full_name%</info>

To delete the existing file:

    <info>$ bldr %command.full_name% -d</info>
    <info>$ bldr %command.full_name% --delete</info>

To create a dist file:

    <info>$ bldr %command.full_name% --dist</info>

EOF
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $allowed = ['yml' => true, 'json' => true];
        if (!array_key_exists($input->getOption('extension'), $allowed)) {
            throw new \Exception(
                sprintf(
                    "%s is not a valid extension.",
                    $input->getOption('extension')
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');

        $whitelist = ['name', 'description'];
        $options   = array_filter(array_intersect_key($input->getOptions(), array_flip($whitelist)));

        if ($dialog->askConfirmation(
            $output,
            $dialog->getQuestion('Would you like to define your profiles?', 'yes', '?'),
            true
        )
        ) {
            $options['profiles'] = $this->determineProfiles($output);
            foreach ($this->tasks as $taskName => $task) {
                $options['tasks'][$taskName] = array_merge_recursive($task, ['calls' => []]);
            }
        }

        $config = Config::create($input->getOption('extension'), $options, $input->getOption('dist'));

        $output->writeln(
            [
                "",
                "Config file generated.",
                ""
            ]
        );
    }

    /**
     * @param OutputInterface $output
     *
     * @return array
     */
    private function determineProfiles(OutputInterface $output)
    {

        $output->writeln(["", "Defining Profiles", ""]);

        $profiles = [];
        do {
            $result = $this->buildProfile($output);
            if ($result === false) {
                break;
            }
            $profiles[$result[0]] = $result[1];
        } while (true);

        return $profiles;
    }

    /**
     * @param OutputInterface $output
     *
     * @return array|bool
     */
    private function buildProfile(OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');

        $profile = [];

        $name = $dialog->ask($output, $dialog->getQuestion('Profile Name'), null);
        if ($name === null) {
            return false;
        }
        $description = $dialog->ask($output, $dialog->getQuestion('Description'), null);
        if ($description !== null) {
            $profile['description'] = $description;
        }

        $output->writeln(["", "Task Ordering"]);
        $tasks = [];
        do {
            $result = $this->buildTask($output);
            if ($result === null) {
                break;
            }

            $this->tasks[$result[0]] = $result[1];
            $tasks[]                 = $result[0];
        } while (true);

        if (!empty($tasks)) {
            $profile['tasks'] = $tasks;
        }

        return [$name, $profile];
    }

    /**
     * @param OutputInterface $output
     *
     * @return array|bool
     */
    private function buildTask(OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');

        $task = [];

        $taskName = $dialog->ask($output, $dialog->getQuestion('Task name', null), null);
        if ($taskName === null) {
            return null;
        }
        $desc = $dialog->ask($output, $dialog->getQuestion('Task Description', null), null);
        if ($desc !== null) {
            $task['description'] = $desc;
        }

        return [$taskName, $task];
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
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

        $output->writeln("Attempting to create a config file for you. Follow along!");

        $this->getNameOption($input, $output);

        $description = $input->getOption('description') ? : null;
        $input->setOption(
            'description',
            $dialog->ask($output, $dialog->getQuestion('Description', $description), $description)
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function getNameOption(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()
            ->get('dialog');

        if (!$name = $input->getOption('name')) {
            $name = $this->getPackageName();
        }

        $name = $dialog->ask($output, $dialog->getQuestion('Project Name (<vendor>/<name>)', $name), $name);
        $input->setOption('name', $name);
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    private function getPackageName()
    {
        $git = $this->getGitConfig();

        $name = basename(getcwd());
        $name = preg_replace('{(?:([a-z])([A-Z])|([A-Z])([A-Z][a-z]))}', '\\1\\3-\\2\\4', $name);
        $name = strtolower($name);
        if (isset($git['github.user'])) {
            return $git['github.user'] . '/' . $name;
        } elseif (!empty($_SERVER['USERNAME'])) {
            return $_SERVER['USERNAME'] . '/' . $name;
        } elseif (get_current_user()) {
            return get_current_user() . '/' . $name;
        } else {
            return $name . '/' . $name;
        }
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    private function getGitConfig()
    {
        $finder = new ExecutableFinder();
        $gitBin = $finder->find('git');

        $cmd = new Process(sprintf('%s config -l', escapeshellarg($gitBin)));
        $cmd->run();

        if ($cmd->isSuccessful()) {
            $this->gitConfig = array();
            preg_match_all('{^([^=]+)=(.*)$}m', $cmd->getOutput(), $matches, PREG_SET_ORDER);
            if (empty($matches)) {
                return [];
            }

            foreach ($matches as $match) {
                $this->gitConfig[$match[1]] = $match[2];
            }

            return $this->gitConfig;
        }

        return array();
    }
}
