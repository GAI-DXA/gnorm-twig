<?php

namespace Gnorm\Fed;

use Twig\Extension\ExtensionInterface;

class FedExtensions implements ExtensionInterface
{

    /**
     * @inheritDoc
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getNodeVisitors()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTests()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return [];
    }

}
