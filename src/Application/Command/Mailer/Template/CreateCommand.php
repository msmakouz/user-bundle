<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Mailer\Template;

use Symfony\Component\HttpFoundation\Request;
use Zentlix\MainBundle\Application\Command\CreateCommandInterface;

class CreateCommand extends Command implements CreateCommandInterface
{
    public function __construct(Request $request = null)
    {
        if($request) {
            $this->title = $request->request->get('title');
            $this->active = $request->request->get('active', true);
            $this->event = $request->request->get('event');
            $this->provider = $request->request->get('provider', 'email');
            $this->theme = $request->request->get('theme');
            $this->code = $request->request->get('code');
            $this->body = $request->request->get('body');
            $this->recipient = $request->request->get('recipient', '%default_to%');
            $this->sites = array_flip($request->request->get('sites', []));
        }
    }
}