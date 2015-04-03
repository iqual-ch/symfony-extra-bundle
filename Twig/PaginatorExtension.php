<?php

namespace SymfonyExtraBundle\Twig;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class PaginatorExtension extends Twig_Extension
{

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('paginator', array($this, 'paginator'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            ))
        );
    }

    public function paginator(Twig_Environment $twig, $totalItems, $itemsPerPage, $route, $currentPage = 1, $template = 'SymfonyExtraBundle::pagination.html.twig')
    {
        $pages = ceil($totalItems / $itemsPerPage);
        if ($pages > 0) {
            $pages = range(1, $pages);
        } else {
            $pages = array();
        }
        
        return $twig->render($template, array(
            'total' => $totalItems,
            'per_page' => $itemsPerPage,
            'route' => $route,
            'current_page' => $currentPage,
            'pages' => $pages,
            'prev_page' => $currentPage - 1,
            'next_page' => $currentPage + 1
        ));
    }

    public function getName()
    {
        return 'se_paginator';
    }

}
