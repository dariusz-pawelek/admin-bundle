<?php

namespace spec\FSi\Bundle\AdminBundle\DataGrid\Extension\Admin\ColumnTypeExtension;

use FSi\Component\DataGrid\Column\ColumnTypeInterface;
use FSi\Component\DataGrid\Column\CellViewInterface;
use FSi\Component\DataGrid\Column\HeaderViewInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FSi\Component\DataGrid\Column\ColumnTypeExtensionInterface;

class AttributesExtensionSpec extends ObjectBehavior
{
    function it_is_column_extension()
    {
        $this->shouldBeAnInstanceOf(ColumnTypeExtensionInterface::class);
    }

    function it_adds_actions_options(ColumnTypeInterface $column, OptionsResolver $optionsResolver)
    {
        $column->getOptionsResolver()->willReturn($optionsResolver);

        $optionsResolver->setDefined(['header_attr', 'cell_attr', 'container_attr', 'value_attr'])
            ->shouldBeCalled();
        $optionsResolver->setAllowedTypes('header_attr', 'array')->shouldBeCalled();
        $optionsResolver->setAllowedTypes('cell_attr', 'array')->shouldBeCalled();
        $optionsResolver->setAllowedTypes('container_attr', 'array')->shouldBeCalled();
        $optionsResolver->setAllowedTypes('value_attr', 'array')->shouldBeCalled();
        $optionsResolver->setDefaults([
            'header_attr' => [],
            'cell_attr' => [],
            'container_attr' => [],
            'value_attr' => []
        ])->shouldBeCalled();

        $this->initOptions($column);
    }

    function it_passes_attributes_to_cell_view(ColumnTypeInterface $column, CellViewInterface $view)
    {
        $column->getOption('cell_attr')->willReturn(['cell attributes']);
        $view->setAttribute('cell_attr', ['cell attributes'])->shouldBeCalled();
        $column->getOption('container_attr')->willReturn(['container attributes']);
        $view->setAttribute('container_attr', ['container attributes'])->shouldBeCalled();
        $column->getOption('value_attr')->willReturn(['value attributes']);
        $view->setAttribute('value_attr', ['value attributes'])->shouldBeCalled();

        $this->buildCellView($column, $view);
    }

    function it_passes_attributes_to_header_view(ColumnTypeInterface $column, HeaderViewInterface $view)
    {
        $column->getOption('header_attr')->willReturn(['header attributes']);
        $view->setAttribute('header_attr', ['header attributes'])->shouldBeCalled();

        $this->buildHeaderView($column, $view);
    }
}
