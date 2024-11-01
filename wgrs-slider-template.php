<?php
if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
?>
<?php if (count($data) > 0): ?>
    <div class="ui centered container">
        <div class="row">
            <div class="owl-carousel wgrs-carousel">
                <?php if (isset($data['slides']) && count($data['slides']) > 0): ?>
                    <?php foreach ($data['slides'] as $slider): ?>
                        <div class="item">
                            <img src="<?php echo esc_url($slider['image_uri']); ?>" alt="<?php echo sanitize_text_field($slider['caption']); ?>" title="<?php echo sanitize_text_field($slider['caption']); ?>" class="ui image centered" />
                            <?php if (isset($data['show_caption']) && (string) $data['show_caption'] === 'show'): ?>
                                <div class="caption <?php echo (isset($data['caption_align']) && !empty($data['caption_align'])) ? sanitize_text_field($data['caption_align']) : 'right_bottom'; ?>">
                                    <div class="caption-title">
                                        <h4 class="ui large"><?php echo $slider['caption']; ?></h4>
                                        <p><?php echo $slider['description']; ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery.noConflict();
        jQuery(window).load(function ()
        {
            var items, autoplay, nav, dots, loop, animateOut, animateIn;
            items = parseInt(<?php echo (isset($data['items']) && (int) $data['items']) ? (int) $data['items'] : 1; ?>);
            autoplay = Boolean(<?php echo (isset($data['autoplay']) && $data['autoplay'] === 'yes') ? true : false; ?>);
            nav = Boolean(<?php echo (isset($data['nav']) && $data['nav'] === 'yes') ? true : false; ?>);
            dots = Boolean(<?php echo (isset($data['dots']) && $data['dots'] === 'yes') ? true : false; ?>);
            loop = Boolean(<?php echo (isset($data['loop']) && $data['loop'] === 'yes') ? true : false; ?>);
            animateOut = "<?php echo (isset($data['animateOut']) && !empty($data['animateOut'])) ? $data['animateOut'] : 'fadeOut'; ?>";
            animateIn = "<?php echo (isset($data['animateIn']) && !empty($data['animateIn'])) ? $data['animateIn'] : 'fadeIn'; ?>";
            jQuery(".wgrs-carousel").owlCarousel({
                animateOut: animateOut,
                animateIn: animateIn,
                autoplay: autoplay,
                items: items,
                nav: nav,
                dots: dots,
                loop: loop
            });
        });
    </script>
<?php endif; ?>