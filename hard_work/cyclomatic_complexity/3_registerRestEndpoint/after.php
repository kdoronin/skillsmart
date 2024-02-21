<?php
/**
 * Исходная ЦС метода – 17
 * Целевая ЦС метода – 8
 * Итоговая ЦС метода – 8
 *
 * Анализ ситуации до:
 * 1. Первый кусок метода, который бросается в глаза – это формирование Notice. Его явно необходимо вынести за пределы
 *    метода, а также переработать для снижения общей ЦС.
 * 2. Также в отдельный метод нужно перенести форматирование данных из массива $_POST в массив с элементами корзины
 * 3. Третий блок – это возвращение результата, в котором используется else. Его также стоит унифицировать
 *
 * Результат работы:
 * 1. Очень понравился процесс переработки условий в самом начале метода. Фильтры – это очень удобный инструмент
 * 2. Также в данном методе считаю оправданным перенос части сложносочинённых условий во вспомогательный метод
 * 3. Переделка условий switch-case, скорее всего, почти ничего не дала. Похоже, что даже ЦС не уменьшилась
 * 4. Ещё один метод, в котором проблема – это неправильное проектирование. Сложно всё исправить до состояния "хорошо"
 *
 * Вывод:
 * Считаю, что модулю в целом нужна гораздо более осмысленная архитектура. С продумыванием всех частей модуля
 */

class CartEndpoint extends RestAbstractController
{

    protected static $_instance = null;

    private array $result = [];


    public function registerRestEndpoint(WP_REST_Request $request): WP_REST_Response
    {
        $this->passRawInputData();
        //Заменил цикл с условиями на фильтры. Результат тот же, ЦС снизилась
        $variation       = array_filter($_POST, function ($k) {
            return str_starts_with($k, 'attribute_pa_') && $k !== 'attribute_pa_game';
        }, ARRAY_FILTER_USE_KEY);
        $attribute_check = ! in_array(false, $variation);
        $cart_item_data  = array_filter($_POST, function ($k) use ($variation) {
            return ! array_key_exists($k, $variation) && $k !== 'quantity';
        }, ARRAY_FILTER_USE_KEY);
        if (isset($_POST['attribute_pa_choose-your-region'])) {
            $cart_item_data['attribute_pa_choose-your-region'] = $_POST['attribute_pa_choose-your-region'];
        }

        $cart_item_key                = null;
        $cart                         = WC()->cart;
        $passed                       = apply_filters('wv_add_to_cart_validation', $_POST);
        $notices                      = Notice::instance();
        $cart_item_data['unique_key'] = $this->addUniqueKey($cart, $_POST, $variation, $cart_item_data);

        if ($this->shouldItemBeAddedToCart(
            $cart,
            $passed,
            $_POST,
            $attribute_check,
            $cart_item_data
        )) { // Монструозное условие назвал понятным словом и вынес в отдельный метод
            $cart_item_key = $cart->add_to_cart(
                $_POST['product_id'],
                $_POST['quantity'],
                $_POST['variation_id'],
                $variation,
                $cart_item_data
            );
            $notices->addNotice('Product added to the cart');
            $this->addResult([
                'status' => 200,
                'data'   => [
                    'itemsInCart' => $cart->get_cart_contents_count(),
                    'cartTotal'   => floatval($cart->get_total('int')),
                    'productKey'  => $cart_item_key,
                ],
            ]);
        }
        $conditions = [
            [
                'condition' => function () use ($passed, $cart, $cart_item_key) {
                    return ! $passed || empty($cart->get_cart_item($cart_item_key));
                },
                'action'    => function () use ($notices, &$result) {
                    $invalidEventMessage = __(
                        'Sorry, the selected time slot is no longer available. Please choose another time slot on the product page.',
                        'wv_lang'
                    );
                    $notices->addNotice($invalidEventMessage, 'notice');
                    $result['status'] = 409;
                }
            ],
            [
                'condition' => function () use ($cart_item_key) {
                    return ! isset($cart_item_key);
                },
                'action'    => function () use ($notices, &$result) {
                    $notices->addNotice('Bad request: required data not received', 'error');
                    $result['status'] = 400;
                }
            ]
        ];
        foreach ($conditions as $item) {
            if ($item['condition']()) {
                $item['action']();
                break;
            }
        }
        // Удалил код для дебага. Не подходящая организация дебага.
        $result['notices'] = $notices->getNotices();
        $result['data']['cart_contents'] = $cart->get_cart_contents();
        $result['data']['cart']          = $cart;
        die(print_r($result, 1));
    }

    private function addUniqueKey($cart, $post, $variation, $cart_item_data)
    {
        if ( ! $this->isItemForSingleAdding($post, $cart)) {
            return $cart_item_data['unique_key'];
        }

        return $cart->generate_cart_id(
            $post['product_id'],
            $post['quantity'],
            $post['variation_id'],
            array_merge($variation, ['uniqid' => uniqid()])
        );
    }

    private function addResult($result)
    {
        if ( ! empty($this->result)) {
            return;
        }
        $this->result = $result;
    }

    private function shouldItemBeAddedToCart($cart, $passed, $post, $attribute_check, $cart_item_data): bool
    {
        return $this->isDataForAddToCart($cart, $passed, $post) && $attribute_check && $this->validateCheckboxes(
                $post['variation_id'],
                $cart_item_data
            );
    }
}