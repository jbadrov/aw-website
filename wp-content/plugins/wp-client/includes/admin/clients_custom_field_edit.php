<?php
if ( !current_user_can( 'wpc_admin' ) && !current_user_can( 'administrator' ) ) { do_action( 'wp_client_redirect', get_admin_url() . 'admin.php?page=wpclient_clients' ); } add_thickbox(); wp_register_script( 'wpc-thickbox-popup-js', $this->plugin_url . 'js/thickbox_popup.js', false, WPC_CLIENT_VER ); wp_enqueue_script( 'wpc-thickbox-popup-js', false, array('jquery'), WPC_CLIENT_VER, true ); $error = ""; if ( isset( $_POST['submit'] ) ) { $custom_field_name = ( isset( $_POST['custom_field']['name'] ) ) ? $_POST['custom_field']['name'] : ''; $custom_field_name = strtolower( $custom_field_name ); $custom_field_name = str_replace( ' ', '_', $custom_field_name ); $custom_field_name = preg_replace( '/[^a-z0-9_]/i', '', $custom_field_name ); $wpc_custom_fields = $this->cc_get_settings( 'custom_fields' ); if ( empty( $custom_field_name ) ) { $error .= __( 'A Custom Field Name is required.<br/>', WPC_CLIENT_TEXT_DOMAIN ); } elseif ( isset( $_GET['add'] ) && '1' == $_GET['add'] ) { $custom_field_name = 'wpc_cf_' . $custom_field_name; if ( isset( $wpc_custom_fields[$custom_field_name] ) ) $error .= sprintf( __( 'A Custom Field with this name "%s" already exist already.<br/>', WPC_CLIENT_TEXT_DOMAIN ), $custom_field_name ); } if ( empty( $_POST['custom_field']['type'] ) ) $error .= __( 'A Custom Field Type is required.<br/>', WPC_CLIENT_TEXT_DOMAIN ); if ( empty( $error ) ) { $custom_field = $_POST['custom_field']; unset( $custom_field['name'] ); $custom_field['display'] = ( isset( $custom_field['display'] ) && '1' == $custom_field['display'] ) ? '1' : '0'; $custom_field['display_register'] = ( isset( $custom_field['display_register'] ) && '1' == $custom_field['display_register'] ) ? '1' : '0'; $custom_field['display_user'] = ( isset( $custom_field['display_user'] ) && '1' == $custom_field['display_user'] ) ? '1' : '0'; $custom_field['required'] = ( isset( $custom_field['required'] ) && '1' == $custom_field['required'] ) ? '1' : '0'; $custom_field['field_readonly'] = ( isset( $custom_field['field_readonly'] ) && '1' == $custom_field['field_readonly'] ) ? '1' : '0'; $custom_field['track_number'] = ( !empty( $_POST['track_number'] ) && (int)$_POST['track_number'] ) ? (int)$_POST['track_number'] : '0'; $wpc_custom_fields[$custom_field_name] = $custom_field; do_action( 'wp_client_settings_update', $wpc_custom_fields, 'custom_fields' ); do_action( 'wp_client_redirect', 'admin.php?page=wpclient_clients&tab=custom_fields&msg=a' ); exit(); } } if ( isset( $_REQUEST['custom_field'] ) ) { $custom_field = $_REQUEST['custom_field']; } elseif ( isset( $_GET['edit'] ) && '' != $_GET['edit'] ) { $wpc_custom_fields = $this->cc_get_settings( 'custom_fields' ); if ( isset( $wpc_custom_fields[$_GET['edit']] ) ) { $custom_field = $wpc_custom_fields[$_GET['edit']]; $custom_field['name'] = $_GET['edit']; unset( $wpc_custom_fields ); } else { do_action( 'wp_client_redirect', 'admin.php?page=wpclient_clients&tab=custom_fields&msg=n' ); exit(); } } if ( isset( $_GET['add'] ) && '1' == $_GET['add'] ) $title_text = __( 'Add Custom Field', WPC_CLIENT_TEXT_DOMAIN ); else $title_text = __( 'Update Custom Field', WPC_CLIENT_TEXT_DOMAIN ); ?>

<style type="text/css">

.wrap input[type=text] {
    width:400px;
}

.wrap input[type=password] {
    width:400px;
}

</style>




