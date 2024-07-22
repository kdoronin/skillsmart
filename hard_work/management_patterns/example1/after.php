<?php

use AbstractController;

class CustomFields extends AbstractController {
    protected $callable = [];
    protected $used_blocks = [];

    public function getFields(): void
    {
        if ((function_exists('is_portal') && is_portal()) || is_checkout()) {
            return;
        }

        global $mobile_prefix, $is_mobile;

        $is_mobile_prefix = $is_mobile ? $mobile_prefix . '-' : '';
        $identifier = $this->getIdentifier();
        $identifier = apply_filters('custom_fields_page_identifier', $identifier);

        $points = ['top', 'bottom'];
        $blocks = $this->getBlockTypes();

        foreach ($points as $point) {
            $this->processPoint($point, $identifier, $is_mobile_prefix, $blocks);
        }
    }

    private function getIdentifier(): string
    {
        if (is_single() || is_page()) {
            global $post;
            return $post->ID;
        }
        return "product_cat_" . get_queried_object_id();
    }

    private function getBlockTypes(): array
    {
        return [
            'banner' => 'getBanner',
            'choose_your_game' => 'getChooseYourGame',
            'mobile_advantages' => 'getMobileAdvantages',
            'seo_text' => 'getSeoText',
            'contact_block' => 'getContactBlock',
            'big_customer_reviews_block' => 'getBigReviewsBlock',
            'faq_block' => 'getFAQBlock',
            'about_us_block' => 'getAboutUsBlock',
            'service_request' => 'getServiceRequestBanner',
            'black_friday_big' => 'getBigBlackFridayBanner',
            'black_friday_small' => 'getSmallBlackFridayBanner',
            'destiny_2_weekly_banner' => 'getD2WeeklyBanner',
            'destiny_2_three_weapons_banner' => 'getD2ThreeWeaponsBanner',
            'destiny_2_iron_banner' => 'getD2IronBanner',
            'destiny_2_iron_banner_one_tooltip' => 'getD2IronBannerOneTooltip',
            'banner_with_subscription' => 'getBannerWithSubscription',
            'patterns' => 'getPattern',
            'raid_banner' => 'getRaidBannerBlock',
            'our_key_services' => 'getOurKeyServicesBlock',
            'our_advantages' => 'getOurAdvantagesBlock',
            'popular_services' => 'getPopularServicesBlock',
            'products_slider' => 'getSliderServicesBlock',
            'full_width_banner_with_bullets' => 'getFullWidthBannerBlock',
            'full_width_banner_with_four_items' => 'getFullWidthBannerWithFourItemsBlock',
            'full_width_banner_with_five_items' => 'getFullWidthBannerWithFiveItemsBlock',
            'new_raid_banner' => 'getNewRaidBanner',
            'product_gallery' => 'getProductGallery',
            'product_content' => 'getProductDescription',
            'eta_content' => 'getETAblock',
            'faq_product' => 'getFAQProduct',
            'product_gold' => 'getProductGoldBlock'
        ];
    }

    private function processPoint(string $point, string $identifier, string $is_mobile_prefix, array $blocks): void
    {
        $pointProcessor = PointFactory::createPoint($point, $blocks);
        $processedBlocks = $pointProcessor->processBlocks($identifier, $is_mobile_prefix);

        foreach ($processedBlocks as $counter => $block) {
            $this->callable[$point][$counter] = $block;
            $layout = $block['context']['layout'];
            $this->used_blocks['standard'][$layout] = $is_mobile_prefix . "block-" . $layout;
        }
    }
}