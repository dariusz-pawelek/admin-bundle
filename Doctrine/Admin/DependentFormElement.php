<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminBundle\Doctrine\Admin;

use FSi\Bundle\AdminBundle\Admin\DependentElement;
use FSi\Bundle\AdminBundle\Admin\DependentElementImpl;

abstract class DependentFormElement extends FormElement implements DependentElement
{
    use DependentElementImpl;

    public function getRouteParameters()
    {
        return array_merge(
            parent::getRouteParameters(),
            [DependentElement::REQUEST_PARENT_PARAMETER => $this->getParentObjectId()]
        );
    }
}
