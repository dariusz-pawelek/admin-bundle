<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\AdminBundle\Doctrine\Admin;

use PhpSpec\ObjectBehavior;
use Doctrine\Common\Persistence\ManagerRegistry;

class ResourceElementSpec extends ObjectBehavior
{
    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    function let($registry)
    {
        $this->beAnInstanceOf('FSi\Bundle\AdminBundle\spec\fixtures\MyResourceElement');
        $this->setManagerRegistry($registry);
    }

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param \FSi\Bundle\ResourceRepositoryBundle\Model\ResourceValueRepository $repository
     */
    function it_return_repository($registry, $om, $repository)
    {
        $registry->getManagerForClass('FSi\Bundle\DemoBundle\Entity\Resource')->willReturn($om);
        $om->getRepository('FSi\Bundle\DemoBundle\Entity\Resource')->willReturn($repository);

        $this->getRepository()->shouldReturn($repository);
    }

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param \Doctrine\Common\Persistence\ObjectRepository $repository
     */
    function it_throws_exception_when_repository_does_not_implement_resource_value_repository(
        $registry, $om, $repository
    ) {
        $registry->getManagerForClass('FSi\Bundle\DemoBundle\Entity\Resource')->willReturn($om);
        $registry->getRepository('FSi\Bundle\DemoBundle\Entity\Resource')->willReturn($repository);

        $this->shouldThrow()->during('getRepository');
    }
}
