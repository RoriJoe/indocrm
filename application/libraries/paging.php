<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * Description of paging
 *
 * @author ferdhie
 */
class Paging
{
    var $page_param = 'p';
    var $offset_param = 'o';
    var $page = 1;
    var $offset = 20;
    var $start = 0;
    var $numrows = 0;
    var $page_count = 0;
    var $pages = array();
    var $prev_trailing = 3;
    var $next_trailing = 3;
    var $prev = 0;
    var $next = 0;

    var $page_url = '';
    var $page_query = array();

    function Paging( $params=null )
    {
        if (is_array($params))
        {
            foreach( $params as $k => $v )
            {
                if ( isset($this->$k) )
                    $this->$k = $v;
            }
        }

        if (!$this->page)
            $this->page = 1;

        if (!$this->offset)
            $this->offset = 20;

        $this->start = abs(($this->page-1) * $this->offset);
        if ( $this->page_url )
        {
            $info = parse_url( $this->page_url );
            $this->page_url = (isset( $info['scheme'] ) ? "$info[scheme]://" : '') .
                              (isset( $info['host'] ) ? $info['host'] : '') .
                              (isset( $info['path'] ) ? $info['path'] : '/');
            if ( isset($info['query']) )
            {
                parse_str( $info['query'], $this->page_query );
            }
        }
    }

    function page_url( $page, $offset=0 )
    {
        $page_query = $this->page_query;
        $page_query[$this->page_param] = $page;
        $page_query[$this->offset_param] = $offset ? $offset : $this->offset;
        $query_string = http_build_query( $page_query );
        return $this->page_url . "?$query_string";
    }

    function create_paging( $numrows )
    {
        $this->numrows = $numrows;

        $this->page_count = ceil($this->numrows / $this->offset);
        $this->pages = array($this->page);

        //build next
        for( $i=$this->page+1, $j=0; ($i <= $this->page_count && $j < $this->next_trailing); $i++, $j++ )
            $this->pages[] = $i;

        //last page
        if ( $i+1 <= $this->page_count )
        {
            $this->pages[] = '';
            $this->pages[] = $this->page_count;
        }

        //build prev
        for( $i=$this->page-1, $j=0; ($i >= 1 && $j < $this->prev_trailing); $i--, $j++ )
        {
            array_unshift( $this->pages, $i );
        }

        //first page
        if ( $i-1 >= 1 )
        {
            array_unshift( $this->pages, '' );
            array_unshift( $this->pages, 1 );
        }

        $this->prev = $this->start > 0 ? $this->page - 1 : 0;
        $this->next = $this->page < $this->page_count ? $this->page + 1 : 0;

        return $this->pages;
    }
}
