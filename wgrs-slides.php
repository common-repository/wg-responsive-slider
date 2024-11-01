<?php
if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
$obj = new Wgrs_responsive_slider();

/*
 * Delete Slides
 */
$delete_id = (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) ? intval($_GET['delete_id']) : NULL;
$_wpnonce = (isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) ? sanitize_text_field($_GET['_wpnonce']) : '';
if (!empty($delete_id) && !empty($_wpnonce) && wp_verify_nonce($_GET['_wpnonce']) && current_user_can('manage_options'))
{
    $status = $obj->wgrs_delete_slide($delete_id, $_wpnonce);
}
/*
 * Slider paginations
 */
$limit = 10;
$start = 0;
$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
if (isset($page))
{
    $start = ($page - 1) * $limit;
}
$total_pages = $obj->wgrs_slides_count();
$slides = $obj->wgrs_slides($limit, $start);
$targetpage = admin_url('admin.php?page=wgrs-slides');
$pagestring = "&paged=";
$paginations = $obj->wgrs_pagination_string($page, $total_pages, $limit, $start, $targetpage, $pagestring);
?>
<link type="text/css" rel="stylesheet" href="<?php echo WGRS_PLUGIN_URL . ('assets/css/wgrs.stylesheet.css') ?>" />
<div class="wrap">
    <h3>Slide Lists</h3>
    <hr />
    <table class="wp-list-table widefat wgrs-slide-lists">
        <thead>
            <tr>
                <th>#</th>
                <th>Caption</th>
                <th>Description</th>
                <th>Slider Type</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($slides) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($slides as $slide): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo sanitize_text_field($slide['caption']); ?></td>
                        <td><?php echo sanitize_text_field($slide['description']) ?></td>
                        <td><?php echo sanitize_text_field($slide['slider']) ?></td>
                        <td><img src="<?php echo esc_url($slide['image_uri']); ?>" alt="<?php echo sanitize_text_field($slide['caption']) ?>" title="<?php echo sanitize_text_field($slide['caption']) ?>" style="width: 150px; height: auto;"></td>
                        <td><?php echo sanitize_text_field($slide['slider_status']) ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=wgrs-uploads&edit_id=' . intval($slide['id'])); ?>">Edit</a> | 
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=wgrs-slides&delete_id=' . intval($slide['id']))); ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No slides found!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo $paginations; ?>
</div>