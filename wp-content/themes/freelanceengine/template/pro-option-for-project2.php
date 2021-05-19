<?php
	$id = isset( $_REQUEST[ 'id' ] ) ? $_REQUEST[ 'id' ] : 0;
	if ( $id ) {
		$post = get_post( $id );
		if ( $post ) {
			global $ae_post_factory, $option_for_project;
			$post_object  = $ae_post_factory->get( $post->post_type );
			$post_convert = $post_object->convert( $post );
			echo '<script type="data/json"  id="edit_postdata">' . json_encode( $post_convert ) . '</script>';

			$data_ex = get_post_meta( $id, 'et_expired_date' );

			$max_days = ( mktime( 0, 0, 0, date( 'm', strtotime( $data_ex[ 0 ] ) ), date( 'd', strtotime( $data_ex[ 0 ] ) ), date( 'Y', strtotime( $data_ex[ 0 ] ) ) ) - mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) ) ) / 86400;

			$ae_pack  = $ae_post_factory->get( 'pack' );
			$packs    = $ae_pack->fetch( 'pack' );
			$pro_func = [];
			foreach ( $packs as $key => $package ) {
				if ( array_search( $package->sku, $option_for_project ) !== false ) {
					unset( $packs[ $key ] );
					$pro_func[] = $package;
				}
			}
			echo '<script type="data/json" id="pro_func">' . json_encode( $pro_func ) . '</script>';

			$opt = [];
			foreach ( $option_for_project as $value ) {
				if ( $post_convert->$value == 1 ) {
					$opt[] = [
						'name'    => $value,
						'et_date' => date( 'd-m-Y', strtotime( get_post_meta( $_REQUEST[ 'id' ], 'et_' . $value )[ 0 ] ) )
					];
				}
			}
			echo '<script type="data/json" id="opt_on">' . json_encode( $opt ) . '</script>';
		}
	}
?>
<div id="pro_functions">
    <input type='hidden' name='days_active_project' data-max_days=<?php echo $max_days; ?>>
	<?php
		global $pro_em_functions;

		foreach ( $pro_em_functions as $item ) {
			?>
            <div class="fre-input-field">
                <div class="checkline">
                    <input id="<?php echo $item[ 'sku' ] ?>"
                           name="<?php echo $item[ 'sku' ] ?>" type="checkbox"
                           value="1">
                    <label for="<?php echo $item[ 'sku' ] ?>"><?= getNameByProperty( $item[ 'sku' ] ); ?></label>
                    <div class="<?php echo $item[ 'sku' ] ?> tooltip_wp">
                        <i>?</i>
                        <div class="tip"></div>
                    </div>
                </div>

                <input type="hidden" id="price_<?php echo $item[ 'sku' ] ?>"
                       name="<?= $item[ 'price' ] ?>" value="<?= $item[ 'price' ] ?>"
                       data-price_option="<?= $item[ 'price' ] ?>">
            </div>
		<?php } ?>
    <input type="hidden" id="options_name" value="">
    <input type="hidden" id="options_days" value="">
</div>
