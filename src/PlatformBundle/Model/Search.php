<?php

namespace PlatformBundle\Model;

class Search
{
    private $query;

    public function __construct()
    {
        $this->query = '';
    }

    /**
     * Set query
     *
     * @param string $query
     *
     * @return Search
     */
    public function setQuery($query)
    {
        $this->query = $query === null ? '' : trim($query);

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
