<?php

namespace App\Service;

interface GithubApiServiceInterface
{

    /**
     * This method would be configurable to match the Github API, in the interest of time we're hardcoding to PHP
     * Better yet - this would use v4 and GraphQL to improve performance, i.e. only get the fields we're actually using.
     *
     * @return array
     */
    public function search() : array;
}