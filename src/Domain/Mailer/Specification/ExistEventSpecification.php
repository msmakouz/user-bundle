<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Specification;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Domain\Shared\Specification\AbstractSpecification;
use Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository;

final class ExistEventSpecification extends AbstractSpecification
{
    private EventRepository $eventRepository;
    private TranslatorInterface $translator;

    public function __construct(EventRepository $eventRepository, TranslatorInterface $translator)
    {
        $this->eventRepository = $eventRepository;
        $this->translator = $translator;
    }

    public function isExist(int $eventId): bool
    {
        return $this->isSatisfiedBy($eventId);
    }

    public function isSatisfiedBy($value): bool
    {
        $event = $this->eventRepository->find($value);

        if(is_null($event)) {
            throw new \DomainException(sprintf($this->translator->trans('zentlix_user.mailer.event_not_found'), $value));
        }

        return true;
    }

    public function __invoke(int $eventId)
    {
        return $this->isExist($eventId);
    }
}