<?php
/**
 * Fonctions de Meilleur Copain
 *
 * @package meilleur-copain\inc
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function meilleur_copain_register_placeholder_block() {
    wp_register_script(
        'meilleur-copain-placeholder',
        plugins_url( 'dist/index.js', dirname( __FILE__ ) ),
		array( 'wp-element', 'wp-editor' )
	);

	register_block_type( 'meilleur-copain/placeholder', array(
        'editor_script' => 'meilleur-copain-placeholder',
	) );
}
add_action( 'init', 'meilleur_copain_register_placeholder_block' );

function meilleur_copain_set_blocks_template() {
    if ( ! isset( $_GET['post'] ) ) {
        return;
    }

    $page     = (int) $_GET['post'];
    $bp_pages = array_map( 'intval', wp_list_pluck( (array) buddypress()->pages, 'id' ) );

    if ( in_array( $page, $bp_pages, true ) ) {
        $page_object = get_post_type_object( 'page' );

        $page_object->template = array(
            array( 'meilleur-copain/placeholder', array(
                'align' => 'none',
            ) )
        );

        $page_object->template_lock = 'all';
    }
}
add_action( 'load-page.php', 'meilleur_copain_set_blocks_template' );

function meilleur_copain_set_thumbnail( $html = '', $post_ID = 0, $post_thumbnail_id = 0, $size = '', $attr = '' ) {
    // Prevent infinite loops.
    remove_filter( 'post_thumbnail_html', 'meilleur_copain_set_thumbnail' );

    $mc = meilleur_copain();

    if ( ! isset( $mc->post ) || ! $mc->post->ID ) {
        return $html;
    }

    // Restore the page thumbnail.
    return get_the_post_thumbnail( $mc->post, $size, $attr );
}

function meilleur_copain_theme_compat() {
    $bp               = buddypress();
    $mc               = meilleur_copain();

    if ( bp_is_group() ) {
        $bp_object = 'groups';
    } elseif ( bp_is_user() ) {
        $bp_object = 'members';
    } else {
        $bp_object = bp_current_component();
    }

    if ( ! $bp_object || ! isset( $bp->pages->{$bp_object}->id ) ) {
        return;
    }

    // Get the cached post.
    $mc->post = WP_POST::get_instance( $bp->pages->{$bp_object}->id );

    // Use the page thumbnail only for BuddyPress directories.
    if ( bp_is_directory() && has_post_thumbnail( $mc->post ) ) {
        add_filter( 'has_post_thumbnail', '__return_true' );
        add_filter( 'post_thumbnail_html', 'meilleur_copain_set_thumbnail', 10, 5 );
    }
}
add_action( 'bp_setup_theme_compat', 'meilleur_copain_theme_compat', 100 );

function meilleur_copain_get_container_class() {
    $mc = meilleur_copain();
    $class = '';

    if ( ! isset( $mc->post ) ) {
        return $class;
    }

    if ( function_exists( 'has_block' ) && has_block( 'meilleur-copain/placeholder', $mc->post ) ) {
        $blocks = parse_blocks( $mc->post->post_content );
        $block  = reset( $blocks );

        // Get page attributes.
        $attributes = wp_parse_args( $block['attrs'],
            array(
                'align' => '',
            )
        );

        if ( $attributes['align'] ) {
            $class = 'align' . $attributes['align'];
        }
    }

    return apply_filters( 'meilleur_copain_get_container_class', $class );
}

function meilleur_copain_template_pack_container_classes( $class = '' ) {
    $mc_class = meilleur_copain_get_container_class();

    if ( ! $mc_class ) {
        return $class;
    }

    return $class . ' ' . sanitize_html_class( $mc_class );
}
add_filter( 'bp_nouveau_get_container_classes', 'meilleur_copain_template_pack_container_classes' );

/**
 *
 * admin init éditer le contenu des pages pour '<!-- wp:meilleur-copain/placeholder {"align":"none"} /-->'
 * Changement de page, activation d'un composant etc..
 */
