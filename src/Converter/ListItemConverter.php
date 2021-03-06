<?php

namespace League\HTMLToMarkdown\Converter;

use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\ElementInterface;

class ListItemConverter implements ConverterInterface, ConfigurationAwareInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param Configuration $config
     */
    public function setConfig(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string
     */
    public function convert(ElementInterface $element)
    {
        // If parent is an ol, use numbers, otherwise, use dashes
        $list_type = $element->getParent()->getTagName();

        // Add spaces to start for nested list items
        $level = $element->getListItemLevel($element);

        $prefixForParagraph = str_repeat("\t", $level + 1);
        $value = trim(implode("\n" . $prefixForParagraph, explode("\n", trim($element->getValue()))));

        // If list item is the first in a nested list, add a newline before it
        $prefix = '';
        if ($level > 0 && $element->getSiblingPosition() === 1) {
            $prefix = "\n";
        }

        if ($list_type === 'ul') {
            $list_item_style = $this->config->getOption('list_item_style', '-');
            return $prefix . $list_item_style . ' ' . $value . "\n";
        }

        if ($list_type === 'ol' && $start = $element->getParent()->getAttribute('start')) {
            $number = $start + $element->getSiblingPosition() - 1;
        } else {
            $number = $element->getSiblingPosition();
        }

        return $prefix . $number . '. ' . $value . "\n";
    }

    /**
     * @return string[]
     */
    public function getSupportedTags()
    {
        return array('li');
    }
}
