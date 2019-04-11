const { createElement, Fragment } = wp.element;
const { registerBlockType } = wp.blocks;
const { BlockAlignmentToolbar, BlockControls } = wp.blockEditor || wp.editor;
const { __ } = wp.i18n;
const { Placeholder } = wp.components;

registerBlockType( 'meilleur-copain/placeholder', {
    title: __( 'Emplacement BuddyPress', 'meilleur-copain' ),

    description: __( 'Cette page est réservée à l’affichage d’un composant de BuddyPress. Vous pouvez personnalisez l’alignement du contenu généré par ce composant grâce à la barre d’outils du bloc.', 'meilleur-copain' ),

    icon: 'buddicons-buddypress-logo',

    category: 'layout',

    attributes: {
        align: {
            type: 'string',
        },
    },

    edit: function( props ) {
        const { align } = props.attributes;
        const { placeholderIcon, placeholderLabel } = window.meilleurCopainVars;

        const setAttributes = function( attrs ) {
            props.setAttributes( attrs );
        }

        return (
            <Fragment>
                <BlockControls>
                    <BlockAlignmentToolbar
                        value={ align }
                        onChange={ ( nextAlign ) => {
                            setAttributes( { align: nextAlign } );
                        } }
                        controls={ [ 'left', 'center', 'right', 'wide', 'full' ] }
                    />
                </BlockControls>
                <Placeholder
                    icon={ placeholderIcon }
		            label={ placeholderLabel }
                />
            </Fragment>
        );
    },

    save: function() {
        return null;
    }
} );
