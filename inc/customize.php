<?php
/**
 * Fonctions liées au customizer de Meilleur Copain
 *
 * @package meilleur-copain\inc
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function meilleur_copain_custom_css_option( $args = array() ) {
	$custom_css = '';

	if ( isset( $args['meilleur_copain_custom_css'] ) ) {
		$custom_css = $args['meilleur_copain_custom_css'];
	}

	return array_merge( $args, array(
		'meilleur_copain_custom_css' => $custom_css,
	) );
}
add_filter( 'bp_after_nouveau_appearance_settings_parse_args', 'meilleur_copain_custom_css_option' );

function meilleur_copain_customizer_sections( $sections = array() ) {
    return array_merge( $sections, array(
        'meilleur_copain_custom_css_section' => array(
            'title'       => __( 'Styles d’accompagnement', 'meilleur-copain' ),
			'panel'       => 'bp_nouveau_panel',
			'priority'    => 90,
			'description' => __( 'Ajouter des règles de style pour optimiser l’affichage des pages de BuddyPress.', 'meilleur-copain' ),
        ),
    ) );
}
add_filter( 'bp_nouveau_customizer_sections', 'meilleur_copain_customizer_sections' );

function meilleur_copain_customizer_settings( $settings = array() ) {
    return array_merge( $settings, array(
        'bp_nouveau_appearance[meilleur_copain_custom_css]' => array(
			'index'             => 'meilleur_copain_custom_css',
			'capability'        => 'bp_moderate',
			'sanitize_callback' => 'meilleur_copain_sanitize_custom_css',
			'transport'         => 'postMessage',
			'type'              => 'option',
		)
    ) );
}
add_filter( 'bp_nouveau_customizer_settings', 'meilleur_copain_customizer_settings' );

function meilleur_copain_customizer_controls( $controls = array() ) {
	return array_merge( $controls, array(
		'meilleur_copain_custom_css' => array(
			'class'       => 'WP_Customize_Code_Editor_Control',
			'label'       => __( 'Code CSS', 'meilleur-copain' ),
			'description' => __( 'Les règles de style ne seront chargées que dans les pages BuddyPress', 'meilleur-copain' ),
			'section'     => 'meilleur_copain_custom_css_section',
			'settings'    => 'bp_nouveau_appearance[meilleur_copain_custom_css]',
			'code_type'   => 'text/css',
		),
	) );
}
add_filter( 'bp_nouveau_customizer_controls', 'meilleur_copain_customizer_controls' );

function meilleur_copain_customize_preview_js() {
	$mc = meilleur_copain();

	wp_enqueue_script(
		'meilleur-copain-customize',
		$mc->js_url . '/customize/index.js',
		array( 'customize-preview' ),
		$mc->version,
		true
	);
}
add_action( 'customize_preview_init', 'meilleur_copain_customize_preview_js' );
