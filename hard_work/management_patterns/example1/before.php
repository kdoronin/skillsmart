<?php
public function getFields(): void
{
    if ( (function_exists( 'is_portal' ) && is_portal()) || is_checkout() ) {
        return;
    }

    global $mobile_prefix, $is_mobile;

    $is_mobile_prefix = $is_mobile ? $mobile_prefix . '-' : '';
    if (is_single() || is_page()) {
        global $post;
        $identifier = $post->ID;
    } else {
        $queried_object_id = get_queried_object_id();
        $identifier = "product_cat_{$queried_object_id}";
    }
    $identifier = apply_filters('custom_fields_page_identifier', $identifier);
    $points = array('top', 'bottom');
    if (is_singular('product')) {
        $this->used_blocks['woocommerce']['popular_services'] = $is_mobile_prefix . 'block-popular_services';
    }
    foreach ($points as $point) {
        if (have_rows("blocks_on_{$point}", $identifier)) {
            $counter = 0;
            while (have_rows("blocks_on_{$point}", $identifier)) {
                the_row();
                if (get_row_layout() == 'default_layout') {
                    if (have_rows('default_blocks')) {
                        while (have_rows('default_blocks')) {
                            the_row();
                            if (get_row_layout() == 'banner') {
                                $context = array();
                                $context['image'] = get_sub_field('image');
                                $context['heading'] = get_sub_field('heading');
                                $context['subheading'] = get_sub_field('subheading');
                                $context['bullets'] = array();
                                $n = 0;
                                if (have_rows('bullet')) {
                                    while (have_rows('bullet')) {
                                        the_row();
                                        if (get_sub_field('icon')) {
                                            $context['bullets'][$n]['icon'] = get_sub_field('icon');
                                        }
                                        $context['bullets'][$n]['heading'] = get_sub_field('heading');
                                        $context['bullets'][$n]['bold_text'] = get_sub_field('bold_text');
                                        $context['bullets'][$n]['text'] = get_sub_field('text');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'choose_your_game') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                if (have_rows('game_region')) {
                                    while (have_rows('game_region')) {
                                        the_row();
                                        $region_name = get_sub_field('region_name');
                                        if (have_rows('game')) {
                                            while (have_rows('game')) {
                                                the_row();
                                                $context['region'][$region_name]['game'][$n]['link'] = get_sub_field('game_link');
                                                $context['region'][$region_name]['game'][$n]['is_coming_soon'] = get_sub_field('is_coming_soon');
                                                $context['region'][$region_name]['game'][$n]['icon'] = get_sub_field('game_icon');
                                                $context['region'][$region_name]['game'][$n]['mobile_icon'] = get_sub_field('game_mobile_icon');
                                                $context['region'][$region_name]['game'][$n]['flag'] = get_sub_field('game_flag');
                                                $context['region'][$region_name]['game'][$n]['mobile_heading'] = get_sub_field('mobile_heading');
                                                $context['region'][$region_name]['game'][$n]['mobile_text'] = get_sub_field('mobile_text');
                                                $n++;
                                            }
                                        }
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getChooseYourGame";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'mobile_advantages') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                if (have_rows('bullet')) {
                                    while (have_rows('bullet')) {
                                        the_row();
                                        $context['bullets'][$n]['heading'] = get_sub_field('heading');
                                        $context['bullets'][$n]['bold_text'] = get_sub_field('bold_text');
                                        $context['bullets'][$n]['text'] = get_sub_field('text');
                                        $context['bullets'][$n]['image'] = get_sub_field('image');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getMobileAdvantages";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'seo_text') {
                                $context = array();
                                $context['text'] = get_sub_field('text');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getSeoText";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'contact_block') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                if (have_rows('rows')) {
                                    while (have_rows('rows')) {
                                        the_row();
                                        $context['rows'][$n]['heading'] = get_sub_field('heading');
                                        $i = 0;
                                        if (have_rows('items')) {
                                            while (have_rows('items')) {
                                                the_row();
                                                if (get_sub_field('copy') == 1) {
                                                    $context['rows'][$n]['items'][$i]['copy'] = get_sub_field('copy');
                                                } else {
                                                    $context['rows'][$n]['items'][$i]['copy'] = '';
                                                }
                                                $context['rows'][$n]['items'][$i]['copy_content'] = get_sub_field('copy_content');
                                                $context['rows'][$n]['items'][$i]['link'] = get_sub_field('link');
                                                $context['rows'][$n]['items'][$i]['link_target'] = false === strpos(get_sub_field('link'), 'javascript') ? '_blank' : '_self';
                                                $context['rows'][$n]['items'][$i]['heading'] = get_sub_field('heading');
                                                $context['rows'][$n]['items'][$i]['link_text'] = get_sub_field('link_text');
                                                $context['rows'][$n]['items'][$i]['icon'] = get_sub_field('icon');
                                                $context['rows'][$n]['items'][$i]['border'] = get_sub_field('border');
                                                $i++;
                                            }
                                        }
                                        $n++;
                                    }
                                }
                                $context['map_heading'] = get_sub_field('map_heading');
                                $context['map_subheading'] = get_sub_field('map_subheading');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getContactBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'big_customer_reviews_block') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getBigReviewsBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'faq_block') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                $context['use_a_big_header'] = get_sub_field('use_a_big_header');
                                if (have_rows('questions')) {
                                    while (have_rows('questions')) {
                                        the_row();
                                        $context['questions'][$n]['question'] = get_sub_field('question');
                                        $context['questions'][$n]['answer'] = get_sub_field('answer');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getFAQBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'about_us_block') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $context['subheading'] = get_sub_field('subheading');
                                $n = 0;
                                if (have_rows('items')) {
                                    while (have_rows('items')) {
                                        the_row();
                                        $context['items'][$n]['heading'] = get_sub_field('heading');
                                        $context['items'][$n]['text'] = get_sub_field('text');
                                        $context['items'][$n]['icon'] = get_sub_field('icon');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getAboutUsBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'service_request') {
                                $context = array();
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getServiceRequestBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'black_friday_big') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $context['white_heading'] = get_sub_field('white_heading');
                                $context['text'] = get_sub_field('text');
                                $context['right_block_heading'] = get_sub_field('right_block_heading');
                                $context['right_block_text'] = get_sub_field('right_block_text');
                                $context['small_right_image'] = get_sub_field('small_right_image');
                                $context['sucess_text_email_start'] = get_sub_field('sucess_text_email_start');
                                $context['sucess_text_email_end'] = get_sub_field('sucess_text_email_end');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getBigBlackFridayBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'black_friday_small') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $context['white_heading'] = get_sub_field('white_heading');
                                $context['text'] = get_sub_field('text');
                                $context['sucess_text_email_start'] = get_sub_field('sucess_text_email_start');
                                $context['sucess_text_email_end'] = get_sub_field('sucess_text_email_end');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getSmallBlackFridayBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'destiny_2_weekly_banner') {
                                $context = array();
                                $groups = ['first', 'second'];
                                foreach ($groups as $group) {
                                    $context['weapon_name_' . $group] = get_sub_field('weapon_name_' . $group);
                                    $context['dungeon_name_' . $group] = get_sub_field('dungeon_name_' . $group);
                                    $context['weapon_image_' . $group] = get_sub_field('weapon_image_' . $group);
                                    $context['time_to_end_' . $group] = get_sub_field('time_to_end_' . $group);
                                    $context['button_text_' . $group] = get_sub_field('button_text_' . $group);
                                    $context['button_url_' . $group] = get_sub_field('button_url_' . $group);
                                    $context['product_' . $group] = get_sub_field('product_' . $group);
                                    $context[$group . '_label'] = get_sub_field($group . '_label');
                                    $context['weapon_name_' . $group . '_mobile'] = get_sub_field('weapon_name_' . $group . '_mobile');
                                }
                                $context['background_image'] = get_sub_field('background_image');
                                $context['background_image_mobile'] = get_sub_field('background_image_mobile');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getD2WeeklyBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'destiny_2_three_weapons_banner') {
                                $context = array();
                                $groups = ['first', 'second', 'third'];
                                foreach ($groups as $group) {
                                    $context['weapon_name_' . $group] = get_sub_field('weapon_name_' . $group);
                                    $context['dungeon_name_' . $group] = get_sub_field('dungeon_name_' . $group);
                                    $context['weapon_image_' . $group] = get_sub_field('weapon_image_' . $group);
                                    $context['time_to_end_' . $group] = get_sub_field('time_to_end_' . $group);
                                    $context['button_text_' . $group] = get_sub_field('button_text_' . $group);
                                    $context['button_url_' . $group] = get_sub_field('button_url_' . $group);
                                    $context['variation_id_' . $group] = get_sub_field('variation_id_' . $group);
                                    $context['product_' . $group] = get_sub_field('product_' . $group);
                                    $context['ym_goal_name_' . $group] = get_sub_field('ym_goal_name_' . $group);
                                    $context[$group . '_label'] = get_sub_field($group . '_label');
                                    $context[$group . '_label_color'] = get_sub_field($group . '_label_color');
                                    $context['weapon_name_' . $group . '_mobile'] = get_sub_field('weapon_name_' . $group . '_mobile');
                                }
                                $context['discount_labels_color'] = get_sub_field('discount_labels_color');
                                $context['background_image'] = get_sub_field('background_image');
                                $context['background_image_mobile'] = get_sub_field('background_image_mobile');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getD2ThreeWeaponsBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'destiny_2_iron_banner') {
                                $context = array();
                                $context['background_image'] = get_sub_field('background_image');
                                $context['background_image_mobile'] = get_sub_field('background_image_mobile');
                                $context['second_label'] = get_sub_field('second_label');
                                $context['first_label'] = get_sub_field('first_label');
                                $context['timer_text'] = get_sub_field('timer_text');
                                $context['time_to_end'] = get_sub_field('time_to_end');
                                $context['button_text'] = get_sub_field('button_text');
                                $context['button_url'] = get_sub_field('button_url');
                                $context['tooltips_field'] = get_sub_field('tooltips_field');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getD2IronBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'destiny_2_iron_banner_one_tooltip') {
                                $context = array();
                                $context['background_image'] = get_sub_field('background_image');
                                $context['background_image_mobile'] = get_sub_field('background_image_mobile');
                                $context['second_label'] = get_sub_field('second_label');
                                $context['first_label'] = get_sub_field('first_label');
                                $context['timer_text'] = get_sub_field('timer_text');
                                $context['time_to_end'] = get_sub_field('time_to_end');
                                $context['button_text'] = get_sub_field('button_text');
                                $context['button_url'] = get_sub_field('button_url');
                                $context['tooltips_field'] = get_sub_field('tooltips_field');
                                $context['product'] = get_sub_field('product');
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getD2IronBannerOneTooltip";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'banner_with_subscription') {
                                $context = array();
                                $context['title'] = get_sub_field('bws_title');
                                $context['text'] = get_sub_field('bws_text');
                                $context['subscribed_message'] = get_sub_field('bws_success_text');
                                $context['background_image_big'] = get_sub_field('bws_background_image_big');
                                $context['background_image_small'] = get_sub_field('bws_background_image_small');
                                $category = get_sub_field('bws_category');
                                $context['category'] = $category ? $category->term_id : 2190;
                                $this->callable[$point][$counter]['callback'] = "Standard{$mobile_prefix}Blocks::getBannerWithSubscription";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif ( 'patterns' === get_row_layout() ) {
                                $this->callable[$point][$counter]['callback'] = 'StandardBlocks::getPattern';
                                $this->callable[$point][$counter]['context']  = [ 'id' => get_sub_field( 'pattern_id' ) ];
                            }
                            $this->used_blocks['standard'][get_row_layout()] = $is_mobile_prefix . "block-" . get_row_layout();
                            $counter++;
                        }
                    }
                }
                elseif (get_row_layout() == 'woocommerce_layout') {
                    if (have_rows('woocommerce_blocks')) {
                        while (have_rows('woocommerce_blocks')) {
                            the_row();
                            if (get_row_layout() == 'raid_banner') {
                                $context = array();
                                $context['background_image'] = get_sub_field('raid_banner_background_image') ?? '';
                                $context['timer_label'] = get_sub_field('raid_banner_timer_label_text') ?? '';
                                $context['button_text'] = get_sub_field('raid_banner_button_text') ?? '';
                                $context['banner_id'] = $counter;
                                if ($context['is_wow_banner'] = get_sub_field('is_wow_banner')) {
                                    $wow_banner = get_sub_field('wow_raid_banner_settings');
                                    $context['us'] = [
                                        'first_label' => $wow_banner['wow_raid_banner_us_first_label'] ?? '',
                                        'second_label' => $wow_banner['wow_raid_banner_us_second_label'] ?? '',
                                        'bundle' => $wow_banner['wow_raid_banner_us_bundle'] ?? '',
                                        'products' => array_values(($wow_banner['wow_raid_banner_us_product_repeater'] ?? []) ?: []),
                                    ];
                                    $context['eu'] = [
                                        'first_label' => $wow_banner['wow_raid_banner_eu_first_label'] ?? '',
                                        'second_label' => $wow_banner['wow_raid_banner_eu_second_label'] ?? '',
                                        'bundle' => $wow_banner['wow_raid_banner_eu_bundle'] ?? '',
                                        'products' => array_values(($wow_banner['wow_raid_banner_eu_product_repeater'] ?? []) ?: []),
                                    ];
                                } else {
                                    $default_banner = get_sub_field('default_raid_banner_settings');
                                    $context['default'] = [
                                        'first_label' => $default_banner['default_raid_banner_first_label'] ?? '',
                                        'second_label' => $default_banner['default_raid_banner_second_label'] ?? '',
                                        'bundle' => $default_banner['default_raid_banner_bundle'] ?? '',
                                        'products' => array_values(($default_banner['default_raid_banner_product_repeater'] ?? []) ?: []),
                                    ];
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getRaidBannerBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'our_key_services') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                if (have_rows('items')) {
                                    while (have_rows('items')) {
                                        the_row();
                                        $context['items'][$n]['heading'] = get_sub_field('heading');
                                        if (get_sub_field('image')) {
                                            $context['items'][$n]['image'] = get_sub_field('image');
                                        }
                                        $context['items'][$n]['link_text'] = get_sub_field('link_text');
                                        $context['items'][$n]['link'] = get_sub_field('link');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getOurKeyServicesBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'our_advantages') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                if (have_rows('item')) {
                                    while (have_rows('item')) {
                                        the_row();
                                        if (get_sub_field('image')) {
                                            $context['items'][$n]['image'] = get_sub_field('image');
                                        }
                                        $context['items'][$n]['text'] = get_sub_field('text');
                                        if (get_sub_field('hide_on_mobile') == 1) {
                                            $context['items'][$n]['is_mobile_hidden'] = '__mobile-hidden';
                                        } else {
                                            $context['items'][$n]['is_mobile_hidden'] = '';
                                        }
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getOurAdvantagesBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'popular_services') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getPopularServicesBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'products_slider') {
                                $context = array();
                                $context['heading'] = get_sub_field('heading');
                                $context['products'] = get_sub_field('products');
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getSliderServicesBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'full_width_banner_with_bullets') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                $context['link'] = get_sub_field('link');
                                if (get_sub_field('image')) {
                                    if ($is_mobile && !empty(get_sub_field('image_mobile'))) {
                                        $context['image'] = get_sub_field('image_mobile');
                                    } else {
                                        $context['image'] = get_sub_field('image');
                                    }
                                }
                                if (have_rows('bullets')) {
                                    while (have_rows('bullets')) {
                                        the_row();
                                        $context['bullets'][$n]['text'] = get_sub_field('text');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getFullWidthBannerBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'full_width_banner_with_four_items') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                $context['link'] = get_sub_field('link');
                                $context['image'] = get_sub_field('image');
                                if (have_rows('items')) {
                                    while (have_rows('items')) {
                                        the_row();
                                        $context['bullets'][$n]['heading'] = get_sub_field('heading');
                                        $context['bullets'][$n]['link'] = get_sub_field('link');
                                        $context['bullets'][$n]['image'] = get_sub_field('image');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getFullWidthBannerWithFourItemsBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'full_width_banner_with_five_items') {
                                $context = array();
                                $n = 0;
                                $context['heading'] = get_sub_field('heading');
                                $context['link'] = get_sub_field('link');
                                $context['image'] = get_sub_field('image');
                                if (have_rows('items')) {
                                    while (have_rows('items')) {
                                        the_row();
                                        $context['bullets'][$n]['heading'] = get_sub_field('heading');
                                        $context['bullets'][$n]['link'] = get_sub_field('link');
                                        $context['bullets'][$n]['image'] = get_sub_field('image');
                                        $n++;
                                    }
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getFullWidthBannerWithFiveItemsBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'new_raid_banner') {
                                $context = array();
                                $context['tablet_image'] = get_sub_field('tablet_image');
                                $context['mobile_image'] = get_sub_field('mobile_image');
                                $context['link'] = get_sub_field('link');
                                $context['image'] = get_sub_field('image');
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getNewRaidBanner";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'product_gallery') {
                                $context = array();
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getProductGallery";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'product_content') {
                                $context = array();
                                $context['product_description'] = get_sub_field('product_description');
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getProductDescription";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'eta_content') {
                                $context = array();
                                $context['eta_title'] = get_sub_field('eta_title');
                                $context['eta_text'] = get_sub_field('eta_text');
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getETAblock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'faq_product') {
                                $context = array();
                                $context['faq_title'] = get_sub_field('faq_title');
                                if (have_rows('faq_items')) {
                                    $n = 0;
                                    while (have_rows('faq_items')) {
                                        the_row();
                                        $context['faq_items'][$n]['faq_item_caption'] = get_sub_field('faq_item_caption');
                                        $context['faq_items'][$n]['faq_item_text'] = get_sub_field('faq_item_text');
                                        $n++;
                                    }
                                    $context['faq_count'] = $n;
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getFAQProduct";
                                $this->callable[$point][$counter]['context'] = $context;
                            }
                            elseif (get_row_layout() == 'product_gold') {
                                $context = array();
                                $groups = ['first', 'second', 'third', 'fourth'];
                                $context['gold_icon_image'] = get_sub_field('gold_icon_image');
                                foreach ($groups as $group) {
                                    $context['package_image_' . $group] = get_sub_field('package_' . $group)['package_image_' . $group];
                                    $context['package_title_' . $group] = get_sub_field('package_' . $group)['package_title_' . $group];
                                    $context['package_sum_' . $group] = get_sub_field('package_' . $group)['package_sum_' . $group];
                                    $context['package_bonus_' . $group] = get_sub_field('package_' . $group)['package_bonus_' . $group];
                                    $context['package_color_' . $group] = sanitize_html_class(get_sub_field('package_' . $group)['package_color_' . $group]);
                                }
                                $this->callable[$point][$counter]['callback'] = "Woocommerce{$mobile_prefix}Blocks::getProductGoldBlock";
                                $this->callable[$point][$counter]['context'] = $context;
                            }

                            // End woocommerce_layout
                            $layout = get_row_layout();
                            if ($layout == 'products_slider') {
                                $this->used_blocks['woocommerce'][$layout] = $is_mobile_prefix . "block-" . $layout;
                                $layout = 'popular_services';
                            }
                            $this->used_blocks['woocommerce'][$layout] = $is_mobile_prefix . "block-" . $layout;
                            $counter++;
                        }
                    }
                }
            }
        }
    }
}