<?php

/*
 * Исходная ЦС метода – 19
 * Целевая ЦС метода – 9
 * Итоговая ЦС метода (беру метод с самым высоким ЦС) – 5
 * Анализ ситуации до:
 * 1. Метод начинается сразу с двух проверок на тип страницы. Очевидно, что решение о показе на конкретной странице
 *    нужно вынести на уровень выше. В экосистеме CMS WordPress (в которой и происходит дело) вопрос решается на уровне
 *    представления (шаблона) конкретной страницы. Альтернативным решением может являться табличная логика, когда у нас
 *    заранее задано соответствие отображаемых модулей со страницами в иерархии.
 * 2. В методе есть явное нарушение принципа единственной ответственности. Так как первая его часть отвечает за
 *    получение структуры меню из transient-кэша, а вторая занимается формированием меню в отсутствии этого кэша.
 *    Здесь решение – разделить исходный метод на несколько других, соблюдающих принцип единственной ответственности.
 * 3. Во второй части метода также перемешаны, по сути, две функциональности: отображение меню на странице категории и
 *    отображение меню на странице игры.
 * 4. Структуре плагина в целом не хватает класса Menu с дочерними классами GameMenu и CategoryMenu
 * 5. Также стоит добавить класс Page, который будет хранить информацию о типе текущей страницы и в зависимости от типа
 *    будет формироваться то или иное меню
 *
 * Результат работы:
 * 1. Из-за запутанности бизнес-логики, было принято решение отказаться от отдельных классов для меню.
 * 2. Разнёс логику из класса Templates по более узко-специализированным классам. Считаю, что логике в целом нужен
 *    рефакторинг. Так как хранение кэша является неоптимальным
 * 3. Не получилось для меню найти более элегантного решения, как всё переделать. По этому, вероятно, я просто
 *    перераспределил ЦС по другим методам. Но это хотя бы улучшит понимание, что и для чего используется
 * 4. Цикломатическая сложность самого "сложного" класса снизилась с 22 до 13. Тоже какой-то результат
 *
 * Вывод:
 * Метод было перерабатывать действительно сложно. Возможно, я упустил какой-то из инструментов, который сработал бы
 * здесь наилучшим способом.
 * */


class Templates
{
    public function __construct()
    {
        // Game menu
        add_action('portal_left_sidebar_before', [$this, 'getGameMenu']);
    }

    /**
     * Get game menu
     */
    public function getGameMenu(): void
    {
        $page = new CurrentPage();
        $menu = new PortalMenu($page->getType());
        echo $menu->getMenu();
    }
}

class PortalMenu
{
    private array $menuType;

    private $transient;
    private string $language;
    private int $gameTermID;
    private int $catTermID;

    public function __construct($menuType)
    {
        $this->menuType  = $menuType;
        $this->language  = function_exists('wpm_get_language') ? wpm_get_language() : '';
        $this->transient = $this->getTransient();
    }

    private function getTransient()
    {
        $this->gameTermID = get_current_game('term_id') ?: 0;
        if (in_array('game', $this->menuType)) {
            return get_transient("portal_game_menu_game_{$this->language}_{$this->gameTermID}");
        }
        $this->catTermID = get_queried_object()->term_id;
        $transientArray  = get_transient("portal_game_menu_cat_{$this->language}_{$this->catTermID}");

        return is_array($transientArray) && ! empty($transientArray[$this->catTermID])
            ? $transientArray[$this->catTermID]
            : '';
    }

    private function saveTransient($menu): void
    {
        if (in_array('game', $this->menuType)) {
            set_transient("portal_game_menu_game_{$this->language}_{$this->gameTermID}", $menu, DAY_IN_SECONDS);
        } else {
            $game_menu_array               = get_transient(
                "portal_game_menu_game_{$this->language}_{$this->gameTermID}"
            ) ?: [];
            $game_menu_array[$this->catTermID] = $menu;
            set_transient("portal_game_menu_game_{$this->language}_{$this->gameTermID}", $game_menu_array, DAY_IN_SECONDS);
        }
    }

    public function getMenu()
    {
        if ( ! empty($this->transient)) {
            return $this->transient;
        }
        $game_menu = new Walker_GameMenu();
        $args      = [
            'current_category' => $this->gameTermID,
            'show_count'       => true,
            'show_option_all'  => __('All'),
            'taxonomy'         => 'video_game',
            'title_li'         => '<h2 class="game-menu-title">' . apply_filters(
                    'portal_game_block_title',
                    'Games'
                ) . '</h2>',
            'orderby'          => 'count',
            'order'            => 'DESC',
            'echo'             => false,
            'class'            => 'game-menu-inner',
            'show_arrow'       => true,
            'walker'           => $game_menu,
        ];
        if ($this->catTermID) {
            $post_args = [
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [
                    [
                        'taxonomy' => 'category',
                        'field'    => 'id',
                        'terms'    => $this->catTermID,
                    ],
                ]
            ];

            $query              = new WP_Query($post_args);
            $args['object_ids'] = $query->posts;
        }
        $menu = wp_list_categories($args);
        $menu = '<div id="game-menu" class="game-menu">' . $this->fixAllGamesURL($menu) . '</div>';
        $this->saveTransient($menu);
    }


    /**
     * Fix 'All' menu item URL
     *
     * @param string $menu
     *
     * @return string
     */
    private function fixAllGamesURL(string $menu): string
    {
        $category    = is_category() ? get_queried_object()->slug : 'all';
        $link        = is_category()
            ? get_category_link(get_queried_object()->term_id)
            : get_portal_url();
        $posts_count = '<span class="count">' . get_portal_posts_count(
                $category
            ) . '</span><span class="arrow"></span>';
        $current_cat = get_current_game() ? '' : ' current-cat';

        return (string)preg_replace(
            '~(class=\'cat-item-all)(\'>\s*<a href=\')[^*]*(\'>[^<]*</a>)~m',
            '$1' . $current_cat . '$2' . $link . '$3' . $posts_count,
            $menu
        );
    }

}

class CurrentPage
{
    private array $type;

    private const TYPES_AND_CHECKERS = [
        'category' => 'is_category',
        'author'   => 'is_author',
        'search'   => 'is_search',
        'game'     => 'is_game',
    ];

    public function __construct()
    {
        $this->type = $this->getPageType();
    }

    private function getPageType(): array
    {
        return
            array_keys(
                array_filter(self::TYPES_AND_CHECKERS, function ($checker) {
                    return $checker();
                }
                )
            );
    }

    public function getType(): array
    {
        return $this->type;
    }
}