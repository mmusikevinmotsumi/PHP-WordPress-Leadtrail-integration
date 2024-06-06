<?php

/**
 ** A base module for [leadquantity] and [leadquantity*]
 **/

/* form_tag handler */

add_action('wpcf7_init', 'nbcpf_add_form_tag_leadquantity');

function nbcpf_add_form_tag_leadquantity()
{
  wpcf7_add_form_tag(
    array('leadquantity', 'leadquantity*'),
    'nbcpf_leadquantity_form_tag_handler',
    array('name-attr' => true)
  );
}

function nbcpf_leadquantity_form_tag_handler($tag)
{
  if (empty($tag->name)) {
    return '';
  }

  $validation_error = wpcf7_get_validation_error($tag->name);

  $class = wpcf7_form_controls_class($tag->type);

  if ($validation_error) {
    $class .= ' wpcf7-not-valid';
  }

  $atts = array();

  $atts['class'] = $tag->get_class_option($class);
  $atts['id'] = $tag->get_id_option();
  $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

  if ($tag->is_required()) {
    $atts['aria-required'] = 'true';
  }

  if ($validation_error) {
    $atts['aria-invalid'] = 'true';
    $atts['aria-describedby'] = wpcf7_get_validation_error_reference(
      $tag->name
    );
  } else {
    $atts['aria-invalid'] = 'false';
  }


  if ($tag->has_option('size')) {
    $size = $tag->get_option('size', 'int', true);

    if ($size) {
      $atts['size'] = $size;
    } elseif ($multiple) {
      $atts['size'] = 4;
    } else {
      $atts['size'] = 1;
    }
  }

  if ($data = (array) $tag->get_data_option()) {
    $tag->values = array_merge($tag->values, array_values($data));
    $tag->labels = array_merge($tag->labels, array_values($data));
  }

  $values = $tag->values;
  $labels = $tag->labels;




  $html = '';
  $selected = 0;
  $hangover = wpcf7_get_hangover($tag->name);

  foreach ($values as $key => $value) {
    if ($hangover) {
      $selected = in_array($value, (array) $hangover, true);
    }

    $item_atts = array(
      'value' => $value,
      'selected' => $selected ? 'selected' : '',
    );

    $item_atts = wpcf7_format_atts($item_atts);

    $label = isset($labels[$key]) ? $labels[$key] : $value;

    $html .= sprintf(
      '<option %1$s>%2$s</option>',
      $item_atts,
      esc_html($label)
    );
  }



  $atts['name'] = $tag->name;

  $html = sprintf(
    '<span class="wpcf7-form-control-wrap" data-name="%1$s"><select %2$s>%3$s</select>%4$s</span>',
    esc_attr($tag->name),
    wpcf7_format_atts($atts),
    $html,
    $validation_error
  );

  return $html;
}


/* Validation filter */


add_filter('wpcf7_validate_leadquantity', 'nbcpf_leadquantity_validation_filter', 10, 2);
add_filter('wpcf7_validate_leadquantity*', 'nbcpf_leadquantity_validation_filter', 10, 2);

function nbcpf_leadquantity_validation_filter($result, $tag)
{
  $type = $tag->type;
  $name = $tag->name;

  $value = isset($_POST[$name]) ? (string) sanitize_text_field($_POST[$name]) : '';

  if ($tag->is_required() && '' == $value) {
    $result->invalidate($tag, wpcf7_get_message('invalid_required'));
  }
  if ($value) {
    if (intval($value) != $value) {
      $result->invalidate($tag, wpcf7_get_message('not_decimal'));
    } elseif (!is_numeric($value)) {
      $result->invalidate($tag, wpcf7_get_message('invalid_number'));
    }
  }

  return $result;
}


add_filter('wpcf7_messages', 'wpcf7_number_messages_qua', 10, 1);

function wpcf7_number_messages_qua($messages)
{
  return array_merge($messages, array(
    'not_decimal' => array(
      'description' => __("Please enter a number. Quantity cannot contain decimals.", 'contact-form-7'),
      'default' => __("Please enter a number. Quantity cannot contain decimals.", 'contact-form-7'),
    ),
  ));
}

/* Tag generator */

add_action('wpcf7_admin_init', 'nbcpf_add_tag_generator_leadquantity', 20);

function nbcpf_add_tag_generator_leadquantity()
{
  $tag_generator = WPCF7_TagGenerator::get_instance();
  $tag_generator->add(
    'leadquantity',
    __('lead quantity', 'leadtrail'),
    'nbcpf_tag_generator_leadquantity'
  );
}

function nbcpf_tag_generator_leadquantity($contact_form, $args = '')
{
  $args = wp_parse_args($args, array());
  $type = 'leadquantity';

  $description = __("Generate a form-tag for a quanity text input field.", 'leadtrail');

  //$desc_link = wpcf7_link( __( 'https://contactform7.com/text-fields/', 'leadtrail' ), __( 'Text Fields', 'leadtrail' ) );
  $desc_link = '';
?>
  <div class="control-box">
    <fieldset>
      <legend><?php echo sprintf(esc_html($description), $desc_link); ?></legend>

      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><?php echo esc_html(__('Field type', 'leadtrail')); ?></th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'leadtrail')); ?></legend>
                <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'leadtrail')); ?></label>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'leadtrail')); ?></label></th>
            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Options', 'contact-form-7')); ?></th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><?php echo esc_html(__('Options', 'contact-form-7')); ?></legend>
                <textarea name="values" oninput="this.value = this.value.replace(/[^0-9\n]/g, '').replace(/(\..*?)\..*/g, '$1');" class="values" id="<?php echo esc_attr($args['content'] . '-values'); ?>">1</textarea>
                <label for="<?php echo esc_attr($args['content'] . '-values'); ?>"><span class="description"><?php echo esc_html(__("One option per line.", 'contact-form-7')); ?></span><br /><span class="description"><b><?php echo esc_html(__("Please enter only numbers.", 'contact-form-7')); ?></b></span></label><br />

              </fieldset>
            </td>
          </tr>


          <tr>
            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-id'); ?>"><?php echo esc_html(__('Id attribute', 'leadtrail')); ?></label></th>
            <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" /></td>
          </tr>

          <tr>
            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-class'); ?>"><?php echo esc_html(__('Class attribute', 'leadtrail')); ?></label></th>
            <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" /></td>
          </tr>

        </tbody>
      </table>
    </fieldset>
  </div>

  <div class="insert-box">
    <input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

    <div class="submitbox">
      <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'leadtrail')); ?>" />
    </div>

    <br class="clear" />

    <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf(esc_html(__("To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'leadtrail')), '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" /></label></p>
  </div>
<?php
}
