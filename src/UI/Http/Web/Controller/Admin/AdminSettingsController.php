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
use Zentlix\MainBundle\Domain\Dashboard\Service\Widgets;
use Zentlix\MainBundle\UI\Http\Web\Controller\Admin\AbstractAdminController;
use Zentlix\UserBundle\Application\Command\AdminSetting\Locale\ChangeLocaleCommand;
use Zentlix\UserBundle\Application\Command\AdminSetting\Widgets\ChangeWidgetsCommand;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;
use Zentlix\UserBundle\UI\Http\Web\Form\Widget\Form;

class AdminSettingsController extends AbstractAdminController
{
    public function locale(Request $request): Response
    {
        try {
            $this->exec(new ChangeLocaleCommand($request->request->get('locale_id')));
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json($this->error($e->getMessage()));
        }
    }

    public function widgets(Request $request, Widgets $widgets, AdminSettings $settings): Response
    {
        try {
            $command = new ChangeWidgetsCommand($settings->getWidgets(), $widgets->getWidgets());
            $form = $this->createForm(Form::class, $command);

            $this->handleRequest($request, $form);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->exec($command);
                return $this->json($this->redirectSuccess($this->generateUrl('admin.index'), $this->translator->trans('zentlix_user.widgets.update_success')));
            }
        } catch (\Exception $e) {
            return $this->json($this->error($e->getMessage()));
        }

        return $this->json($this->liform->transform($form));
    }
}