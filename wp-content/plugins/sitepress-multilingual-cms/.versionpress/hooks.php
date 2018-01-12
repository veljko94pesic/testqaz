<?php
/**
 * Created by PhpStorm.
 * User: Logos 036
 * Date: 14.11.2017.
 * Time: 11:18
 */






add_option('wpml_prefix_hash', rand(1, 999), '', 'no');

add_option('wpml_trid_count', 1, '', 'no');


function on_all_status_transitions( $post_id ) {

        $post = get_post($post_id);

        $wpml_element_type = apply_filters( 'wpml_element_type', $post->post_type );
        $get_language_args = array('element_id' => $post->ID, 'element_type' => $wpml_element_type);
        $post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );

        if($post_language_info->source_language_code == null){
            $count = get_option('wpml_trid_count');
            $trid = get_option('wpml_prefix_hash')*100000 + $count;


        $set_language_args = array(
                'element_id' => $post->ID,
                'element_type' => $wpml_element_type,
                'trid' => $trid,
                'language_code' => $post_language_info->language_code,
                'source_language_code' => $post_language_info->source_language_code
            );

            do_action('wpml_set_element_language_details', $set_language_args);
            $count+= 10;
            update_option( 'wpml_trid_count', $count, 'no' );

            }

}

add_action( 'transition_post_status', function ( $new_status, $old_status, $post )
{
    if ( 'publish' === $new_status && 'draft' === $old_status) {
        add_action(  'save_post',  'on_all_status_transitions', 100, 3 );
    }
}, 10, 3 );



function comment_created( $comment_ID, $comment_approved ) {
   // if( 1 === $comment_approved ){
        $get_language_args = array('element_id' => $comment_ID, 'element_type' => 'comment');
        $comment_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );

        $count = get_option('wpml_trid_count');
        $trid = get_option('wpml_prefix_hash')*100000 + $count;


        $set_language_args = array(
            'element_id' => $comment_ID,
            'element_type' => 'comment',
            'trid' => $trid,
            //'language_code' => $comment_language_info->language_code,
            'language_code' => 'en',
            'source_language_code' => $comment_language_info->source_language_code
        );

        do_action('wpml_set_element_language_details', $set_language_args);
        $count+= 10;
        update_option( 'wpml_trid_count', $count, 'no' );

   // }
}

//add_action( 'comment_post', 'comment_created', 10, 2 );


function taxonomy_created($term_id, $tt_id){

    create_tax($tt_id, 'tax_category');
}


function post_tag_created($term_id, $tt_id){


    create_tax($tt_id, 'tax_post_tag');
}

function link_category_created($term_id, $tt_id){

    create_tax($tt_id, 'tax_link_category');
}

function post_format_created($term_id, $tt_id){

    create_tax($tt_id, 'tax_post_format');
}

function create_tax($tt_id,$tax_type){
    $get_language_args = array('element_id' => $tt_id, 'element_type' => $tax_type);
    $tax_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );

    $count = get_option('wpml_trid_count');
    $trid = get_option('wpml_prefix_hash')*100000 + $count;

    $set_language_args = array(
        'element_id' => $tt_id,
        'element_type' => $tax_type,
        'trid' => $trid,
        'language_code' => $tax_language_info->language_code,
        'source_language_code' => $tax_language_info->source_language_code
    );

    do_action('wpml_set_element_language_details', $set_language_args);
    $count+= 10;
    update_option( 'wpml_trid_count', $count, 'no' );

}

add_action('create_category', 'taxonomy_created', 10, 2);
add_action('create_post_tag', 'post_tag_created', 10, 2);
add_action('create_link_category', 'link_category_created', 10, 2);
add_action('create_post_format', 'post_format_created', 10, 2);