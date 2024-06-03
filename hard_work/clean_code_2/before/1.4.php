<?php


/**
 * Get top category id (game id) by page id (subcategory or product)
 * Retrieve null if there is no category with such page or int if success
 *
 * @param int $id
 * @return int|null
 */
function wvGetGameId(int $id): ?int
{
    $product = wc_get_product($id);
    $term = get_term($id);
    /* if received id isnt a product id or a category id, then it is invalid value */
    if (!$product && !$term) {
        return null;
    }

    /* try to get highest category id by cat or subcat id */
    if (!$product && $term) {
        $categories = get_ancestors($id, 'product_cat');
        /* for top level categories ancestors doesnt exist, then return received id */
        return !empty($categories) ? end($categories) : $id;
    }

    $id = $product->is_type('variation') ? $product->get_parent_id() : $id;
    /* try to get highest category id by product id */
    if (!$termIds = wc_get_product_cat_ids($id)) {
        return null;
    }

    return ($categoryId = end($termIds)) === false ? null : $categoryId;
}

/**
 * Get game term by queried object id (category, subcategory, product)
 * Return null if id doesnt blow to any game
 *
 * @param int $id
 * @return WP_Term|null
 */
function wvGetGameTermByObjectId(int $id): ?WP_Term
{
    if (!$gameId = wvGetGameId($id)) {
        return null;
    }
    $gameTerm = get_term($gameId);
    return is_wp_error($gameTerm) || !($gameTerm instanceof WP_Term) ? null : $gameTerm;
}

/**
 * Get game term by slug or null
 *
 * @param string $slug
 * @return WP_Term|null
 */
function wvGetGameTermBySlug(string $slug): ?WP_Term
{
    if (empty($slug)) {
        return null;
    }

    $term = get_term_by('slug', $slug, 'product_cat');
    return is_wp_error($term) || !($term instanceof WP_Term) ? null : $term;
}