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

function meilleur_copain_customizer_sections( $sections = array() ) {
    return array_merge( $sections, array(
        'meilleur_copain_custom_css_settings' => array(
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
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		)
    ) );
}
add_filter( 'bp_nouveau_customizer_settings', 'meilleur_copain_customizer_settings' );
