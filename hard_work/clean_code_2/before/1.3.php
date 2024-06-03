<?php

/**
 * Returns whether field is visible, based on conditions
 * This is called by pewc_is_field_required during cart validation
 * @param $id					Field ID
 * @param $item				Current item
 * @param $items			Product items
 * @param $product_id	Product ID
 * @param $posted			$_POST
 * @return Boolean		True if field is visible, false if field is hidden
 */
function pewc_get_conditional_field_visibility( $id, $item, $items, $product_id, $posted=array(), $variation_id=null, $cart_item_data=array(), $quantity=0, $group_id=false, $group=false ) {

    if( empty( $posted ) ) {
        $posted = $_POST;
    }

    // Check if the field is in a hidden group
    if( ! pewc_is_group_visible( $group_id, $group, $posted ) ) {
        return false;
    }

    $cart_item = pewc_get_cart_item_by_extras( $product_id, $variation_id, $cart_item_data );
    $line_total = isset( $cart_item['line_total'] ) ? $cart_item['line_total'] : false;

    /**
     * If $line_total hasn't yet been calculated, use the price_with_extras value
     * @since 3.7.13
     */
    if( ! $line_total ) {
        $line_total =	isset( $cart_item_data['product_extras']['price_with_extras'] ) ? $cart_item_data['product_extras']['price_with_extras'] * $quantity : false;
    }

    // First, does the field have any conditions?
    $conditions = pewc_get_field_conditions( $item, $product_id );

    if( empty( $conditions ) ) {

        // No conditions so the field must be visible
        $is_visible = true;

    } else {

        // Check the rules for the conditions
        $rules = pewc_get_field_conditional( $item, $product_id );

        // Was the field initially visible?
        $is_visible = true;
        if( $rules['action'] == 'show' ) {
            // Field is hidden
            $is_visible = false;
        }

        // we use this to determine if we have an incomplete condition
        $incomplete_condition = true;

        // We've got conditions so establish whether the field is currently visible or not
        if( $rules['match'] == 'all' ) {

            // If all conditions need to obtain
            $rules_obtain = true;

            foreach( $conditions as $condition ) {

                $field = isset( $condition['field'] ) ? $condition['field'] : '';

                if ( empty( $field ) || 'not-selected' == $field ) {
                    // no field is selected, so skip
                    continue;
                }

                // this condition might be complete and valid
                $incomplete_condition = false;

                $rule = isset( $condition['rule'] ) ? $condition['rule'] : '';

                // $value = isset( $condition['value'] ) ? $condition['value'] : '';
                // Switched to key since 2.4.5
                $value = isset( $condition['key'] ) ? $condition['key'] : '';

                // We need to get the field type of the field that triggers the condition
                $condition_field_id = explode( '_', $field );
                $condition_field_id = isset( $condition_field_id[3] ) ? $condition_field_id[3] : false;
                $field_type = isset( $items[$condition_field_id]['field_type'] ) ? $items[$condition_field_id]['field_type'] : false;

                // Use this variable for fields that have arrays as values, e.g. checkbox groups and products
                $posted_field = isset( $posted[$field] ) ? $posted[$field] : false;
                $posted_field = isset( $posted[$field. '_child_product'] ) ? $posted[$field. '_child_product'] : $posted_field;

                // Ensure we remove any backslashes, apostrophes, etc
                if( is_array( $posted_field ) ) {
                    foreach( $posted_field as $pf_key=>$pf_value ) {
                        $posted_field[$pf_key] = pewc_keyify_field( $pf_value );
                    }
                } else {
                    $posted_field = pewc_keyify_field( $posted_field );
                }

                // Check each condition
                if( ! empty( $product_id) && ! empty( $variation_id ) && 'pa_' === substr( $field, 0, 3 ) ) {

                    // since 3.11.9. Attribute in condition
                    $rules_obtain = pewc_attribute_in_condition_rule_obtained( $product_id, $variation_id, $field, $rule, $value );

                    if ( ! $rules_obtain ) {
                        break;
                    }

                } else if( $rule == 'is' ) {

                    // $posted[$field] is the value of the field on which the condition depends
                    if( $field_type == 'checkbox' && ! isset( $posted[$field] ) ) {

                        $rules_obtain = false;
                        break;

                    } else if( $posted_field && is_array( $posted_field ) && ! in_array( $value, $posted_field ) ) {

                        // Fields which return an array for their value, e.g. radio groups
                        $rules_obtain = false;
                        break; // Restored this in 3.7.2 to ensure fields with multiple conditions were getting hidden correctly

                    } else if( isset( $posted_field ) && ! is_array( $posted_field ) && $field_type != 'checkbox' && $posted_field != $value ) {

                        // Fields which don't return an array of values
                        $rules_obtain = false;
                        break;

                    }

                } else if( $rule == 'is-not' ) {

                    if( $posted_field && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

                        // Fields which return an array for their value, e.g. radio groups
                        $rules_obtain = false;
                        break;

                    } else if( isset( $posted[$field] ) && $posted[$field] == $value ) {
                        $rules_obtain = false;
                        break;
                    }

                } else if( $rule == 'contains' ) {

                    if( $posted_field && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

                        $rules_obtain = true;

                    } else {

                        $rules_obtain = false;

                    }

                } else if( $rule == 'cost-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads == $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity == $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total == $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else {

                        // Probably calculation

                        if( $posted_field && $posted_field == $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    }

                } else if( $rule == 'cost-greater' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads > $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity > $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total > $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else {

                        // Probably calculation

                        if( $posted_field && $posted_field > $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    }

                } else if( $rule == 'cost-less' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads < $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity < $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'cost' ) {

                        if( $line_total < $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else {

                        // Probably calculation
                        if( $posted_field && $posted_field < $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    }

                } else if( $rule == 'greater-than-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads >= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity >= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'cost' ) {

                        if( $line_total >= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else {

                        // Probably calculation
                        if( $posted_field && $posted_field >= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    }

                } else if( $rule == 'less-than-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads <= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity <= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'cost' ) {

                        if( $line_total <= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;

                        }

                    } else {

                        // Probably calculation
                        if( $posted_field && $posted_field <= $value ) {

                            $rules_obtain = true;

                        } else {

                            $rules_obtain = false;
                            break;

                        }

                    }

                } //

            }

        } else if( $rules['match'] == 'any' ) {

            // If any condition needs to obtain
            $rules_obtain = false;

            foreach( $conditions as $condition ) {

                $field = isset( $condition['field'] ) ? $condition['field'] : '';

                if ( empty( $field ) || 'not-selected' == $field ) {
                    // no field is selected, so skip
                    continue;
                }

                // this condition might be complete and valid
                $incomplete_condition = false;

                $rule = isset( $condition['rule'] ) ? $condition['rule'] : '';

                // $value = isset( $condition['value'] ) ? $condition['value'] : '';
                // Switched to key since 2.4.5
                $value = $key = isset( $condition['key'] ) ? $condition['key'] : '';

                // We need to get the field type of the field that triggers the condition
                $condition_field_id = explode( '_', $field );
                $condition_field_id = isset( $condition_field_id[3] ) ? $condition_field_id[3] : false;
                $field_type = isset( $items[$condition_field_id]['field_type'] ) ? $items[$condition_field_id]['field_type'] : false;

                // Use this variable for fields that have arrays as values, e.g. checkbox groups and products
                $posted_field = isset( $posted[$field] ) ? $posted[$field] : false;
                $posted_field = isset( $posted[$field. '_child_product'] ) ? $posted[$field. '_child_product'] : $posted_field;

                // Ensure we remove any backslashes, apostrophes, etc
                if( is_array( $posted_field ) ) {
                    foreach( $posted_field as $pf_key=>$pf_value ) {
                        $posted_field[$pf_key] = pewc_keyify_field( $pf_value );
                    }
                } else {
                    $posted_field = pewc_keyify_field( $posted_field );
                }

                // Check each condition
                if( ! empty( $product_id) && ! empty( $variation_id ) && 'pa_' === substr( $field, 0, 3 ) ) {

                    // since 3.11.9. Attribute in condition
                    $rules_obtain = pewc_attribute_in_condition_rule_obtained( $product_id, $variation_id, $field, $rule, $value );

                    if ( $rules_obtain ) {
                        break;
                    }

                } else if( $rule == 'is' ) {

                    if( is_array( $posted_field ) && in_array( $key, $posted_field ) ) {
                        $rules_obtain = true;
                        break;
                    } else if( isset( $posted_field ) && $posted_field == $key ) {
                        $rules_obtain = true;
                        // break;
                    }

                } else if( $rule == 'is-not' ) {

                    if( is_array( $posted_field ) && ! in_array( $key, $posted_field ) ) {
                        $rules_obtain = true;
                        break;
                    } else if( $posted_field != $key ) {
                        $rules_obtain = true;
                        // break;
                    }

                } else if( $rule == 'contains' ) {

                    if( isset( $posted_field ) && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

                        $rules_obtain = true;
                        break;

                    } else {

                        $rules_obtain = false;
                        // break;

                    }

                } else if( $rule == 'cost-greater' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads > $value ) {

                            $rules_obtain = true;
                            break;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity > $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total > $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else {

                        // Calculation or number field
                        if( $posted_field > $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    }

                } else if( $rule == 'cost-less' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads < $value ) {

                            $rules_obtain = true;
                            break;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity < $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total < $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else {

                        // Calculation or number field
                        if( $posted_field < $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    }

                } else if( $rule == 'cost-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads == $value ) {

                            $rules_obtain = true;
                            break;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity == $value ) {

                            $rules_obtain = true;
                            break;

                            // } else {
                            //
                            // 	$rules_obtain = false;
                            // 	break;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total == $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else {

                        // Cost
                        if( $posted_field == $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    }

                } else if( $rule == 'greater-than-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads >= $value ) {

                            $rules_obtain = true;
                            break;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity >= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total >= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else {

                        // Calculation or number field
                        if( $posted_field >= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    }

                } else if( $rule == 'less-than-equals' ) {

                    if( $field_type == 'upload' ) {

                        $number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

                        if( $number_uploads <= $value ) {

                            $rules_obtain = true;
                            break;

                        } else {

                            $rules_obtain = false;

                        }

                    } else if( $field == 'quantity' ) {

                        // Quantity
                        if( $quantity <= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else if( $field == 'cost' ) {

                        // Cost
                        if( $line_total <= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    } else {

                        // Calculation or number field
                        if( $posted_field <= $value ) {

                            $rules_obtain = true;
                            break;

                        }

                    }

                }

            }

        }


        if ( $incomplete_condition ) {

            // since 3.12.1. Incomplete conditions are always visible
            $is_visible = true;

        } else if( $rules['action'] == 'show' ) {

            $is_visible = $rules_obtain;

        } else {

            $is_visible = ! $rules_obtain;

        }

        // return $is_visible;

    }

    return apply_filters( 'pewc_get_conditional_field_visibility', $is_visible, $id, $item, $items, $product_id, $variation_id, $cart_item_data, $group_id, $group );

}