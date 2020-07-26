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
use libphonenumber\PhoneNumberUtil;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;
use Zentlix\UserBundle\Domain\User\Entity\User;

class CreateUserCommand extends ConsoleCommand {

    private CommandBus $commandBus;
    private GroupRepository $groupRepository;
    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(CommandBus $commandBus, GroupRepository $groupRepository, PhoneNumberUtil $phoneNumberUtil)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
        $this->groupRepository = $groupRepository;
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    protected function configure(): void
    {
        $this
            ->setName('zentlix_user:create:user')
            ->setDescription('Given a email, password, name, address generates a new user.')
            ->addArgument('email', InputArgument::REQUIRED, 'User Email address');
    }

    /** @throws \Exception */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new CreateCommand();

        $command->email = $input->getArgument('email');

        $helper = $this->getHelper('question');

        $question = new Question('Password:');
        $command->plain_password = $helper->ask($input, $output, $question);

        $groups = array_flip($this->groupRepository->assoc());
        $question = new ChoiceQuestion('Please, select group:', $groups);
        $question->setErrorMessage('Group %s is invalid.');
        $command->groups = [$this->groupRepository->findOneByCode($helper->ask($input, $output, $question))];

        $question = new Question('First name:');
        $command->first_name = (string) $helper->ask($input, $output, $question);

        $question = new Question('Last name:');
        $command->last_name = (string) $helper->ask($input, $output, $question);

        $question = new Question('Middle name:');
        $command->middle_name = (string) $helper->ask($input, $output, $question);

        $question = new Question('Middle name:');
        $command->middle_name = (string) $helper->ask($input, $output, $question);

        $question = new Question('Phone number:');
        $command->phone = $this->phoneNumberUtil->parse((string) $helper->ask($input, $output, $question), PhoneNumberUtil::UNKNOWN_REGION);

        $question = new ChoiceQuestion('Please, select user status:', [
            User::STATUS_ACTIVE,
            User::STATUS_BLOCKED,
            User::STATUS_WAIT
        ], 0);
        $question->setErrorMessage('Status %s is invalid.');
        $command->status = (string) $helper->ask($input, $output, $question);

        $question = new Question('Zip code:');
        $command->zip = $helper->ask($input, $output, $question);

        $question = new Question('User country:');
        $command->country = $helper->ask($input, $output, $question);

        $question = new Question('User city:');
        $command->city = $helper->ask($input, $output, $question);

        $question = new Question('Street:');
        $command->street = $helper->ask($input, $output, $question);

        $question = new Question('House:');
        $command->house = $helper->ask($input, $output, $question);

        $question = new Question('Flat:');
        $command->flat = $helper->ask($input, $output, $question);

        $this->commandBus->handle($command);

        $output->writeln('<info>User was created: </info>');
        $output->writeln('');
        $output->writeln("Email: $command->email");
        $output->writeln("Password: $command->plain_password");
        $output->writeln(sprintf("Group: %s", $this->groupRepository->findOneByCode(array_shift($command->groups))->getTitle()));
        $output->writeln("Status: $command->status");
        $output->writeln("Zip code: $command->zip");
        $output->writeln("Country: $command->country");
        $output->writeln("City: $command->city");
        $output->writeln("Street: $command->street");
        $output->writeln("House: $command->house");
        $output->writeln("Flat: $command->flat");
    }
}