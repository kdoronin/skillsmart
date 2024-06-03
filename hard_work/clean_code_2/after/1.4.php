<?php
class Game
{
    private $id;
    private $slug;
    private $methodMap;

    public function __construct($id = null, $slug = null)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->initializeMethodMap();
    }

    private function initializeMethodMap()
    {
        $this->methodMap = [
            'gameId' => 'getGameIdById',
            'gameTermById' => 'getGameTermById',
            'gameTermBySlug' => 'getGameTermBySlug',
            'getGameId' => 'getGameIdById',
            'getGameTerm' => 'getGameTermById',
            'getGameTermBySlugMethod' => 'getGameTermBySlug'
        ];
    }

    /**
     * Get top category id (game id) by page id (subcategory or product)
     * Retrieve null if there is no category with such page or int if success
     *
     * @param int $id
     * @return int|null
     */
    private function getGameIdById(int $id): ?int
    {
        $product = wc_get_product($id);
        $term = get_term($id);

        if (!$product && !$term) {
            return null;
        }

        if (!$product && $term) {
            $categories = get_ancestors($id, 'product_cat');
            return !empty($categories) ? end($categories) : $id;
        }

        $id = $product->is_type('variation') ? $product->get_parent_id() : $id;
        if (!$termIds = wc_get_product_cat_ids($id)) {
            return null;
        }

        return ($categoryId = end($termIds)) === false ? null : $categoryId;
    }

    /**
     * Get game term by queried object id (category, subcategory, product)
     * Return null if id doesnt belong to any game
     *
     * @param int $id
     * @return WP_Term|null
     */
    private function getGameTermById(int $id): ?WP_Term
    {
        if (!$gameId = $this->getGameIdById($id)) {
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
    private function getGameTermBySlug(string $slug): ?WP_Term
    {
        if (empty($slug)) {
            return null;
        }

        $term = get_term_by('slug', $slug, 'product_cat');
        return is_wp_error($term) || !($term instanceof WP_Term) ? null : $term;
    }

    /**
     * Магический метод для получения свойств
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->methodMap)) {
            return $this->{$this->methodMap[$name]}($this->id);
        }

        return null;
    }

    /**
     * Магический метод для вызова методов
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->methodMap)) {
            return $this->{$this->methodMap[$name]}(...$arguments);
        }

        return null;
    }
}