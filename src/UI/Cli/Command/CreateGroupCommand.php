<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Cli\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;

class CreateGroupCommand extends ConsoleCommand {

    public function __construct(
        private CommandBus $commandBus
    ) {
        parent::__construct();
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

        $io = new SymfonyStyle($input, $output);

        $command->code = $io->ask('Group code');
        $command->group_role = $io->choice('Please, select group role', ['ROLE_USER', 'ROLE_ADMIN'], 0);
        $command->sort = (int) $io->ask('Group sort', '1');

        $this->commandBus->handle($command);

        $io->success('User group was created!');
        $io->text([
            "Title: $command->title",
            "Code: $command->code",
            "Role: $command->group_role",
            "Sort: $command->sort"
        ]);

        return self::SUCCESS;
    }
}
