<?php

namespace Pulp;

class Search
{
    protected $db;
    protected $query;

    public function __construct()
    {
        global $wpdb, $wp_query;
        $this->db    = (object) $wpdb;
        $this->query = (object) $wp_query;
        add_filter('posts_join', [$this, 'postsJoin']);
        add_filter('posts_where', [$this, 'postsWhere']);
        add_filter('posts_distinct', [$this, 'postsDistinct']);
    }

    /**
     * Join posts on postmeta
     * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
     */
    public function postsJoin(string $join): string
    {
        if (! empty($this->query->query_vars['s'])) {
            $join .= ' LEFT JOIN ' . $this->db->postmeta . ' ON ' . $this->db->posts . '.ID = ' . $this->db->postmeta . '.post_id ';
        }
        return $join;
    }

    /**
     * Modify where to include meta value
     * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
     */
    public function postsWhere(string $where): string
    {
        if (! empty($this->query->query_vars['s'])) {
            $where = preg_replace(
                '/\(\s*' . $this->db->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                '(' . $this->db->posts . '.post_title LIKE $1) OR (' . $this->db->postmeta . '.meta_value LIKE $1)',
                $where
            );
        }
        return $where;
    }

    /**
     * Prevent duplicates
     * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
     */
    public function postsDistinct(string $distinct): string
    {
        if (! empty($this->query->query_vars['s'])) {
            return 'DISTINCT';
        }
        return $distinct;
    }
}
