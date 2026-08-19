<?php

namespace Falang\Filter\Admin;

use Falang\Filter\Filters;

class Yoast extends Filters
{

    /**
     * Constructor
     *
     * @since 1.4.5
     *
     */
    public function __construct(&$falang)
    {
        parent::__construct($falang);
        add_action('wpseo_saved_indexable', [$this, 'wpseo_saved_indexable']);
    }

    /**
     * Store for term product_cat only for now the old meta in the meta term table
     * yoast now use the yoast_indexable table
     * use _yoast_wpseo_title and _yoast_wpseo_metadesc
     *
     * @since 1.4.5
     *
     */
    public function wpseo_saved_indexable($indexable)
    {

        // Seulement les catégories produits
        if ($indexable->object_type !== 'term' || $indexable->object_sub_type !== 'product_cat') {
            return;
        }

        update_term_meta($indexable->object_id, '_yoast_wpseo_title', $indexable->title);

        update_term_meta($indexable->object_id, '_yoast_wpseo_metadesc', $indexable->description);
    }
}