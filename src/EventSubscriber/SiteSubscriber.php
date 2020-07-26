<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zentlix\MainBundle\Application\Command\DynamicPropertyCommand;
use Zentlix\MainBundle\Application\Command\Site;
use Zentlix\MainBundle\Domain\Site\Entity;
use Zentlix\MainBundle\Domain\Site\Event;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Application\Command\Site as SiteCommand;
use Zentlix\UserBundle\Domain\User\Repository\SiteRepository;

class SiteSubscriber implements EventSubscriberInterface
{
    private CommandBus $commandBus;
    private SiteRepository $siteRepository;

    public function __construct(CommandBus $commandBus, SiteRepository $userSiteRepository)
    {
        $this->commandBus = $commandBus;
        $this->siteRepository = $userSiteRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\CreateForm::class   => 'addSiteFields',
            Event\UpdateForm::class   => 'addSiteFields',
            Event\AfterCreate::class  => 'onAfterCreateSite',
            Event\AfterUpdate::class  => 'onAfterUpdateSite',
            Event\BeforeDelete::class => 'onBeforeDelete'
        ];
    }

    /**
     * @param Event\CreateForm|Event\UpdateForm $event
     */
    public function addSiteFields($event)
    {
        $builder = $event->getFormBuilder();

        /** @var $command DynamicPropertyCommand */
        $command = $builder->getData();

        $userSite = null;
        if($command instanceof Site\UpdateCommand) {
            $userSite = $this->siteRepository->findOneBySiteId($command->getEntity()->getId());
        }

        $command->createProperty('email', $userSite ? $userSite->getEmail()->getValue() : null);
        $command->createProperty('smtp_host', $userSite ? $userSite->getSmtpHost() : null);
        $command->createProperty('smtp_port', $userSite ? $userSite->getSmtpPort() : null);
        $command->createProperty('smtp_user', $userSite ? $userSite->getSmtpUser() : null);
        $command->createProperty('smtp_password', $userSite ? $userSite->getSmtpPassword() : null);

        $user = $builder->create('user', Type\FormType::class, ['inherit_data' => true, 'label' => 'zentlix_user.user.users'])
            ->add('email', Type\EmailType::class, ['label' => 'zentlix_user.email'])
            ->add('smtp_host', Type\TextType::class, ['label' => 'zentlix_user.smtp_host', 'required' => false])
            ->add('smtp_port', Type\IntegerType::class, ['label' => 'zentlix_user.smtp_port', 'required' => false])
            ->add('smtp_user', Type\TextType::class, ['label' => 'zentlix_user.user.user', 'required' => false])
            ->add('smtp_password', Type\PasswordType::class, ['label' => 'zentlix_user.password', 'required' => false]);

        $builder->add($user);
    }

    public function onAfterCreateSite(Event\AfterCreate $afterCreate)
    {
        /** @var Site\Command $siteCommand */
        $siteCommand = $afterCreate->getCommand();
        /** @var Entity\Site $site */
        $site = $afterCreate->getEntity();

        $command = new SiteCommand\CreateCommand();
        $command = $this->prepareCommand($command, $siteCommand);
        $command->site = $site;

        $this->commandBus->handle($command);
    }

    public function onAfterUpdateSite(Event\AfterUpdate $afterUpdate)
    {
        /** @var Site\UpdateCommand $siteCommand */
        $siteCommand = $afterUpdate->getCommand();

        $command = new SiteCommand\UpdateCommand($this->siteRepository->findOneBySiteId($afterUpdate->getEntity()->getId()));
        $command = $this->prepareCommand($command, $siteCommand);

        $this->commandBus->handle($command);
    }

    public function onBeforeDelete(Event\BeforeDelete $beforeDelete)
    {
        $setting = $this->siteRepository->findOneBySiteId($beforeDelete->getCommand()->site->getId());

        if($setting) {
            $this->commandBus->handle(new SiteCommand\DeleteCommand($setting));
        }
    }

    private function prepareCommand(SiteCommand\Command $command, Site\Command $siteCommand)
    {
        $command->email = $siteCommand->getProperty('email');
        $command->smtp_host = $siteCommand->getProperty('smtp_host');
        $command->smtp_port = $siteCommand->getProperty('smtp_port');
        $command->smtp_user = $siteCommand->getProperty('smtp_user');
        $command->smtp_password = $siteCommand->getProperty('smtp_password');

        return $command;
    }
}