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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zentlix\MainBundle\UI\Http\Web\Controller\Admin\ResourceController;
use Zentlix\UserBundle\Application\Query\Journal\DataTableQuery;
use Zentlix\UserBundle\UI\Http\Web\DataTable\Journal\Table;

class JournalController extends ResourceController
{
    public function index(Request $request): Response
    {
        return $this->listResource(new DataTableQuery(Table::class), $request);
    }
}