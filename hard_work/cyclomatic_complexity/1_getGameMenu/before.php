<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

class Templates
{
    public function __construct()
    {
        // Game menu
        add_action( 'portal_left_sidebar_before', [ $this, 'getGameMenu' ] );
    }

    /**
     * Get game menu
     */
    public function getGameMenu(): void
    {
        if ( ! is_author() && ! is_search() ) {
            $game_term_id = get_current_game( 'term_id' ) ?: 0;
            $cat_term_id  = is_category() ? get_queried_object()->term_id : 0;
            $language     = function_exists( 'wpm_get_language' ) ? wpm_get_language() : '';

            // caching
            if ( ! $game_term_id && $cat_term_id ) {
                $game_menu_content = get_transient( "portal_game_menu_cat_{$language}_{$cat_term_id}" );
            }
            elseif ( $game_term_id && $cat_term_id ) {
                $game_menu_array   = get_transient( "portal_game_menu_game_{$language}_{$game_term_id}" );
                $game_menu_content = is_array( $game_menu_array ) && ! empty( $game_menu_array[ $cat_term_id ] )
                    ? $game_menu_array[ $cat_term_id ]
                    : '';
            }

            // show results from cache
            if ( ! empty( $game_menu_content ) ) {
                echo $game_menu_content;
                return;
            }

            $game_menu = new Walker_GameMenu();

            // wp_list_categories args for video_game taxonomy
            $args      = [
                'current_category' => $game_term_id,
                'show_count'       => true,
                'show_option_all'  => __( 'All' ),
                'taxonomy'         => 'video_game',
                'title_li'         => '<h2 class="game-menu-title">' . apply_filters( 'portal_game_block_title', 'Games' ) . '</h2>',
                'orderby'          => 'count',
                'order'            => 'DESC',
                'echo'             => false,
                'class'            => 'game-menu-inner',
                'show_arrow'       => true,
                'walker'           => $game_menu,
            ];

            // wp_list_categories args for category taxonomy
            if ( $cat_term_id ) {
                $post_args = [
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'category',
                            'field'    => 'id',
                            'terms'    => $cat_term_id,
                        ],
                    ]
                ];

                $query              = new WP_Query( $post_args );
                $args['object_ids'] = $query->posts;
            }

            $menu = wp_list_categories( $args );
            $menu = '<div id="game-menu" class="game-menu">' . $this->fixAllGamesURL( $menu ) . '</div>';

            // set cache
            if ( ! $game_term_id && $cat_term_id ) {
                set_transient( "portal_game_menu_cat_{$language}_{$cat_term_id}", $menu, DAY_IN_SECONDS );
            }
            elseif ( $game_term_id && $cat_term_id ) {
                $game_menu_array = get_transient( "portal_game_menu_game_{$language}_{$game_term_id}" ) ?: [];
                $game_menu_array[ $cat_term_id ] = $menu;
                set_transient( "portal_game_menu_game_{$language}_{$game_term_id}", $game_menu_array, DAY_IN_SECONDS );
            }

            echo $menu;
        }
    }

    /**
     * Fix 'All' menu item URL
     *
     * @param  string $menu
     * @return string
     */
    private function fixAllGamesURL( string $menu ): string
    {
        $category    = is_category() ? get_queried_object()->slug : 'all';
        $link        = is_category()
            ? get_category_link( get_queried_object()->term_id )
            : get_portal_url();
        $posts_count = '<span class="count">' . get_portal_posts_count( $category ) . '</span><span class="arrow"></span>';
        $current_cat = get_current_game() ? '' : ' current-cat';

        return (string) preg_replace(
            '~(class=\'cat-item-all)(\'>\s*<a href=\')[^*]*(\'>[^<]*</a>)~m',
            '$1' . $current_cat . '$2' . $link . '$3' . $posts_count,
            $menu
        );
    }
}