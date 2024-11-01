<?php
if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
$obj = new Wgrs_responsive_slider();
$slider_lists = $obj->wgrs_slider_lists();
$edit_id = (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) ? intval($_GET['edit_id']) : NULL;
$single_slider = $obj->wgrs_single_slider($edit_id);
$results = array();
$btn_submit = (isset($_POST['but_submit']) && !empty($_POST['but_submit'])) ? sanitize_text_field($_POST['but_submit']) : NULL;
if (isset($btn_submit) && (string) $btn_submit === 'Submit' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && current_user_can('manage_options'))
{
    $results = $obj->wgrs_save_slider($_POST);
}

$delete_id = (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) ? intval($_GET['delete_id']) : NULL;
$_wpnonce = (isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) ? sanitize_text_field($_GET['_wpnonce']) : '';
if (!empty($delete_id) && !empty($_wpnonce) && wp_verify_nonce($_wpnonce) && current_user_can('manage_options'))
{
    $results = $obj->wgrs_delete_slider($delete_id, $_wpnonce);
}
?>
<div class="wrap">
    <h3>Add new slider</h3>
    <hr />
    <?php if (isset($results['messages']) && count($results['messages']) > 0): ?>
        <div>
            <ul class="list">
                <?php foreach ($results['messages'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method='post' name='myform' class="form-table">
        <?php wp_nonce_field(); ?>
        <table>
            <tr>
                <th><label for="slider_name">Slider Name*</label></th>
                <?php
                $slider_name = '';
                if (isset($_POST['slider_name']) && !empty($_POST['slider_name']))
                {
                    $slider_name = sanitize_text_field($_POST['slider_name']);
                } elseif (isset($single_slider['slider_name']) && !empty($single_slider['slider_name']))
                {
                    $slider_name = sanitize_text_field($single_slider['slider_name']);
                }
                ?>
                <td><input type='text' name='slider_name' id="slider_name" value="<?php echo $slider_name; ?>" style="width: 345px;"></td>
            </tr>
            <tr>
                <th><label for="slider_status">Slider Status*</label></th>
                <td>
                    <?php
                    $slider_status = '';
                    if (isset($_POST['slider_status']) && !empty($_POST['slider_status']))
                    {
                        $slider_status = sanitize_text_field($_POST['slider_status']);
                    } elseif (isset($single_slider['slider_status']) && !empty($single_slider['slider_status']))
                    {
                        $slider_status = sanitize_text_field($single_slider['slider_status']);
                    }
                    ?>
                    <input type="radio" name="slider_status" id="active" value="active" <?php echo ($slider_status == 'active' || $slider_status == '' ) ? 'checked' : ''; ?>> <label for="active">Active</label>
                    <input type="radio" name="slider_status" id="inactive" value="inactive" <?php echo ($slider_status == 'inactive') ? 'checked' : ''; ?>> <label for="inactive">Inactive</label>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Show/Hide Caption*</label></th>
                <td>
                    <?php
                    $show_caption = '';
                    if (isset($_POST['show_caption']) && !empty($_POST['show_caption']))
                    {
                        $show_caption = sanitize_text_field($_POST['show_caption']);
                    } elseif (isset($single_slider['show_caption']) && !empty($single_slider['show_caption']))
                    {
                        $show_caption = sanitize_text_field($single_slider['show_caption']);
                    }
                    ?>
                    <input type="radio" name="show_caption" id="show" value="show" <?php echo ($show_caption == 'show' || $show_caption == '' ) ? 'checked' : ''; ?>> <label for="show">Show</label>
                    <input type="radio" name="show_caption" id="hide" value="hide" <?php echo ($show_caption == 'hide') ? 'checked' : ''; ?>> <label for="hide">Hide</label>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Caption Alignment*</label></th>
                <td>
                    <?php
                    $caption_align = '';
                    if (isset($_POST['caption_align']) && !empty($_POST['caption_align']))
                    {
                        $caption_align = sanitize_text_field($_POST['caption_align']);
                    } elseif (isset($single_slider['caption_align']) && !empty($single_slider['caption_align']))
                    {
                        $caption_align = sanitize_text_field($single_slider['caption_align']);
                    }
                    ?>
                    <input type="radio" name="caption_align" id="left_top" value="left_top" <?php echo ($caption_align == 'left_top') ? 'checked' : ''; ?>> <label for="left_top">Left Top</label>
                    <input type="radio" name="caption_align" id="left_bottom" value="left_bottom" <?php echo ($caption_align == 'left_bottom') ? 'checked' : ''; ?>> <label for="left_bottom">Left Bottom</label>
                    <input type="radio" name="caption_align" id="right_top" value="right_top" <?php echo ($caption_align == 'right_top') ? 'checked' : ''; ?>> <label for="right_top">Right Top</label>
                    <input type="radio" name="caption_align" id="right_bottom" value="right_bottom" <?php echo ($caption_align == 'right_bottom' || $caption_align == '') ? 'checked' : ''; ?>> <label for="right_bottom">Right Bottom</label>
                </td>
            </tr>
            <tr>
                <th><label for="slides_per_page">Slides per page*</label></th>
                <td>
                    <?php
                    $slides_per_page = 0;
                    if (isset($_POST['slides_per_page']) && !empty($_POST['slides_per_page']))
                    {
                        $slides_per_page = intval(sanitize_text_field($_POST['slides_per_page']));
                    } elseif (isset($single_slider['slides_per_page']) && !empty($single_slider['slides_per_page']))
                    {
                        $slides_per_page = intval(sanitize_text_field($single_slider['slides_per_page']));
                    }
                    ?>

                    <select name="slides_per_page" id="slides_per_page">
                        <option value="">Slides limits</option>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($slides_per_page == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="slide_effect">Slide Effect*</label></th>
                <td>
                    <?php
                    $slide_effect = '';
                    if (isset($_POST['slide_effect']) && !empty($_POST['slide_effect']))
                    {
                        $slide_effect = sanitize_text_field($_POST['slide_effect']);
                    } elseif (isset($single_slider['slide_effect']) && !empty($single_slider['slide_effect']))
                    {
                        $slide_effect = sanitize_text_field($single_slider['slide_effect']);
                    }
                    $effects = $obj->wgrs_slider_effects();
                    ?>
                    <?php if (is_array($effects) && count($effects) > 0): ?>
                        <select name="slide_effect" id="slide_effect">
                            <option value="">Slide Effect</option>
                            <?php foreach ($effects as $key => $effect): ?>
                                <option value="<?php echo sanitize_text_field($effect); ?>" <?php echo ($effect === $slide_effect) ? 'selected' : ''; ?>><?php echo sanitize_text_field($key); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Autoplay*</label></th>
                <td>
                    <?php
                    $autoplay = '';
                    if (isset($_POST['autoplay']) && !empty($_POST['autoplay']))
                    {
                        $autoplay = sanitize_text_field($_POST['autoplay']);
                    } elseif (isset($single_slider['autoplay']) && !empty($single_slider['autoplay']))
                    {
                        $autoplay = sanitize_text_field($single_slider['autoplay']);
                    }
                    ?>
                    <input type="radio" name="autoplay" id="autoplay_yes" value="yes" <?php echo ($autoplay == 'yes' || $autoplay == '' ) ? 'checked' : ''; ?>> <label for="autoplay_yes">Yes</label>
                    <input type="radio" name="autoplay" id="autoplay_no" value="no" <?php echo ($autoplay == 'no') ? 'checked' : ''; ?>> <label for="autoplay_no">No</label>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Nav*</label></th>
                <td>
                    <?php
                    $nav = '';
                    if (isset($_POST['nav']) && !empty($_POST['nav']))
                    {
                        $nav = sanitize_text_field($_POST['nav']);
                    } elseif (isset($single_slider['nav']) && !empty($single_slider['nav']))
                    {
                        $nav = sanitize_text_field($single_slider['nav']);
                    }
                    ?>
                    <input type="radio" name="nav" id="nav_yes" value="yes" <?php echo ($nav == 'yes' || $nav == '' ) ? 'checked' : ''; ?>> <label for="nav_yes">Yes</label>
                    <input type="radio" name="nav" id="nav_no" value="no" <?php echo ($nav == 'no') ? 'checked' : ''; ?>> <label for="nav_no">No</label>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Dots*</label></th>
                <td>
                    <?php
                    $dots = '';
                    if (isset($_POST['dots']) && !empty($_POST['dots']))
                    {
                        $dots = sanitize_text_field($_POST['dots']);
                    } elseif (isset($single_slider['dots']) && !empty($single_slider['dots']))
                    {
                        $dots = sanitize_text_field($single_slider['dots']);
                    }
                    ?>
                    <input type="radio" name="dots" id="dots_yes" value="yes" <?php echo ($dots == 'yes' || $dots == '' ) ? 'checked' : ''; ?>> <label for="dots_yes">Yes</label>
                    <input type="radio" name="dots" id="dots_no" value="no" <?php echo ($dots == 'no') ? 'checked' : ''; ?>> <label for="dots_no">No</label>
                </td>
            </tr>
            <tr>
                <th><label for="slider_status">Loop*</label></th>
                <td>
                    <?php
                    $loop = '';
                    if (isset($_POST['loop']) && !empty($_POST['loop']))
                    {
                        $loop = sanitize_text_field($_POST['loop']);
                    } elseif (isset($single_slider['loop']) && !empty($single_slider['loop']))
                    {
                        $loop = sanitize_text_field($single_slider['loop']);
                    }
                    ?>
                    <input type="radio" name="loop" id="loop_yes" value="yes" <?php echo ($loop == 'yes' || $dots == '' ) ? 'checked' : ''; ?>> <label for="loop_yes">Yes</label>
                    <input type="radio" name="loop" id="loop_no" value="no" <?php echo ($loop == 'no') ? 'checked' : ''; ?>> <label for="loop_no">No</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" name="slide_id" value="<?php echo (isset($single_slider['id']) && !empty($single_slider['id'])) ? intval($single_slider['id']) : ''; ?>">
                    <input type='submit' name='but_submit' value='Submit' class="button button-primary">
                </td>
            </tr>
        </table>
    </form>
    <h3>Sliders List</h3>
    <table class="wp-list-table widefat striped slider-lists">
        <thead>
            <tr>
                <th>#</th>
                <th>Slider Name</th>
                <th>Status</th>
                <th>Slides Per Page</th>
                <th>Active Slides</th>
                <th>Alignment</th>
                <th style="width: 227px;">Shortcodes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($slider_lists) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($slider_lists as $slider_list): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $slider_list['slider_name']; ?></td>
                        <td><?php echo $slider_list['slider_status']; ?></td>
                        <td><?php echo $slider_list['slides_per_page']; ?></td>
                        <td><?php echo isset($slider_list['counter']) ? $slider_list['counter'] : 0; ?></td>
                        <td><?php echo isset($slider_list['caption_align']) ? ucwords(str_replace("_", " ", strtolower($slider_list['caption_align']))) : ''; ?></td>
                        <td>[wgrs_gallery id="<?php echo intval($slider_list['id']); ?>"]</td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=wgrs-settings&edit_id=' . $slider_list['id']); ?>"><input type='button' value='Edit' class="button button-secondary" /></a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=wgrs-settings&delete_id=' . $slider_list['id'])); ?>"><input type='button' value='Delete' class="button button-link-delete" /></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No slides found!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="wrap" style="border-top: 2px solid #999; padding-top: 10px; margin-top: 20px;">
    <h3>Developer Guides</h3>
    <h4>If you are a developer and having knowledge of WordPress functions and it's file structure then you can use WG Resposive Slider anywhere into your template files</h4>
    <p>Please pick the slider id from Shortcodes column from above table and use below function.</p>
    <h3>Example:</h3>
    <pre>
        &lt?php echo wgrs_custom_slider(1) ?&gt;
    </pre>
    <p>If you wanted to inject Wg Responsive Slider with your style and template then you can add extra parameter into above as true. It will provide you array data set as per your setting into the plugin</p>
    <pre>
        &lt?php echo wgrs_custom_slider(1, true) ?&gt;
    </pre>
    <p>This function can be used into any template file such as header.php, footer.php, single.php page.php, category.php, archive.php etc</p>
    <h3>If you are having any issues related this plugin please fill free to contact me on twitter <a href="https://twitter.com/abdulbaquee85" target="_blank">Abdul Baquee</a></h3>
    <h1>Thank you ! Have a wonderful Day :)</h1>

</div>