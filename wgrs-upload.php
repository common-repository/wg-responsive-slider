<?php
if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
$obj = new Wgrs_responsive_slider();
$slider_lists = $obj->wgrs_slider_lists();
$results = array();
$btn_submit = (isset($_POST['but_submit']) && !empty($_POST['but_submit'])) ? sanitize_text_field($_POST['but_submit']) : NULL;
if (isset($btn_submit) && (string) $btn_submit === 'Submit' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && current_user_can('manage_options'))
{
    $results = $obj->wgrs_save_slide($_POST, $_FILES['file']);
}

$single_slide = array();
$edit_id = (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) ? intval($_GET['edit_id']) : NULL;
if (isset($edit_id) && !empty($edit_id))
{
    $single_slide = $obj->wgrs_get_slide($edit_id);
}
?>
<div class="wrap">
    <h3>Add New Slide</h3>
    <hr />
    <!-- Success and Error classes -->
    <?php if (isset($results['status']) && count($results['status']) > 0 && $results['status'] == 'error'): ?>
        <div class="error notice">
            <ul class="list-wrap">
                <?php foreach ($results['messages'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <!-- Form -->
    <form method='post' name='myform' class="form-table" enctype='multipart/form-data'>
        <?php wp_nonce_field(); ?>
        <table class="wp-list-table">
            <tr>
                <th><label for="caption">Caption*</label></th>
                <?php
                $caption = '';
                if (isset($_POST['caption']) && !empty($_POST['caption']))
                {
                    $caption = sanitize_text_field($_POST['caption']);
                } elseif (isset($single_slide['caption']) && !empty($single_slide['caption']))
                {
                    $caption = sanitize_text_field($single_slide['caption']);
                }
                ?>
                <td><input type='text' name='caption' id="caption" value="<?php echo $caption; ?>" style="width: 345px;"></td>
            </tr>
            <tr>
                <th><label for="description">Description*</label></th>
                <td>
                    <?php
                    $description = '';
                    if (isset($_POST['description']) && !empty($_POST['description']))
                    {
                        $description = sanitize_textarea_field($_POST['description']);
                    } elseif (isset($single_slide['description']) && !empty($single_slide['description']))
                    {
                        $description = sanitize_textarea_field($single_slide['description']);
                    }
                    ?>
                    <textarea name="description" id="description" rows="8" cols="40"><?php echo $description; ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="slider">Select Slider Type:*</label></th>
                <td>
                    <?php
                    $slider = '';
                    if (isset($_POST['slider']) && !empty($_POST['slider']))
                    {
                        $slider = intval($_POST['slider']);
                    } elseif (isset($single_slide['slider_id']) && !empty($single_slide['slider_id']))
                    {
                        $slider = intval($single_slide['slider_id']);
                    }
                    ?>
                    <select name="slider" id="slider">
                        <option value="">Slider type</option>
                        <?php if (count($slider_lists) > 0): ?>
                            <?php foreach ($slider_lists as $slider_list): ?>
                                <option value="<?php echo intval($slider_list['id']); ?>" <?php echo (intval($slider_list['id']) == $slider) ? 'selected' : ''; ?>><?php echo ucfirst(strtolower(sanitize_text_field($slider_list['slider_name']))); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="upload">Upload a file:</label></th>
                <td>
                    <p><input type='file' name='file' id="upload"></p>
                    <?php if (isset($single_slide['image_uri']) && !empty($single_slide['image_uri'])): ?>
                        <p><img src="<?php echo esc_url($single_slide['image_uri']); ?>" width="100"></p>
                        <input type="hidden" name="image_uri" value="<?php echo esc_url($single_slide['image_uri']); ?>">
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="description">Slider status*</label></th>
                <td>
                    <?php
                    $slider_status = '';
                    if (isset($_POST['slider_status']) && !empty($_POST['slider_status']))
                    {
                        $slider_status = sanitize_text_field($_POST['slider_status']);
                    } elseif (isset($single_slide['slider_status']) && !empty($single_slide['slider_status']))
                    {
                        $slider_status = sanitize_text_field($single_slide['slider_status']);
                    }
                    ?>
                    <input type="radio" name="slider_status" id="active" value="active" <?php echo ($slider_status == 'active' || $slider_status == '' ) ? 'checked' : ''; ?>> <label for="active">Active</label>
                    <input type="radio" name="slider_status" id="inactive" value="inactive" <?php echo ($slider_status == 'inactive') ? 'checked' : ''; ?>> <label for="inactive">Inactive</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="hidden" name="form_type" value="<?php echo (isset($single_slide['id']) && !empty($single_slide['id'])) ? 'edit_form' : 'add_form'; ?>">
                    <input type="hidden" name="slide_id" value="<?php echo (isset($single_slide['id']) && !empty($single_slide['id'])) ? intval($single_slide['id']) : ''; ?>">
                    <input type='submit' name='but_submit' value='Submit' class="button button-primary">
                </td>
            </tr>
        </table>
    </form>
</div>
