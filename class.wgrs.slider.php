<?php

if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}

class Wgrs_responsive_slider
{

    private $wpdb;
    private $prefix;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix;
    }

    function wgrs_slider_lists()
    {
        $query = "SELECT * FROM {$this->prefix}slider_list ORDER BY id DESC";
        $results = $this->wpdb->get_results($query, ARRAY_A);
        $hold_array = array();
        if (count($results) > 0)
        {
            foreach ($results as $result)
            {
                $id = $result['id'];
                $query = "SELECT count(id) as counter FROM {$this->prefix}sliders WHERE slider_id = '$id' GROUP BY slider_id";
                $slider_count = $this->wpdb->get_row($query, ARRAY_A);
                $result['counter'] = $slider_count['counter'];
                array_push($hold_array, $result);
            }
        }
        return $hold_array;
    }

    function wgrs_single_slider($edit_id = null)
    {
        $edit_id = intval($edit_id);
        if (!empty($edit_id))
        {
            $query = "SELECT * FROM {$this->prefix}slider_list WHERE id='$edit_id'";
            return $this->wpdb->get_row($query, ARRAY_A);
        }
        return array();
    }

    function wgrs_save_slider($post_data = array())
    {
        $status["status"] = "error";
        $status["messages"] = array();
        $slider_name = sanitize_text_field($post_data['slider_name']);
        $slider_status = sanitize_text_field($post_data['slider_status']);
        $slides_per_page = intval($post_data['slides_per_page']);
        $show_caption = sanitize_text_field($post_data['show_caption']);
        $caption_align = sanitize_text_field($post_data['caption_align']);

        $slide_effect = sanitize_text_field($post_data['slide_effect']);
        $autoplay = sanitize_text_field($post_data['autoplay']);
        $nav = sanitize_text_field($post_data['nav']);
        $dots = sanitize_text_field($post_data['dots']);
        $loop = sanitize_text_field($post_data['loop']);
        $slide_id = intval($post_data['slide_id']);
        
        if (!isset($post_data['_wpnonce']) || !wp_verify_nonce($post_data['_wpnonce']))
        {
            array_push($status["messages"], "Sorry, your nonce did not verify.");
        }
        if (!current_user_can('manage_options'))
        {
            array_push($status["messages"], "Sorry, you don't have permission to perform this task. Please contact your admistrator");
        }
        if (empty($slider_name) || strlen($slider_name) < 3 || strlen($slider_name) > 50)
        {
            array_push($status["messages"], "Slider name field should be min 3 and max 50 Characters");
        }
        if (empty($slider_status) || !is_string($slider_status))
        {
            array_push($status["messages"], "Slider status field is compulsory");
        }
        if (empty($slides_per_page) || !is_int($slides_per_page))
        {
            array_push($status["messages"], "Slider number field is compulsory");
        }
        if (!empty($slider_status) && $slider_status !== 'active' && $slider_status !== 'inactive')
        {
            array_push($status["messages"], "Slider status field should be active or inactive only");
        }
        if (empty($show_caption) || !is_string($show_caption))
        {
            array_push($status["messages"], "Show caption field is compulsory");
        }
        if (empty($caption_align) || !is_string($caption_align))
        {
            array_push($status["messages"], "Caption Align field is compulsory");
        }
        if (empty($slide_effect) || !is_string($slide_effect))
        {
            array_push($status["messages"], "Slide effect filed is compulsory");
        }
        if (empty($autoplay) || !is_string($autoplay))
        {
            array_push($status["messages"], "Autoplay filed is compulsory");
        }
        if (empty($nav) || !is_string($nav))
        {
            array_push($status["messages"], "Nav filed is compulsory");
        }
        if (empty($dots) || !is_string($dots))
        {
            array_push($status["messages"], "Dots filed is compulsory");
        }
        if (empty($loop) || !is_string($loop))
        {
            array_push($status["messages"], "Loop filed is compulsory");
        }
        if (!empty($slide_id) && !is_int($slide_id))
        {
            array_push($status["messages"], "Slider id fields is compulsory");
        }
        
        if (isset($status["messages"]) && count($status["messages"]) > 0)
        {
            return $status;
        }

        $format = array('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s');
        $data = array(
            'slider_name' => $slider_name,
            'slider_status' => $slider_status,
            'show_caption' => $show_caption,
            'caption_align' => $caption_align,
            'slides_per_page' => $slides_per_page,
            'slide_effect' => $slide_effect,
            'autoplay' => $autoplay,
            'nav' => $nav,
            'dots' => $dots,
            'loop' => $loop,
        );
        if (count($post_data) > 0)
        {
            if (isset($slide_id) && !empty($slide_id))
            {
                $results = $this->wpdb->update("{$this->prefix}slider_list", $data, array('id' => $slide_id), $format, array('%d'));
            } else
            {
                $results = $this->wpdb->insert("{$this->prefix}slider_list", $data, $format);
            }
            wp_redirect(admin_url("admin.php?page=wgrs-settings"));
            exit;
        }
        return $status;
    }

    function wgrs_delete_slider($del_id = NULL, $_wpnonce = NULL)
    {
        $del_id = intval($del_id);
        $status["status"] = "error";
        $status["messages"] = array("Record not removed");
        if (!empty($del_id) && !empty($_wpnonce) && wp_verify_nonce($_wpnonce) && current_user_can('manage_options'))
        {
            $result = $this->wpdb->delete("{$this->prefix}slider_list", array('id' => $del_id));
            if ($result === 1)
            {
                wp_redirect(admin_url("admin.php?page=wgrs-settings"));
                exit;
            }
        }
        return $status;
    }

    function wgrs_save_slide($post_data, $file_data)
    {
        $status["status"] = "error";
        $status["messages"] = array();
        if (!isset($post_data['_wpnonce']) || !wp_verify_nonce($post_data['_wpnonce']))
        {
            array_push($status["messages"], "Sorry, your nonce did not verify.");
        } elseif (!current_user_can('manage_options'))
        {
            array_push($status["messages"], "Sorry, you don't have permission to perform this task. Please contact your admistrator");
        } else
        {
            $caption = sanitize_text_field($post_data['caption']);
            $description = sanitize_text_field($post_data['description']);
            $slider = intval($post_data['slider']);
            $form_type = sanitize_text_field($post_data['form_type']);
            $file_size = intval($file_data['size']);
            $image_uri = isset($post_data['image_uri']) ? esc_url($post_data['image_uri']) : '';
            $slider_status = sanitize_text_field($post_data['slider_status']);
            $slide_id = isset($post_data['slide_id']) ? intval($post_data['slide_id']) : NULL;

            if (empty($caption) || strlen($caption) < 3 || strlen($caption) > 50)
            {
                array_push($status["messages"], "Caption field should be min 10 and max 50 Characters");
            }
            if (empty($description) || strlen($description) < 3 || strlen($description) > 200)
            {
                array_push($status["messages"], "Description field should be min 10 and max 200 Characters");
            }
            if (empty($slider) || !is_int($slider))
            {
                array_push($status["messages"], "Slider type fields is compulsory");
            }
            if (empty($form_type) || !is_string($form_type))
            {
                array_push($status["messages"], "Form type field is compulsory and only string charaters are allowed");
            }
            if (!empty($form_type) && is_string($form_type) && $form_type == 'add_form' && $file_size === 0)
            {
                array_push($status["messages"], "Uploading file is compulsory");
            }

            if (!empty($form_type) && is_string($form_type) && $form_type == 'edit_form' && empty($slide_id))
            {
                array_push($status["messages"], "Slider id field is compulsory");
            }
            if (empty($slider_status) || !is_string($slider_status))
            {
                array_push($status["messages"], "Slider status field is compulsory");
            }
            if (!empty($slider_status) && $slider_status !== 'active' && $slider_status !== 'inactive')
            {
                array_push($status["messages"], "Slider status field should be active or inactive only");
            }
            if (!empty($form_type) && is_string($form_type) && $form_type == 'edit_form' && empty($image_uri))
            {
                array_push($status["messages"], "Uploading file is compulsory");
            }

            if (isset($status['messages']) && count($status['messages']) > 0)
            {
                return $status;
            }

            $format = array('%s', '%s', '%d', '%s', '%s');
            $data = array(
                'caption' => $caption,
                'description' => $description,
                'slider_id' => $slider,
                'slider_status' => $slider_status,
                'image_uri' => $image_uri
            );

            if (isset($file_size) && $file_size > 0)
            {
                /*
                 * Remove existing uploaded image file
                 * $param $file_url
                 */
                if (!empty($image_uri))
                {
                    $file = str_replace(rtrim(get_site_url(), '/') . '/', ABSPATH, $image_uri);
                    $delete = apply_filters('wp_delete_file', $file);
                    if (!empty($delete))
                    {
                        @unlink($delete);
                    }
                }
                /*
                 * Insert new image file
                 */
                // We are only allowing images
                $allowedMimes = array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'png' => 'image/png',
                );
                $file_type = wp_check_filetype(basename($file_data['name']), $allowedMimes);
                if (isset($file_type['type']) && empty($file_type['type']))
                {
                    array_push($status["messages"], "Your uploaded file type is not allowed! You can only upload jpg, jpeg, jpe, gif, png");
                } else
                {
                    $upload_overrides = array('test_form' => false, 'mimes' => $allowedMimes);
                    $movefile = wp_handle_upload($file_data, $upload_overrides);
                    if (isset($movefile['error']) && !empty($movefile['error']))
                    {
                        $status["messages"] = $movefile['error'];
                    } else
                    {
                        $data['image_uri'] = $movefile['url'];
                    }
                }
            }

            $result = '';
            if ($post_data['form_type'] == 'add_form')
            {
                $result = $this->wpdb->insert("{$this->prefix}sliders", $data, $format);
            } elseif ($post_data['form_type'] == 'edit_form')
            {
                $result = $this->wpdb->update("{$this->prefix}sliders", $data, array('id' => $slide_id), $format, array('%d'));
            }
            if ($result)
            {
                wp_redirect(admin_url("admin.php?page=wgrs-slides"));
                exit;
            }
        }

        return $status;
    }

    function wgrs_slides($limit = 10, $offset = 0)
    {
        $query = "SELECT sl.id, sl.slider_id, sl.caption, sl.description, sl.image_uri, sl.slider_status, sl.inserted_on, li.slider_name as slider FROM {$this->prefix}sliders as sl LEFT JOIN {$this->prefix}slider_list li ON sl.slider_id = li.id ORDER BY sl.id DESC limit $offset,$limit";
        return $this->wpdb->get_results($query, ARRAY_A);
    }

    function wgrs_slides_front_end($id)
    {
        $id = intval($id);
        if (empty($id))
        {
            return array();
        }
        $query = "SELECT * FROM {$this->prefix}sliders WHERE slider_id='$id' AND slider_status = 'active' ORDER BY id DESC";
        return $this->wpdb->get_results($query, ARRAY_A);
    }

    function wgrs_slides_count()
    {
        $query = "SELECT COUNT(*) as count FROM {$this->prefix}sliders";
        $total = $this->wpdb->get_row($query, ARRAY_A);
        if (isset($total) && count($total) > 0)
        {
            return $total['count'];
        }
        return 0;
    }

    function wgrs_delete_slide($del_id = NULL, $_wpnonce = NULL)
    {
        $status = false;
        $del_id = intval($del_id);
        if (!empty($del_id) && !empty($_wpnonce) && wp_verify_nonce($_wpnonce) && current_user_can('manage_options'))
        {
            $query = "SELECT * FROM {$this->prefix}sliders WHERE id='$del_id'";
            $result = $this->wpdb->get_row($query, ARRAY_A);
            if (isset($result) && count($result) > 0)
            {
                $status = $this->wpdb->delete("{$this->prefix}sliders", array('id' => $del_id));
                $file = str_replace(rtrim(get_site_url(), '/') . '/', ABSPATH, $result['image_uri']);
                $delete = apply_filters('wp_delete_file', $file);
                if (!empty($delete))
                {
                    @unlink($delete);
                }
                wp_redirect(admin_url("admin.php?page=wgrs-slides"));
                exit;
            }
        }
        return $status;
    }

    function wgrs_get_slide($edit_id)
    {
        $edit_id = intval($edit_id);
        $query = "SELECT sl.id, sl.slider_id, sl.caption, sl.description, sl.image_uri, sl.slider_status, sl.inserted_on, li.slider_name as slider FROM {$this->prefix}sliders as sl LEFT JOIN {$this->prefix}slider_list li ON sl.slider_id = li.id WHERE sl.id='$edit_id'";
        return $this->wpdb->get_row($query, ARRAY_A);
    }

    //function to return the pagination string
    function wgrs_pagination_string($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
    {
        //defaults
        if (!$adjacents)
            $adjacents = 1;
        if (!$limit)
            $limit = 10;
        if (!$page)
            $page = 1;
        if (!$targetpage)
            $targetpage = "/";

        //other vars
        $prev = $page - 1;         //previous page is page - 1
        $next = $page + 1;         //next page is page + 1
        $lastpage = ceil($totalitems / $limit);    //lastpage is = total items / items per page, rounded up.
        $lpm1 = $lastpage - 1;        //last page minus 1

        /*
          Now we apply our rules and draw the pagination object.
          We're actually saving the code to a variable in case we want to draw it more than once.
         */
        $margin = "5px";
        $padding = "5px";
        $pagination = "";
        if ($lastpage > 1)
        {
            $pagination .= "<div class=\"pagination\"";
            if ($margin || $padding)
            {
                $pagination .= " style=\"";
                if ($margin)
                    $pagination .= "margin: $margin;";
                if ($padding)
                    $pagination .= "padding: $padding;";
                $pagination .= "\"";
            }
            $pagination .= ">";

            //previous button
            if ($page > 1)
                $pagination .= "<a href=\"$targetpage$pagestring$prev\">&laquo; prev</a>";
            else
                $pagination .= "<span class=\"disabled\">&laquo; prev</span>";

            //pages	
            if ($lastpage < 7 + ($adjacents * 2)) //not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                }
            }
            elseif ($lastpage >= 7 + ($adjacents * 2)) //enough pages to hide some
            {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 3))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                    }
                    $pagination .= "<span class=\"elipses\">...</span>";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>";
                    $pagination .= "<span class=\"elipses\">...</span>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";
                }
                //close to end; only hide early pages
                else
                {
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>";
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>";
                    $pagination .= "<span class=\"elipses\">...</span>";
                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else
                            $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $next . "\">next &raquo;</a>";
            else
                $pagination .= "<span class=\"disabled\">next &raquo;</span>";
            $pagination .= "</div>\n";
        }

        return $pagination;
    }

    function wgrs_slider_effects()
    {
        $data = array(
            'Shake Swing' => 'shake_swing',
            'Bounce In Out' => 'bounceOut_bounceIn',
            'Bounce In Out Down' => 'bounceOutDown_bounceInDown',
            'Bounce In Left Right' => 'bounceOutRight_bounceInLeft',
            'Bounce In Out Up' => 'bounceOutUp_bounceInUp',
            'Fade In Out' => 'fadeOut_fadeIn',
            'Fade In Out Down' => 'fadeOutDown_fadeInDown',
            'Fade In Out Down Big' => 'fadeOutDownBig_fadeInDownBig',
            'Fade In Out Left' => 'fadeOutLeft_fadeInLeft',
            'Fade In Out Out Left Big' => 'fadeOutLeftBig_fadeInLeftBig',
            'Fade In Out Right' => 'fadeOutRight_fadeInRight',
            'Fade In Out Right Big' => 'fadeOutRightBig_fadeInRightBig',
            'Fade In Out Up' => 'fadeOutUp_fadeInUp',
            'Fade In Out Up Big' => 'fadeOutUpBig_fadeInUpBig',
            'Flip In X Y' => 'flipInY_flipInX',
            'Flip Out X Y' => 'flipOutY_flipOutX',
            'Light Speed In Out' => 'lightSpeedOut_lightSpeedIn',
            'Rotate In Out' => 'rotateOut_rotateIn',
            'Rotate In Out Down Left' => 'rotateOutDownLeft_rotateInDownLeft',
            'Rotate In Out Down Right' => 'rotateOutDownRight_rotateInDownRight',
            'Rotate In Out Up Left' => 'rotateOutUpLeft_rotateInUpLeft',
            'Rotate In Out Up Right' => 'rotateOutUpRight_rotateInUpRight',
            'Slide In Out Up' => 'slideOutUp_slideInUp',
            'Slide In Out Down' => 'slideOutDown_slideInDown',
            'Slide In Out Left' => 'slideOutLeft_slideInLeft',
            'slide In Out Right' => 'slideOutRight_slideInRight',
            'Zoom In Out' => 'zoomOut_zoomIn',
            'Zoom In Out Down' => 'zoomOutDown_zoomInDown',
            'Zoom In Out Left' => 'zoomOutLeft_zoomInLeft',
            'Zoom In Out Right' => 'zoomOutRight_zoomInRight',
            'Zoom In Out Up' => 'zoomOutUp_zoomInUp',
            'Zack In TheBox Hinge' => 'ZackInTheBox_hinge',
            'Roll In Out' => 'rollOut_rollIn'
        );
        return $data;
    }

}
