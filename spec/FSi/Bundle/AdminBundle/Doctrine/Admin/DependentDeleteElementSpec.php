<?php

namespace spec\FSi\Bundle\AdminBundle\Doctrine\Admin;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use FSi\Bundle\AdminBundle\Admin\CRUD\DataIndexerElement;
use FSi\Bundle\AdminBundle\Admin\DependentElement;
use FSi\Bundle\AdminBundle\Admin\Element;
use FSi\Component\DataIndexer\DataIndexerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DependentDeleteElementSpec extends ObjectBehavior
{
    function let(ManagerRegistry $registry, ObjectManager $om)
    {
        $this->beAnInstanceOf('FSi\Bundle\AdminBundle\spec\fixtures\Doctrine\MyDependentDeleteElement');
        $this->beConstructedWith(array());

        $registry->getManagerForClass('FSiDemoBundle:Entity')->willReturn($om);
        $this->setManagerRegistry($registry);
    }

    function it_is_dependent_batch_element()
    {
        $this->shouldHaveType('FSi\Bundle\AdminBundle\Admin\DependentElement');
        $this->shouldHaveType('FSi\Bundle\AdminBundle\Doctrine\Admin\DeleteElement');
    }

    function it_returns_null_if_parent_element_does_not_have_data_indexer(
        RequestStack $requestStack,
        Request $currentRequest,
        Element $parentElement
    ) {
        $requestStack->getCurrentRequest()->willReturn($currentRequest);

        $this->setRequestStack($requestStack);
        $this->setParentElement($parentElement);

        $this->getParentObject()->shouldReturn(null);
    }

    function it_returns_null_if_parent_object_id_is_not_available(
        RequestStack $requestStack,
        Request $currentRequest,
        DataIndexerElement $parentElement,
        DataIndexerInterface $parentDataIndexer
    ) {
        $parentElement->getDataIndexer()->willReturn($parentDataIndexer);
        $requestStack->getCurrentRequest()->willReturn($currentRequest);
        $currentRequest->get(DependentElement::REQUEST_PARENT_PARAMETER)->willReturn(null);

        $this->setRequestStack($requestStack);
        $this->setParentElement($parentElement);

        $this->getParentObject()->shouldReturn(null);
    }

    function it_returns_parent_object_if_its_available(
        RequestStack $requestStack,
        Request $currentRequest,
        DataIndexerElement $parentElement,
        DataIndexerInterface $parentDataIndexer
    ) {
        $parentElement->getDataIndexer()->willReturn($parentDataIndexer);
        $requestStack->getCurrentRequest()->willReturn($currentRequest);
        $currentRequest->get(DependentElement::REQUEST_PARENT_PARAMETER)->willReturn('parent_object_id');
        $parentDataIndexer->getData('parent_object_id')->willReturn('parent_object');

        $this->setRequestStack($requestStack);
        $this->setParentElement($parentElement);

        $this->getParentObject()->shouldReturn('parent_object');
    }

    function its_route_parameters_contain_parent_object_id_if_its_available(
        RequestStack $requestStack,
        Request $currentRequest,
        DataIndexerElement $parentElement,
        DataIndexerInterface $parentDataIndexer
    ) {
        $parentElement->getDataIndexer()->willReturn($parentDataIndexer);
        $requestStack->getCurrentRequest()->willReturn($currentRequest);
        $currentRequest->get(DependentElement::REQUEST_PARENT_PARAMETER)->willReturn('parent_object_id');

        $this->setRequestStack($requestStack);
        $this->setParentElement($parentElement);

        $this->getRouteParameters()
            ->shouldHaveKeyWithValue(DependentElement::REQUEST_PARENT_PARAMETER, 'parent_object_id');
    }

    public function it_should_return_object_manager(ObjectManager $om)
    {
        $this->getObjectManager()->shouldReturn($om);
    }

    public function it_should_return_object_repository(ObjectManager $om, ObjectRepository $repository)
    {
        $om->getRepository('FSiDemoBundle:Entity')->willReturn($repository);
        $this->getRepository()->shouldReturn($repository);
    }

    public function it_should_have_doctrine_data_indexer(
        ManagerRegistry $registry,
        ObjectManager $om,
        ObjectRepository $repository,
        ClassMetadata $metadata
    ) {
        $registry->getManagerForClass('FSi/Bundle/DemoBundle/Entity/Entity')->willReturn($om);
        $om->getRepository('FSiDemoBundle:Entity')->willReturn($repository);
        $metadata->isMappedSuperclass = false;
        $metadata->rootEntityName = 'FSi/Bundle/DemoBundle/Entity/Entity';
        $om->getClassMetadata('FSi/Bundle/DemoBundle/Entity/Entity')->willReturn($metadata);

        $repository->getClassName()->willReturn('FSi/Bundle/DemoBundle/Entity/Entity');

        $this->setManagerRegistry($registry);
        $this->getDataIndexer()->shouldReturnAnInstanceOf('FSi\Component\DataIndexer\DoctrineDataIndexer');
    }

    public function it_deletes_object_from_object_manager(ObjectManager $om)
    {
        $object = new \stdClass();

        $om->remove($object)->shouldBeCalled();
        $om->flush()->shouldBeCalled();

        $this->apply($object);
    }
}
