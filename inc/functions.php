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


function meilleur_copain_set_thumbnail( $html = '', $post_ID = 0, $post_thumbnail_id = 0, $size = '', $attr = '' ) {
    // Prevent infinite loops.
    remove_filter( 'post_thumbnail_html', 'meilleur_copain_set_thumbnail' );

    $bp = buddypress();
    $mc = meilleur_copain();

    if ( isset( $mc->post->object ) && $mc->post->object ) {
        $mc->post->ID = (int) $bp->pages->{$mc->post->object}->id;
    }

    // Restore the page thumbnail.
    return get_the_post_thumbnail( $mc->post, $size, $attr );
}

function meilleur_copain_activity_index() {
    $mc               = meilleur_copain();
    $mc->post         = get_post();
    $mc->post->object = 'activity';

    if ( has_post_thumbnail( $mc->post ) ) {
        add_filter( 'has_post_thumbnail', '__return_true' );
        add_filter( 'post_thumbnail_html', 'meilleur_copain_set_thumbnail', 10, 5 );
    }

    if ( function_exists( 'has_block' ) && has_block( 'meilleur-copain/placeholder', $mc->post ) ) {
        $blocks = parse_blocks( $mc->post->post_content );
        $block  = reset( $blocks );

        // Set Activity's page attributes.
        buddypress()->activity->page_attributes = wp_parse_args( $block['attrs'],
            array(
                'align' => '',
            )
        );
    }
}
add_action( 'bp_activity_screen_index', 'meilleur_copain_activity_index' );

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

function meilleur_copain_template_pack_container_classes( $class = '' ) {
    if ( bp_is_directory() ) {
        $bp                = buddypress();
        $current_component = bp_current_component();

        if ( isset( $bp->{$current_component}->page_attributes['align'] ) ) {
            $class .= ' align' . $bp->{$current_component}->page_attributes['align'];
        }
    }

    return $class;
}
add_filter( 'bp_nouveau_get_container_classes', 'meilleur_copain_template_pack_container_classes' );

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

/**
 *
 * admin init éditer le contenu des pages pour '<!-- wp:meilleur-copain/placeholder {"align":"none"} /-->'
 * Changement de page, activation d'un composant etc..
 */