<script type="text/javascript" language="javascript">

    jQuery( document ).ready( function() {
        jQuery(window).bind( 'load', field_type_options );
        jQuery( '#type' ).bind( 'change', field_type_options );

        // public field values initiation
        function field_type_options() {
            var type = jQuery( '#type' ).val();
            jQuery( '.ct-field-type-options' ).hide();
            jQuery( '#field_title_box' ).hide();
            jQuery( '#field_description_box' ).hide();
            jQuery( '.ct-field-hiden-value' ).hide();
            jQuery( '.mask_field' ).hide();
            if ( type === 'radio' || type === 'selectbox' || type === 'multiselectbox' || type === 'checkbox' ) {
                jQuery( '.ct-field-type-options' ).show();
                jQuery( '#field_title_box' ).show();
                jQuery( '#field_description_box' ).show();
            }
            else if ( type === 'text' || type === 'datepicker' || type === 'cost' || type === 'textarea' ) {
                if ( type === 'text' ) {
                    jQuery( '.mask_field' ).show();
                }
                jQuery( '#field_title_box' ).show();
                jQuery( '#field_description_box' ).show();
            } else if ( type === 'hidden' ) {
                jQuery( '.ct-field-hiden-value' ).show();
            }
        }



        jQuery('#wpc_add_many_options').click( function() {
            jQuery('#wpc_block_for_many_options').wpc_thickbox_popup({
                'title' : '<?php _e( 'Add Multiple Options', WPC_CLIENT_TEXT_DOMAIN ); ?>'
            });
        });

        jQuery(document).on('click', '.cancel_add_option', function() {
            tb_remove();
            jQuery('#wpc_textarea_many_options').val('');
            return false;
        });

        jQuery(document).on('click', '.submit_add_options', function() {
            var data = jQuery('#wpc_textarea_many_options').val();
            if ( 'undefined' != typeof( data ) ) {
                var arr = data.split('\n');
                var count = parseInt(jQuery('input[name="track_number"]').val(), 10) + 1;
                jQuery(arr).each( function( index, val ) {
                    jQuery( '.ct-field-add-option' ).trigger( 'click' );
                    jQuery( 'input[name="custom_field\\[options\\]\\[' + count + '\\]"]' ).val( val );
                    count++;
                });
            }
            /*if( user_id instanceof Array ) {
                if( user_id.length ) {
                    var item_string = '';
                    user_id.forEach(function( item, key ) {
                        item_string += '&item[]=' + item;
                    });
                    window.location = '<?php echo admin_url(); ?>admin.php?page=wpclient_clients&tab=archive&action=' + action + item_string + '&_wpnonce=' + nonce + '&' + jQuery('#delete_user_settings').serialize() + '&_wp_http_referer=' + jQuery('input[name=_wp_http_referer]').val();
                }
            }  */
            tb_remove();
            jQuery('#wpc_textarea_many_options').val('');
            return false;
        });

        // custom fields remove options
        jQuery('#wpc_client_custom_filed_form').live('submit', function() {
            if ( jQuery( '#type' ).val() === 'hidden' ) {
                jQuery( '.ct-field-type-options' ).remove();
                jQuery( '#field_title_box' ).remove();
                jQuery( '#field_description_box' ).remove();
            } else {
                jQuery( '.ct-field-hiden-value' ).remove();
            }
        });


        // custom fields add options
        jQuery('.ct-field-add-option').click(function() {
            var count = parseInt(jQuery('input[name="track_number"]').val(), 10) + 1;

            jQuery('.ct-field-additional-options').append(function() {


                jQuery('input[name="track_number"]').val(count);
                //var type = jQuery('#type').val();
                var input_type = 'radio';
                /*if ( 'checkbox' == type || 'multiselectbox' == type ) {
                    input_type = 'checkbox';
                } */

                return '<p><?php _e( 'Option', WPC_CLIENT_TEXT_DOMAIN ) ?> ' + count + ': ' +
                            '<input type="text" name="custom_field[options][' + count + ']"> ' +
                            '<input type="' + input_type + '" class="wpc_default_value" value="' + count + '" name="custom_field[default_option]"> ' +
                            '<?php _e( 'Default Value', WPC_CLIENT_TEXT_DOMAIN ) ?> ' +
                            '<a href="javascript:;" class="ct-field-delete-option">[x]</a>' +
                        '</p>';
            });

            jQuery( 'input[name="custom_field[options][' + count + ']"]' ).focus();
        });


        // custom fields remove options
        jQuery('.ct-field-delete-option').live('click', function() {
            jQuery(this).parent().remove();
        });

        jQuery('#mask_type').change(function() {
            var type = jQuery(this).val();
            if( 'custom' != type ) {
                var mask = jQuery(this).children('option:selected').data('mask');
                jQuery('#mask_value').val( mask );
            }
        }).change();

        jQuery('#mask_value').keypress(function() {
            jQuery('#mask_type').val('custom');
        });
    });

