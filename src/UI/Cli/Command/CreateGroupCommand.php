<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Cli\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;

class CreateGroupCommand extends ConsoleCommand {

    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setName('zentlix_user:create:group')
            ->setDescription('Given a title, code, group role, sort generates a new user group.')
            ->addArgument('title', InputArgument::REQUIRED, 'User group title');
    }

    /** @throws \Exception */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new CreateCommand();

        $command->title = $input->getArgument('title');

        $helper = $this->getHelper('question');

        $question = new Question('Group code:');
        $command->code = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion('Please, select group role:', ['ROLE_USER', 'ROLE_ADMIN'], 0);
        $question->setErrorMessage('Group role %s is invalid.');
        $command->group_role = $helper->ask($input, $output, $question);

        $question = new Question('Group sort:');
        $command->sort = (int) $helper->ask($input, $output, $question);

        $this->commandBus->handle($command);

        $output->writeln('<info>User group was created: </info>');
        $output->writeln('');
        $output->writeln("Title: $command->title");
        $output->writeln("Code: $command->code");
        $output->writeln("Role: $command->code");
        $output->writeln("Sort: $command->sort");
    }
}