( function( blocks, element, serverSideRender, blockEditor, components ) {

    const el = element.createElement;
    const ServerSideRender = serverSideRender;
    const { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls } = blockEditor;
    const Fragment = element.Fragment;
    const { TextControl, Panel, PanelBody, PanelRow } = components;
    const { __ } = wp.i18n;

    const sIcon = el('svg', { width: 30, height: 25, viewBox: "-0.891 -22.184 141.732 141.732" },
        el('path', { fill: "#2996cc", d: "M32.909,24.472c0-5.621-4.535-10.181-10.129-10.181c-5.594,0-10.127,4.56-10.127,10.181S17.186,34.65,22.78,34.65   C28.374,34.65,32.909,30.094,32.909,24.472 M9.717,87.969c0,0-0.098,0.134-0.239,0.354h120.996   c-0.011-0.013-0.019-0.021-0.027-0.037L89.151,32.069c0,0-4.896-6.664-9.789,0L53.807,66.86l-9.4-12.805c0,0-4.895-6.662-9.787,0   L9.717,87.969z M134.567,91.956H5.382V5.409h129.186V91.956L134.567,91.956z M139.951,94.66V2.705c0-1.494-1.205-2.705-2.69-2.705   H2.692C1.206,0,0,1.211,0,2.705V94.66c0,1.494,1.205,2.705,2.691,2.705h134.57C138.746,97.365,139.951,96.154,139.951,94.66" } )
    );

    blocks.updateCategory('sti', { icon: sIcon });

    var blockStyle = {
        backgroundColor: '#900',
        color: '#fff',
        padding: '20px',
    };

    blocks.registerBlockType( 'share-this-image/sharing-buttons', {
        apiVersion: 2,
        title: __( 'Sharing Buttons', 'share-this-image' ),
        description: __( 'Share This Image buttons block inserts plugin sharing buttons into the page.', 'share-this-image' ),
        icon: sIcon,
        category: 'sti',
        example: {},
        edit: function( props ) {

            var blockProps = blockEditor.hasOwnProperty('useBlockProps') ? blockEditor.useBlockProps() : null;

            return (
                el( Fragment, {},

                    el(
                        BlockControls,
                        { key: 'controls' },
                        el(
                            AlignmentToolbar,
                            {
                                value:  props.attributes.alignment,
                                onChange: ( value ) => {
                                    props.setAttributes( { alignment: value === undefined ? 'none' : value } );
                                },
                            }
                        )
                    ),

                    el( InspectorControls, {},

                        el( PanelBody, { title: __( 'Buttons', 'share-this-image' ), initialOpen: true },

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: __( 'Sharing Buttons', 'share-this-image' ),
                                        onChange: ( value ) => {
                                            props.setAttributes( { buttons: value } );
                                        },
                                        value: props.attributes.buttons
                                    }
                                ),
                            ),

                            el( PanelRow, {},
                                el(
                                    'div',
                                    {
                                        style: { 'margin-bottom': '15px' },
                                    },
                                    __( 'Available networks:', 'share-this-image' ) + ' ' + props.attributes.available_buttons
                                )
                            ),

                        ),

                        el( PanelBody, { title: __( 'Sharing Content', 'share-this-image' ), initialOpen: true },

                            el( PanelRow, {},
                                el(
                                    'div',
                                    {
                                        style: { 'margin-bottom': '15px' },
                                    },
                                    __( 'Set content that must be shared. When empty default values will be used.', 'share-this-image' )
                                ),
                            ),

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: __( 'Image URL ( required )', 'share-this-image' ),
                                        onChange: ( value ) => {
                                            props.setAttributes( { image: value } );
                                        },
                                        value: props.attributes.image
                                    }
                                )
                            ),

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: __( 'Title', 'share-this-image' ),
                                        onChange: ( value ) => {
                                            props.setAttributes( { title: value } );
                                        },
                                        value: props.attributes.title,
                                    }
                                )
                            ),

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: __( 'Description', 'share-this-image' ),
                                        onChange: ( value ) => {
                                            props.setAttributes( { description: value } );
                                        },
                                        value: props.attributes.description
                                    }
                                )
                            ),

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: __( 'Page URL', 'share-this-image' ),
                                        onChange: ( value ) => {
                                            props.setAttributes( { url: value } );
                                        },
                                        value: props.attributes.url
                                    }
                                )
                            ),

                        ),

                    ),
                    el(
                        'div',
                        blockProps,
                        el( ServerSideRender, {
                            block: 'share-this-image/sharing-buttons',
                            attributes: props.attributes,
                        } )
                    )
                )

            );


        },
        save: function( props ) {
            return null;
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.serverSideRender,
    window.wp.blockEditor,
    window.wp.components,
) );