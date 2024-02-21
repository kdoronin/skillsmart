<?php


class CartEndpoint extends RestAbstractController {

    protected static $_instance = null;

    public function registerRestEndpoint(WP_REST_Request $request): WP_REST_Response
    {
        $this->passRawInputData();

        $variation = [];
        $cart_item_data = [];
        $attribute_check = true;
        foreach ($_POST as $k => $v) {
            if (str_starts_with($k, 'attribute_pa_') && $k !== 'attribute_pa_game') {
                $variation[$k] = $v;
                if (!$v) {
                    $attribute_check = false;
                }
            } elseif ($k === 'quantity') {
                continue;
            } else {
                $cart_item_data[$k] = $v;
            }
            if ($k === 'attribute_pa_choose-your-region') {
                $cart_item_data[$k] = $v;
            }
        }

        $cart_item_key = null;
        $cart = WC()->cart;
        $passed = apply_filters('wv_add_to_cart_validation', $_POST);

        if ($this->isDataForAddToCart($cart, $passed, $_POST) && $attribute_check && $this->validateCheckboxes($_POST['variation_id'], $cart_item_data)) {
            if ($this->isItemForSingleAdding($_POST, $cart)) {
                $cart_item_data['unique_key'] = $cart->generate_cart_id($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], array_merge($variation, ['uniqid' => uniqid()]));
            }
            $cart_item_key = $cart->add_to_cart($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], $variation, $cart_item_data);
        }

        $notices = Notice::instance();
        switch (true):
            case !$passed || empty($cart->get_cart_item($cart_item_key)):
                $invalidEventMessage = __('Sorry, the selected time slot is no longer available. Please choose another time slot on the product page.', 'wv_lang');
                $notices->addNotice($invalidEventMessage, 'notice');
                $result['status'] = 409;
                $result['notices'] = $notices->getNotices();
                break;
            case !isset($cart_item_key):
                $notices->addNotice('Bad request: required data not received', 'error');
                $result['status'] = 400;
                $result['notices'] = $notices->getNotices();
                break;
            case $passed:
            default:
                $notices->addNotice('Product added to the cart');
                $result = [
                    'status' => 200,
                    'data' => [
                        'itemsInCart' => $cart->get_cart_contents_count(),
                        'cartTotal' => floatval($cart->get_total('int')),
                        'productKey' => $cart_item_key,
                    ],
                ];
                break;
        endswitch;

        $result['notices'] = $notices->getNotices();
        if (false === $request['debug']) {
            return new WP_REST_Response($result, $result['status']);
        } else {
            $result['data']['cart_contents'] = $cart->get_cart_contents();
            $result['data']['cart'] = $cart;
            die(print_r($result, 1));
        }
    }
}