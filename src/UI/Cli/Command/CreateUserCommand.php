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
use Symfony\Component\Console\Style\SymfonyStyle;
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
        $io = new SymfonyStyle($input, $output);

        $groups = array_flip($this->groupRepository->assoc());

        $command = new CreateCommand();

        $command->sendRegistrationEmail = false;
        $command->email = $input->getArgument('email');
        $command->plain_password = $io->ask('Password', null, function ($password) {
            if (empty($password)) {
                throw new \RuntimeException('Password cannot be empty.');
            }

            return $password;
        });
        $command->groups = [$io->choice('Please, select group', $groups, 'admin-group')];
        $command->first_name = (string) $io->ask('First name');
        $command->last_name = (string) $io->ask('Last name');
        $command->middle_name = (string) $io->ask('Middle name');
        $phone = $io->ask('Phone number');
        if($phone) {
            $command->phone = $this->phoneNumberUtil->parse((string) $phone, PhoneNumberUtil::UNKNOWN_REGION);
        }
        $command->status = (string) $io->choice('Please, select user status', [
            User::STATUS_ACTIVE,
            User::STATUS_BLOCKED,
            User::STATUS_WAIT
        ], 0);

        $command->zip = $io->ask('Zip code');
        $command->country = $io->ask('Country');
        $command->city = $io->ask('City');
        $command->street = $io->ask('Street');
        $command->house = $io->ask('House');
        $command->flat = $io->ask('Flat');

        try {
            $this->commandBus->handle($command);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage()); exit;
        }

        $io->success('User was created!');

        $io->text("Email: $command->email");
        $io->text("Password: $command->plain_password");
        $io->text(sprintf("Group: %s", array_shift($command->groups)->getTitle()));
        $io->text("Status: $command->status");
        $io->text("Zip code: $command->zip");
        $io->text("Country: $command->country");
        $io->text("City: $command->city");
        $io->text("Street: $command->street");
        $io->text("House: $command->house");
        $io->text("Flat: $command->flat");
    }
}