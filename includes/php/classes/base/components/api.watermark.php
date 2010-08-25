<?php
class Watermark{


        function create_watermark( $main_img_obj, $watermark_img_obj, $alpha_level = 100 ) {
                $alpha_level        /= 100;


                $main_img_obj_w        = imagesx( $main_img_obj );
                $main_img_obj_h        = imagesy( $main_img_obj );
                $watermark_img_obj_w        = imagesx( $watermark_img_obj );
                $watermark_img_obj_h        = imagesy( $watermark_img_obj );


                $main_img_obj_min_x        = floor( ( $main_img_obj_w ) - ( $watermark_img_obj_w ) );
                $main_img_obj_max_x        = ceil( ( $main_img_obj_w ) + ( $watermark_img_obj_w ) );
                $main_img_obj_min_y        = floor( ( $main_img_obj_h ) - ( $watermark_img_obj_h ) );
                $main_img_obj_max_y        = ceil( ( $main_img_obj_h ) + ( $watermark_img_obj_h ) );


                $return_img        = imagecreatetruecolor( $main_img_obj_w, $main_img_obj_h );


                for( $y = 0; $y < $main_img_obj_h; $y++ ) {
                        for( $x = 0; $x < $main_img_obj_w; $x++ ) {
                                $return_color        = NULL;


                                $watermark_x        = $x - $main_img_obj_min_x;
                                $watermark_y        = $y - $main_img_obj_min_y;


                                $main_rgb = imagecolorsforindex( $main_img_obj, imagecolorat( $main_img_obj, $x, $y ) );


                                if (        $watermark_x >= 0 && $watermark_x < $watermark_img_obj_w &&
                                                        $watermark_y >= 0 && $watermark_y < $watermark_img_obj_h ) {
                                        $watermark_rbg = imagecolorsforindex( $watermark_img_obj, imagecolorat( $watermark_img_obj, $watermark_x, $watermark_y ) );


                                        $watermark_alpha        = round( ( ( 127 - $watermark_rbg['alpha'] ) / 127 ), 2 );
                                        $watermark_alpha        = $watermark_alpha * $alpha_level;


                                        $avg_red                = $this->_get_ave_color( $main_rgb['red'],                $watermark_rbg['red'],                $watermark_alpha );
                                        $avg_green        = $this->_get_ave_color( $main_rgb['green'],        $watermark_rbg['green'],        $watermark_alpha );
                                        $avg_blue                = $this->_get_ave_color( $main_rgb['blue'],        $watermark_rbg['blue'],                $watermark_alpha );


                                        $return_color        = $this->_get_image_color( $return_img, $avg_red, $avg_green, $avg_blue );


                                } else {
                                        $return_color        = imagecolorat( $main_img_obj, $x, $y );

                                }


                                imagesetpixel( $return_img, $x, $y, $return_color );

                        }
                }


                return $return_img;

        }


        function _get_ave_color( $color_a, $color_b, $alpha_level ) {
                return round( ( ( $color_a * ( 1 - $alpha_level ) ) + ( $color_b        * $alpha_level ) ) );
        }


        function _get_image_color($im, $r, $g, $b) {
                $c=imagecolorexact($im, $r, $g, $b);
                if ($c!=-1) return $c;
                $c=imagecolorallocate($im, $r, $g, $b);
                if ($c!=-1) return $c;
                return imagecolorclosest($im, $r, $g, $b);
        }

} 
?>