</script>

<div style="" class='wrap'>

    <?php echo $this->get_plugin_logo_block() ?>

    <div class="wpc_clear"></div>

    <div id="container23">
        <ul class="menu">
            <?php echo $this->gen_tabs_menu( 'clients' ) ?>
        </ul>
        <span class="wpc_clear"></span>

        <div class="content23 custom_fields">
            <h2><?php echo $title_text ?></h2>
            <br>
            <div>
                <div id="message" class="updated wpc_notice fade" <?php echo ( empty( $error ) )? 'style="display: none;" ' : '' ?> ><?php echo $error; ?></div>
               <p class="description">
               <?php _e( 'These fields will show on the self registration form ( by default, found at /client-registration )
You can also use the data entered in these fields in your HUBs, Portal Pages & Emails using placeholders.
The default format for these placeholders will be {wpc_cf_xxxxx} with xxxxx being replaced with the slug you enter in the "Field Slug" field ', WPC_CLIENT_TEXT_DOMAIN ) ?>
               </p>
                <form action="" method="post" name="wpc_client_custom_filed_form" id="wpc_client_custom_filed_form">
                    <?php if ( isset( $_GET['edit'] ) && '' != $_GET['edit'] ): ?>
                    <input type="hidden" name="custom_field[name]" value="<?php echo ( isset( $custom_field['name'] ) ) ? $custom_field['name'] : '' ?>" />
                    <?php endif; ?>

                    <table class="form-table">
                        <tr>
                            <th>
                                <label for="name"><?php _e( 'Field Slug', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="required">(<?php _e( 'required', WPC_CLIENT_TEXT_DOMAIN ) ?>)</span></label>
                            </th>
                            <td>
                                <?php if ( isset( $_GET['edit'] ) && '' != $_GET['edit'] ): ?>
                                    <input type="text" disabled value="<?php echo ( isset( $custom_field['name'] ) ) ? $custom_field['name'] : '' ?>" />
                                    <br>
                                    <span class="description"><?php _e( "Can't be changed.", WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                                <?php else: ?>
                                    wpc_cf_<input type="text" name="custom_field[name]" id="name" style="width: 372px;" value="<?php echo ( isset( $custom_field['name'] ) ) ? $custom_field['name'] : '' ?>" />
                                    <br>
                                    <span class="description"><?php _e( 'The name used to identify the custom field. Should consist only of these characters "a-z" and the underscore symbol "_" <br> - (not displayed on the form).', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr id="field_title_box">
                            <th>
                                <label for="title"><?php _e( 'Field Title', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                                <input type="text" name="custom_field[title]" id="title" value="<?php echo ( isset( $custom_field['title'] ) ) ? $custom_field['title'] : '' ?>" />
                                <br>
                                <span class="description"><?php _e( 'The title of the custom field (displayed on the form).', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                            </td>
                        </tr>
                        <tr id="field_description_box">
                            <th>
                                <label for="description"><?php _e( 'Field Description', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                               <textarea name="custom_field[description]" rows="3" cols="69" id="description" ><?php echo ( isset( $custom_field['description'] ) ) ? $custom_field['description'] : '' ?></textarea>
                               <br>
                               <span class="description"><?php _e( 'Description for the custom field (displayed on the form).', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                            </td>
                        </tr>
                        <tr id="field_relate_to">
                            <th>
                                <label for="relate_to"><?php _e( 'Relate to User Meta', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                               <input type="text" name="custom_field[relate_to]" id="relate_to" value="<?php echo ( isset( $custom_field['relate_to'] ) ) ? $custom_field['relate_to'] : '' ?>" />
                               <br>
                               <span class="description"><?php _e( 'You can relate this field value with User Meta. Example: first_name', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                            </td>
                        </tr>
                        <tr id="field_nature">
                            <th>
                                <label for="nature"><?php _e( 'Field For', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                               <select name="custom_field[nature]" id="nature" ><?php echo ( isset( $custom_field['nature'] ) ) ? $custom_field['nature'] : '' ?>
                                    <option value="client" <?php echo ( !isset( $custom_field['nature'] ) || 'client' == $custom_field['nature'] ) ? 'selected' : '' ?>><?php echo $this->custom_titles['client']['p'] ?></option>
                                    <option value="staff" <?php echo ( isset( $custom_field['nature'] ) && 'staff' == $custom_field['nature'] ) ? 'selected' : '' ?>><?php echo $this->custom_titles['staff']['p'] ?></option>
                               </select>
                               <br>
                               <span class="description"><?php _e( 'Select users of the custom field.', WPC_CLIENT_TEXT_DOMAIN ) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="type"><?php _e( 'Field Type', WPC_CLIENT_TEXT_DOMAIN ) ?> <span class="required">(<?php _e( 'required', WPC_CLIENT_TEXT_DOMAIN) ?>)</span></label>
                            </th>
                            <td>
                                <select name="custom_field[type]" id="type">
                                    <option value="text" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'text' ) ? 'selected' : '' ?>><?php _e( 'Text Box', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="datepicker" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'datepicker' ) ? 'selected' : '' ?>><?php _e( 'Datepicker', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="cost" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'cost' ) ? 'selected' : '' ?>><?php _e( 'Cost', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="textarea" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'textarea' ) ? 'selected' : '' ?>><?php _e( 'Multi-line Text Box', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="radio" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'radio' ) ? 'selected' : '' ?>><?php _e( 'Radio Buttons', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="checkbox" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'checkbox' ) ? 'selected' : '' ?>><?php _e( 'Checkboxes', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="selectbox" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'selectbox' ) ? 'selected' : '' ?>><?php _e( 'Select Box', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="multiselectbox" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'multiselectbox' ) ? 'selected' : '' ?>><?php _e( 'Multi Select Box', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                    <option value="hidden" <?php echo ( isset( $custom_field['type'] ) && $custom_field['type'] == 'hidden' ) ? 'selected' : '' ?>><?php _e( 'Hidden Field', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                </select>
                                <br>
                                <span class="description"><?php _e( 'Select type of the custom field.', WPC_CLIENT_TEXT_DOMAIN ) ?></span>

                                <div class="ct-field-type-options">
                                    <h4><?php _e( 'Fill in the options for this field', WPC_CLIENT_TEXT_DOMAIN ) ?>:</h4>
                                    <p style="float: left;"><?php _e( 'Order By', WPC_CLIENT_TEXT_DOMAIN ) ?>:
                                        <select name="custom_field[sort_order]">
                                            <option value="default" <?php echo ( isset( $custom_field['sort_order'] ) && 'default' == $custom_field['sort_order'] ) ? 'selected' : '' ?> ><?php _e( 'Order Entered', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                            <option value="asc" <?php echo ( isset( $custom_field['sort_order'] ) && 'asc' == $custom_field['sort_order'] ) ? 'selected' : '' ?> ><?php _e( 'Name - Ascending', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                            <option value="desc" <?php echo ( isset( $custom_field['sort_order'] ) && 'desc' == $custom_field['sort_order'] ) ? 'selected' : '' ?> ><?php _e( 'Name - Descending', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                        </select>
                                    </p>
                                    <p style="padding-top: 10px;">
                                        <a style="margin-left: 15px;" href="javascript: void(0);" id="wpc_add_many_options">
                                            <?php _e( 'Add Multiple Options', WPC_CLIENT_TEXT_DOMAIN ) ?>
                                        </a>
                                    </p>
                                    <br>
                                    <div id="wpc_block_for_many_options" style="display: none;">
                                        <form id="delete_user_settings" method="get">
                                            <p>
                                                <label for="wpc_textarea_many_options">
                                                    <?php _e( 'Options:', WPC_CLIENT_TEXT_DOMAIN ); ?>
                                                </label>
                                                <br>
                                                <textarea id="wpc_textarea_many_options" cols="70" rows="18"></textarea>
                                                <br>
                                                <span class="description">
                                                    <?php _e( 'Each option in new line', WPC_CLIENT_TEXT_DOMAIN ); ?>
                                                </span>
                                            </p>

                                            <p>
                                                <input type="button" class="button-primary submit_add_options" value="<?php _e( 'Add options', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                                                <input type="button" class="button cancel_add_option" style="float: right;" value="<?php _e( 'Cancel', WPC_CLIENT_TEXT_DOMAIN ); ?>" />
                                            </p>
                                        </form>
                                    </div>

                                    <?php if ( isset( $custom_field['options'] ) && is_array( $custom_field['options'] ) ) { ?>
                                        <?php foreach ( $custom_field['options'] as $key => $field_option ) { ?>
                                            <p>
                                                <?php _e( 'Option', WPC_CLIENT_TEXT_DOMAIN ) ?> <?php echo( $key ) ?>:
                                                <input type="text" name="custom_field[options][<?php echo( $key ) ?>]" value="<?php echo( $field_option ) ?>" />
                                                <input type="radio" class="wpc_default_value" value="<?php echo( $key ) ?>" name="custom_field[default_option]" <?php echo ( isset( $custom_field['default_option'] ) && $custom_field['default_option'] == $key ) ? 'checked' : '' ?> />
                                                <?php _e( 'Default Value', WPC_CLIENT_TEXT_DOMAIN ) ?>
                                                <?php if ( $key != 1 ): ?>
                                                    <a href="javascript:;" class="ct-field-delete-option">[x]</a>
                                                <?php endif; ?>
                                            </p>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <p><?php _e( 'Option', WPC_CLIENT_TEXT_DOMAIN ) ?> 1:
                                            <input type="text" name="custom_field[options][1]" value="<?php echo ( isset( $custom_field['options'][1] ) ) ? $custom_field['options'][1] : '' ?>" />
                                            <input type="radio" class="wpc_default_value" value="1" name="custom_field[default_option]" <?php echo ( isset( $custom_field['default_option'] ) && $custom_field['default_option'] == '1' ) ? 'checked' : '' ?> />
                                            <?php _e( 'Default Value', WPC_CLIENT_TEXT_DOMAIN ) ?>
                                        </p>
                                    <?php } ?>

                                    <div class="ct-field-additional-options"></div>
                                    <?php
 $last_id_option = 1; if ( !empty( $custom_field['track_number'] ) ) $last_id_option = $custom_field['track_number']; elseif( isset( $custom_field['options'] ) ) $last_id_option = max( array_keys( $custom_field['options'] ) ); ?>
                                    <input type="hidden" value="<?php echo $last_id_option ?>" name="track_number" />
                                    <p><a href="javascript:;" class="ct-field-add-option"><?php _e( 'Add another option', WPC_CLIENT_TEXT_DOMAIN ) ?></a></p>
                                </div>


                                <div class="ct-field-hiden-value">
                                    <p>
                                        <?php _e( 'Fill in the value for this field', WPC_CLIENT_TEXT_DOMAIN ) ?>:
                                        <input type="text" name="custom_field[options][1]" value="<?php echo( isset( $custom_field['options'][1] ) ? $custom_field['options'][1] : '' ) ?>" />
                                    </p>
                                </div>

                            </td>
                        </tr>
                        <tr class="mask_field">
                            <th>
                                <label for="display"><?php _e( 'Field Mask', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                                <?php
 $mask_type = isset( $custom_field['mask_type'] ) ? $custom_field['mask_type'] : ''; ?>
                                <select name="custom_field[mask_type]" id="mask_type">
                                    <option value="" data-mask="" <?php selected( $mask_type, '' ); ?>><?php _e( ' - Without Mask - ', WPC_CLIENT_TEXT_DOMAIN ); ?></option>
                                    <option value="date" data-mask="00/00/0000" <?php selected( $mask_type, 'date' ); ?>><?php echo __( 'Date', WPC_CLIENT_TEXT_DOMAIN ) . ': ' . date('m/d/Y') ?></option>
                                    <option value="time" data-mask="00:00:00" <?php selected( $mask_type, 'time' ); ?>><?php echo __( 'Time', WPC_CLIENT_TEXT_DOMAIN ) . ': ' . date('H:i:s') ?></option>
                                    <option value="datetime" data-mask="00/00/0000 00:00:00" <?php selected( $mask_type, 'datetime' ); ?>><?php echo __( 'DateTime', WPC_CLIENT_TEXT_DOMAIN ) . ': ' . date('m/d/Y H:i:s') ?></option>
                                    <option value="zip_code" data-mask="00000-000" <?php selected( $mask_type, 'zip_code' ); ?>><?php echo __( 'Zip Code', WPC_CLIENT_TEXT_DOMAIN ) . ': 12345-678' ?></option>
                                    <option value="zip_code2" data-mask="0-00-00-00" <?php selected( $mask_type, 'zip_code2' ); ?>><?php echo __( 'Zip Code', WPC_CLIENT_TEXT_DOMAIN ) . ': 1-23-45-67' ?></option>
                                    <option value="phone" data-mask="0000-0000" <?php selected( $mask_type, 'phone' ); ?>><?php echo __( 'Phone', WPC_CLIENT_TEXT_DOMAIN ) . ': 1234-5678' ?></option>
                                    <option value="phone2" data-mask="(00) 0000-0000" <?php selected( $mask_type, 'phone2' ); ?>><?php echo __( 'Phone with Code Area', WPC_CLIENT_TEXT_DOMAIN ) . ': (12) 3456-7890' ?></option>
                                    <option value="phone3" data-mask="(000) 000-0000" <?php selected( $mask_type, 'phone3' ); ?>><?php echo __( 'Phone with Code Area', WPC_CLIENT_TEXT_DOMAIN ) . ': (123) 456-7890' ?></option>
                                    <option value="cpf" data-mask="000.000.000-00" <?php selected( $mask_type, 'cpf' ); ?>><?php echo __( 'CPF', WPC_CLIENT_TEXT_DOMAIN ) . ': 123.456.789-01' ?></option>
                                    <option value="money" data-mask="#.##0,00" <?php selected( $mask_type, 'money' ); ?>><?php echo __( 'Money', WPC_CLIENT_TEXT_DOMAIN ) . ': 1.234,00' ?></option>
                                    <option value="ip" data-mask="099.099.099.099" <?php selected( $mask_type, 'ip' ); ?>><?php echo __( 'IP Address', WPC_CLIENT_TEXT_DOMAIN ) . ': ' . $_SERVER['REMOTE_ADDR'] ?></option>
                                    <option value="percent" data-mask="#0,00%" <?php selected( $mask_type, 'percent' ); ?>><?php echo __( 'Percent', WPC_CLIENT_TEXT_DOMAIN ) . ': 100,00%' ?></option>
                                    <option value="custom" <?php selected( $mask_type, 'custom' ); ?>><?php echo __( 'Custom', WPC_CLIENT_TEXT_DOMAIN ) ?></option>
                                </select><br />
                                <input type="text" name="custom_field[mask]" id="mask_value" value="<?php echo isset( $custom_field['mask'] ) ? $custom_field['mask'] : ''; ?>" /> <span class="description mask_desc"><?php _e( 'For custom mask type', WPC_CLIENT_TEXT_DOMAIN ); ?></span><br />
                                <span class="description mask_desc">
                                    A - <?php _e( 'Numbers and Letters', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    S - <?php _e( 'Only Letters', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    0 - <?php _e( 'Only Numbers', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    9 - <?php _e( 'Only Numbers( can be optional )', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                    # - <?php _e( 'Recursive Numbers', WPC_CLIENT_TEXT_DOMAIN ); ?><br />
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="display"><?php printf( __( 'Display Field on Admin Add %s Form', WPC_CLIENT_TEXT_DOMAIN ), $this->custom_titles['client']['s'] ) ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="custom_field[display]" id="display" value="1" <?php echo ( !isset( $custom_field['display'] ) || '1' == $custom_field['display'] ) ? 'checked' : '' ?> />
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="display_register"><?php printf( __( 'Display Field on %s Registration Form', WPC_CLIENT_TEXT_DOMAIN ), $this->custom_titles['client']['p'] ) ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="custom_field[display_register]" id="display_register" value="1" <?php echo ( isset( $custom_field['display_register'] ) && '1' == $custom_field['display_register'] ) ? 'checked' : '' ?> />
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="display"><?php printf( __( 'Display Field on %s Edit Profile Form', WPC_CLIENT_TEXT_DOMAIN ), $this->custom_titles['client']['s'] ) ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="custom_field[display_user]" id="display_user" value="1" <?php echo ( isset( $custom_field['display_user'] ) && '1' == $custom_field['display_user'] ) ? 'checked' : '' ?> />
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="display"><?php _e( 'Required Field', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="custom_field[required]" id="required" value="1" <?php echo ( isset( $custom_field['required'] ) && '1' == $custom_field['required'] ) ? 'checked' : '' ?> />
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="field_readonly"><?php _e( 'Readonly Field', WPC_CLIENT_TEXT_DOMAIN ) ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="custom_field[field_readonly]" id="field_readonly" value="1" <?php echo ( isset( $custom_field['field_readonly'] ) && '1' == $custom_field['field_readonly'] ) ? 'checked' : '' ?> />
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <input type="submit" class="button-primary" name="submit" value="<?php _e( 'Save Custom Field', WPC_CLIENT_TEXT_DOMAIN ) ?>">
                    </p>
                </form>
            </div>

        </div>
    </div>

</div>