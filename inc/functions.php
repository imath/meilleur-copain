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

/**
 * Registers the BuddyPress Placeholder block.
 *
 * @since 1.0.0
 */
function meilleur_copain_register_placeholder_block() {
    $mc = meilleur_copain();

    wp_register_script(
        'meilleur-copain-placeholder',
        $mc->js_url . 'placeholder-block/index.js',
        array( 'wp-element', 'wp-editor' ),
        $mc->version
	);

	register_block_type( 'meilleur-copain/placeholder', array(
        'editor_script' => 'meilleur-copain-placeholder',
    ) );

    wp_set_script_translations( 'meilleur-copain-placeholder', 'meilleur-copain', meilleur_copain()->js_lang_dir );
}
add_action( 'init', 'meilleur_copain_register_placeholder_block' );

/**
 * Sets the BuddyPress component's page blocks template and locks it.
 *
 * @since 1.0.0
 */
function meilleur_copain_set_blocks_template() {
    if ( ! isset( $_GET['post'] ) ) {
        return;
    }

    $bp        = buddypress();
    $page      = (int) $_GET['post'];
    $bp_pages  = array_map( 'intval', wp_list_pluck( (array) $bp->pages, 'id' ) );
    $bp_labels = array_map( 'esc_html', wp_list_pluck( (array) $bp->pages, 'title', 'id' ) );

    if ( in_array( $page, $bp_pages, true ) ) {
        $page_object = get_post_type_object( 'page' );

        $page_object->template = array(
            array( 'meilleur-copain/placeholder', array(
                'align' => 'none',
            ) )
        );

        $page_object->template_lock = 'all';

        // Pass the corresponding object's Buddicon to the Block
        $dashicon_suffix = array_search( $page, $bp_pages );
        if ( ! $dashicon_suffix ) {
            $dashicon_suffix = 'buddypress-logo';
        } elseif ( 'members' === $dashicon_suffix ) {
            $dashicon_suffix = 'community';
        }

        // Pass the Label
        $label = __( 'Composant BuddyPress inconnu', 'meilleur-copain' );
        if ( isset( $bp_labels[ $page ] ) ) {
            $label = $bp_labels[ $page ];
        }

        wp_localize_script( 'meilleur-copain-placeholder', 'meilleurCopainVars', array(
            'placeholderIcon'  => sprintf( 'buddicons-%s', $dashicon_suffix ),
            'placeholderLabel' => $label,
        ) );
    }
}
add_action( 'load-page.php', 'meilleur_copain_set_blocks_template' );

/**
 * Restores the BuddyPress Component's page thumbnail.
 *
 * @since 1.0.0
 *
 * @param  string       $html              The post thumbnail HTML.
 * @param  int          $post_ID           The post ID.
 * @param  string       $post_thumbnail_id The post thumbnail ID.
 * @param  string|array $size              The post thumbnail size. Image size or array of width and height
 *                                         values (in that order). Default 'post-thumbnail'.
 * @param  string       $attr              Query string of attributes.
 * @return string                          The post thumbnail HTML.
 */
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

/**
 * Waits for BP Theme Compatibility to set the BuddyPress Component's page thumbnail.
 *
 * @since 1.0.0
 */
function meilleur_copain_theme_compat() {
    $bp = buddypress();
    $mc = meilleur_copain();

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

/**
 * Gets a BuddyPress Component's page alignment attributes.
 *
 * @since 1.0.0
 */
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

    /**
     * Filters the alignment class.
     *
     * @since 1.0.0
     *
     * @param string $class The alignement class.
     */
    return apply_filters( 'meilleur_copain_get_container_class', $class );
}

/**
 * Sets BP Nouveau main container alignment class.
 *
 * @since 1.0.0
 *
 * @param  string $class BP Nouveau main container space separated classes.
 * @return string        BP Nouveau main container space separated classes.
 */
function meilleur_copain_template_pack_container_classes( $class = '' ) {
    $mc_class = meilleur_copain_get_container_class();

    if ( ! $mc_class ) {
        return $class;
    }

    return $class . ' ' . sanitize_html_class( $mc_class );
}
add_filter( 'bp_nouveau_get_container_classes', 'meilleur_copain_template_pack_container_classes' );

