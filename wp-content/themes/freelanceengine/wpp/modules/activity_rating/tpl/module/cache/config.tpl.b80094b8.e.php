<?php
/** Fenom template 'config.tpl' compiled at 2021-07-20 17:31:12 */
return new Fenom\Render( $fenom, function ( $var, $tpl ) {
	?><?php if ( ! empty( $var["config"] ) && ( is_array( $var["config"] ) || $var["config"] instanceof \Traversable ) ) {
		foreach ( $var["config"] as $var["item"] => $var["value"] ) { ?>
            <tr>
                <td><?php
					/* config.tpl:3: {$lang[$item]} */
					echo $var["lang"][ $var["item"] ]; ?></td>
				<?php
				/* config.tpl:4: {if is_array($value)} */
				if ( is_array( $var["value"] ) ) { ?>
                    <td class="text-center">
						<?php
						/* config.tpl:6: {if $value[0]} */
						if ( $var["value"][0] ) { ?>
							<?php
							/* config.tpl:7: {set $keyField = key($value[0])} */
							$var["keyField"] = key( $var["value"][0] ); ?>
                            <input type="text" class="" name="<?php
							/* config.tpl:8: {$keyField} */
							echo $var["keyField"]; ?>" value="<?php
							/* config.tpl:8: {$value[0][$keyField]} */
							echo $var["value"][0][ $var["keyField"] ]; ?>">
							<?php
							/* config.tpl:9: {/if} */
						} ?>
                    </td>
                    <td class="text-center">
						<?php
						/* config.tpl:12: {if $value[1]} */
						if ( $var["value"][1] ) { ?>
							<?php
							/* config.tpl:13: {set $keyField = key($value[1])} */
							$var["keyField"] = key( $var["value"][1] ); ?>
                            <input type="text" class="" name="<?php
							/* config.tpl:14: {$keyField} */
							echo $var["keyField"]; ?>" value="<?php
							/* config.tpl:14: {$value[1][$keyField]} */
							echo $var["value"][1][ $var["keyField"] ]; ?>">
							<?php
							/* config.tpl:15: {/if} */
						} ?>
                    </td>
					<?php
					/* config.tpl:17: {else} */
				} else { ?>
                    <td colspan="2" class="text-center"><input type="text" class="" name="<?php
						/* config.tpl:18: {$item} */
						echo $var["item"]; ?>" value="<?php
						/* config.tpl:18: {$value} */
						echo $var["value"]; ?>"></td>
					<?php
					/* config.tpl:19: {/if} */
				} ?>
            </tr>
			<?php
			/* config.tpl:21: {/foreach} */
		}
	} ?><?php
}, array(
	'options'   => 128,
	'provider'  => false,
	'name'      => 'config.tpl',
	'base_name' => 'config.tpl',
	'time'      => 1588253059,
	'depends'   => array(
		0 =>
			array(
				'config.tpl' => 1588253059,
			),
	),
	'macros'    => array(),

) );
