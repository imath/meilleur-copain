const { createElement, Fragment } = wp.element;
const { registerBlockType } = wp.blocks;
const { BlockAlignmentToolbar, BlockControls } = wp.editor;
const { __ } = wp.i18n;

registerBlockType( 'meilleur-copain/placeholder', {
    title: __( 'BP Placeholder', 'meilleur-copain' ),

    description: __( 'BuddyPress placeholder', 'meilleur-copain' ),

    icon: 'buddicons-buddypress-logo',

    category: 'layout',

    attributes: {
        align: {
            type: 'string',
        },
    },

    edit: function( props ) {
        const { align } = props.attributes;

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
                <p>Placeholder</p>
            </Fragment>
        );
    },

    save: function() {
        return null;
    }
} );
