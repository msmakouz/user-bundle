<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zentlix\MainBundle\Domain\Locale\Entity\Locale;
use Zentlix\MainBundle\UI\Http\Web\Controller\Admin\ResourceController;
use Zentlix\UserBundle\Application\Command\AdminSetting\Locale\ChangeLocaleCommand;
use Zentlix\UserBundle\Application\Command\AdminSetting\Widgets\ChangeWidgetsCommand;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;
use Zentlix\UserBundle\UI\Http\Web\Form\Widget\Form;

class AdminSettingsController extends ResourceController
{
    public function locale(Locale $locale, Request $request): Response
    {
        try {
            $this->exec(new ChangeLocaleCommand($locale));
            $request->setLocale($locale->getCode());

            $referer = $request->headers->get('referer');

            return $this->redirect($referer);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('admin.index');
        }
    }

    public function widgets(AdminSettings $settings): Response
    {
        static::$updateSuccessMessage = 'zentlix_user.widgets.update_success';

        return $this->updateResource(
            new ChangeWidgetsCommand($settings->getWidgets()), Form::class, '@MainBundle/admin/widget_settings/settings.html.twig'
        );
    }
}