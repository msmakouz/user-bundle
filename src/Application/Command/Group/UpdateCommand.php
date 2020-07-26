<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Group;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Application\Command\UpdateCommandInterface;
use Zentlix\MainBundle\AbstractZentlixBundle;
use Zentlix\MainBundle\Domain\Bundle\Entity\Bundle;
use Zentlix\MainBundle\Domain\Bundle\Repository\BundleRepository;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $code = null;

    public static array $rightsTitles = [];
    public static array $bundleTitles = [];

    private ContainerInterface $container;

    private BundleRepository $bundleRepository;

    public function __construct(UserGroup $group, BundleRepository $bundleRepository, ContainerInterface $container)
    {
        $this->container = $container;
        $this->bundleRepository = $bundleRepository;

        $this->title = $group->getTitle();
        $this->code = $group->getCode();
        $this->group_role = $group->getGroupRole();
        $this->sort = $group->getSort();
        $this->entity = $group;
        $this->setRights();
    }

    public function getEntity(): UserGroup
    {
        return $this->entity;
    }

    public function getRights(): array
    {
        $rights = [];
        foreach (self::$rightsTitles as $right => $val) {
            $rights[$right] = $this->{str_replace('\\', ':', $right)};
        }

        return $rights;
    }

    private function setRights()
    {
        $bundles = $this->bundleRepository->findAll();

        /** @var Bundle $bundle */
        foreach ($bundles as $bundle) {
            $class = $bundle->getClass();
            /** @var AbstractZentlixBundle $kernel */
            $kernel = new $class($this->container);
            if(count($rights = $kernel->configureRights())) {
                foreach ($rights as $property => $title) {
                    $this->createProperty(str_replace('\\', ':', $property), $this->entity->isAccessGranted($property));
                    self::$rightsTitles[$property] = $title;
                    self::$bundleTitles[$bundle->getTitle()][] = str_replace('\\', ':', $property);
                }
            }
        }
    }
}