/**
 * Sanitizes Custom CSS option.
 *
 * @since 1.0.0
 *
 * @param  string $custom_css The style rules.
 * @return string|WP_Error    The sanitized style rules or a WP Error object.
 */
function meilleur_copain_sanitize_custom_css( $custom_css = '' ) {
    if ( preg_match( '#</?\w+#', $custom_css ) ) {
        return new WP_Error( 'illegal_markup', __( 'Le balisage n’est pas permis', 'meilleur-copain' ) );
    }

    return $custom_css;
}

/**
 * Render the Custom CSS styles for BuddyPress areas.
 *
 * @since 1.0.0
 */
function meilleur_copain_custom_css() {
    if ( ! is_buddypress() ) {
        return;
    }

    $styles = bp_nouveau_get_appearance_settings( 'meilleur_copain_custom_css' );
	if ( ! $styles && ! is_customize_preview() ) {
        return;
    }

    // Use `/* meilleur-copain */` delimiters in case another plugin is adding an inline style.
    wp_add_inline_style( 'bp-nouveau', "/* meilleur-copain */\n" . strip_tags( $styles ) . "\n/* meilleur-copain */" );
}
add_action( 'bp_enqueue_scripts', 'meilleur_copain_custom_css', 100 );

/**
 * Updates the BuddyPress Component's page content to a Placeholder block.
 *
 * @since 1.0.0
 *
 * @param array|object  $postarr  Optional. Post data. Arrays are expected to be escaped,
 *                                objects are not. Default array.
 * @return int|WP_Error           The value 0 or WP_Error on failure. The post ID on success.
 */
function meilleur_copain_update_page( $args = array() ) {
    $args = wp_parse_args( (array) $args, array(
        'ID'           => 0,
        'post_content' => '<!-- wp:meilleur-copain/placeholder {"align":"none"} /-->',
    ) );

    if ( ! $args['ID'] ) {
        return false;
    }

    return wp_update_post( $args );
}

/**
 * Listens to `bp-pages` option changes to eventually update the BuddyPress component's page content.
 *
 * @since 1.0.0
 *
 * @param  array $old_value The current value of the option.
 * @param  array $value     The new value of the option.
 */
function meilleur_copain_update_option( $old_value, $value ) {
    $old_value = (array) $old_value;
    $value     = (array) $value;
    $changes   = array_diff( $value, $old_value );

    if ( ! $changes ) {
        return;
    }

    $pages = get_posts( array(
        'numberposts' => -1,
        'include'     => array_values( $changes ),
        'post_type'   => 'page',
    ) );

    if ( $pages ) {
        foreach ( $pages as $page ) {
            if ( ! isset( $page->post_content ) || ! $page->post_content ) {
                meilleur_copain_update_page( array( 'ID' => $page->ID ) );
            }
        }
    }
}
add_action( 'update_option_bp-pages', 'meilleur_copain_update_option', 10, 2 );

/**
 * Loads translation.
 *
 * @since 1.0.0
 */
function meilleur_copain_load_textdomain() {
	$mc = meilleur_copain();
	load_plugin_textdomain( $mc->domain, false, trailingslashit( basename( $mc->dir ) ) . 'languages' );
}
add_action( 'bp_loaded', 'meilleur_copain_load_textdomain' );

/**
 * Plugin's Updater.
 *
 * @since 1.0.0
 */
function meilleur_copain_admin_updater() {
    $db_version      = bp_get_option( '_meilleur_copain_version', 0 );
    $current_version = meilleur_copain()->version;

	if ( ! version_compare( $db_version, $current_version, '<' ) ) {
		return;
    }

	if ( 0 === (int) $db_version ) {
        $page_ids = bp_core_get_directory_page_ids();

        foreach ( $page_ids as $page_id ) {
            meilleur_copain_update_page( array( 'ID' => $page_id ) );
        }
	}

	// Update Plugin version.
	bp_update_option( '_meilleur_copain_version', $current_version );
}
add_action( 'admin_init', 'meilleur_copain_admin_updater', 999 );
