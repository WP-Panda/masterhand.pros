<?php
	$path = admin_url( 'admin.php' ) . '?page=' . $type_user;

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<style>
    .collapseTitle {
        color: #23282d;
        background-color: rgba(46, 96, 140, 0.1607843137254902);
        padding: 10px;
        cursor: pointer;
        border: 1px solid rgba(16, 42, 72, 0.15);
    }

    .collapseTitle h5 {
        margin: 0;
    }

    .add-btn {
        margin-bottom: 10px;
    }

    .simple-little-table form {
        font-size: 14px;
    }

    .simple-little-table form label {
        margin-bottom: 5px;
    }

    .simple-little-table .footer-modal {
        margin-top: 10px;
    }

</style>

<div class="container-fluid">
    <h4 class="mt-3 mb-3"><?php echo ucfirst( $type_user ) ?> statuses</h4>

    <div class="row mb-2">
        <div class="col">
            <div class="collapseTitle" data-toggle="collapse" href="#statusListCollapse" aria-expanded="false"
                 aria-controls="statusListCollapse">
                <h5>Statuses</h5>
            </div>
            <div class="collapse multi-collapse" id="statusListCollapse">
                <table class="table table-striped table-bordered" name="status_list">
                    <thead>
                    <tr>
                        <th scope="col">Status Position</th>
                        <th scope="col">Status Name</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $statuses as $item ) { ?>
                        <tr>
                            <td><?php echo $item[ 'status_position' ] ?></td>
                            <td><?php echo $item[ 'status_name' ] ?></td>
                            <td class="button_delete" onclick="delete_status(this)">
                                <button type="button" class="btn btn-danger">Delete</button>
                                <input type="hidden" value="<?php echo $item[ 'status_id' ] ?>" name="status_id">
                                <input type="hidden" value="<?php echo $item[ 'status_position' ] ?>"
                                       name="status_position">
                            </td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <div class="collapseTitle" data-toggle="collapse" href="#propertyListCollapse" aria-expanded="false"
                 aria-controls="propertyListCollapse">
                <h5>Properties</h5>
            </div>
            <div class="collapse multi-collapse" id="propertyListCollapse">

                <table class="table table-striped table-bordered" name="property_list">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Display</th>
                        <th>Published</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $properties as $item ) {
						if ( $item[ 'property_type' ] != 4 ) { ?>
                            <tr>
                                <td><?php echo $item[ 'property_position' ] ?></td>
                                <td><?php echo ! empty( $item[ 'option_value' ] ) ? $item[ 'property_name' ] . ' ' . $item[ 'option_value' ] : $item[ 'property_name' ] ?></td>
                                <td><?php echo $item[ 'property_display' ] ?></td>
                                <td><?php echo $item[ 'property_published' ] ?></td>
                                <td class="button_delete"
                                    onclick="modalWindow.show('modal_form_property', <?php echo $item[ 'property_id' ] ?>, 'property')">
                                    <button type="button" class="btn btn-primary">Edit</button>
                                </td>
                                <td class="button_delete" onclick="delete_property(this)">
                                    <button type="button" class="btn btn-danger">Delete</button>
                                    <input type="hidden" value="<?php echo $item[ 'property_id' ] ?>"
                                           name="property_id">
                                    <input type="hidden" value="<?php echo $item[ 'property_position' ] ?>"
                                           name="property_position">
                                </td>
                            </tr>
						<?php }
					} ?>
                    </tbody>
                </table>
                <div class="add-btn">
                    <!--                    <input type="submit" name="submit" class="btn btn-primary" value="Create property" onclick="modalWindow.show('modal_form_property')">-->
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <div class="collapseTitle" data-toggle="collapse" href="#optionListCollapse" aria-expanded="false"
                 aria-controls="optionListCollapse">
                <h5>Options</h5>
            </div>
            <div class="collapse multi-collapse" id="optionListCollapse">
                <table class="table table-striped table-bordered" name="option_list">
                    <thead>
                    <tr>
                        <th>Option key</th>
                        <th>Option value</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $options as $item ) { ?>
                        <tr>
                            <td><?php echo $item[ 'option_key' ] ?></td>
                            <td><?php echo $item[ 'option_value' ] ?></td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
                <div class="add-btn">
                    <input type="submit" name="submit" class="btn btn-primary" value="Edit options"
                           onclick="modalWindow.show('modal_form_option')">
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <div class="collapseTitle" data-toggle="collapse" href="#totalListCollapse" aria-expanded="false"
                 aria-controls="totalListCollapse">
                <h5>Total</h5>
            </div>
            <div class="collapse multi-collapse" id="totalListCollapse">
                <table class="table table-striped table-bordered">
					<?php foreach ( $result as $item ) { ?>
                        <tr>
							<?php foreach ( $item as $key => $value ) { ?>
								<?php if ( is_numeric( $key ) ) { ?>
                                    <td>
										<?php if ( $item[ 0 ] != 'id' ) {
											if ( $key != 0 && array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 0 ) {
												echo $value == 0 ? '-' : '+';
											} elseif ( $key == 0 && array_key_exists( 'option_value', $item ) ) {
												echo $value . ' ' . $item[ 'option_value' ];
											} else {
												echo $value == - 1 ? '~' : $value;
											}
											//                            else !empty($item['option_value']) ? $item['property_name'].' '.$item['option_value'] : $item['property_name'];
										} else {
											if ( $value == 'id' ) { ?>
												<?php echo ( ! $item[ 1 ] ) ? '<div class="alert alert-danger" role="alert">No Statuses</div>' : '' ?>
                                                <div class="add-btn">
                                                    <input type="submit" name="submit" class="btn btn-primary"
                                                           value="Add status"
                                                           onclick="modalWindow.show('modal_form_status')">
                                                </div>
											<?php } else { ?>
                                                <div class="add-btn">
                                                    <input type="submit" name="submit" class="btn btn-primary"
                                                           value="Edit"
                                                           onclick="modalWindow.show('modal_form_status', <?php echo $value ?>, 'status')">
                                                </div>
											<?php }
										} ?>
                                    </td>
								<?php } ?>
							<?php } ?>
                        </tr>
					<?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="wrap">
	<?php if ( 1 == 2 && $type_user == 'employer' ) {
		$table                 = $wpdb->get_blog_prefix() . 'pro_properties';
		$additional_properties = $wpdb->get_results( "
SELECT id as property_id, property_position, property_name, property_display, property_type, property_published 
FROM $table
WHERE user_role='$type_user' AND property_type=3
ORDER BY property_position", ARRAY_A ); ?>
        <div>
            <table width="50%" cellpadding='5' cellspacing='0' name="additional_list">
                <thead>
                <tr>
                    <td colspan="6"><h2>Additional options</h2></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Name</td>
                    <td>Display</td>
                    <td>Published</td>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <input type="submit" name="submit" value="Create additional options"
                               onclick="modalWindow.show('modal_form_additional')">
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php foreach ( $additional_properties as $item ) { ?>
                    <tr>
                        <td><?php echo $item[ 'property_position' ] ?></td>
                        <td><?php echo $item[ 'property_name' ] ?></td>
                        <td><?php echo $item[ 'property_display' ] ?></td>
                        <td><?php echo $item[ 'property_published' ] ?></td>
                        <td class="button_delete"
                            onclick="modalWindow.show('modal_form_additional', <?php echo $item[ 'property_id' ] ?>, 'additional')">
                            <a href="#">Edit</a>
                        </td>
                        <td class="button_delete" onclick="delete_additional(this)">
                            <a href="#">delete</a>
                            <input type="hidden" value="<?php echo $item[ 'property_id' ] ?>" name="property_id">
                            <input type="hidden" value="<?php echo $item[ 'property_position' ] ?>"
                                   name="property_position">
                        </td>
                    </tr>
				<?php } ?>
                </tbody>
            </table>
        </div>
	<?php } ?>
</div>

<div id="modal_form_status" class="simple-little-table" name="modal_form"><!-- Сaмo oкнo -->
    <span id="modal_close" onclick="modalWindow.close('modal_form_status')">X</span> <!-- Кнoпкa зaкрыть -->
    <h5 name="title_status">Status:</h5>
    <form id="status" action="<?php echo $path ?>" method="post">
        <div class="name_status">
            <label for='name'>Name status<input type="text" name="name" id="name" placeholder="" value=""></label>
            <label for='position'>Insert after:<select name="position" id="position">
                    <option value="0">Top</option>
					<?php foreach ( $statuses as $item ) { ?>
                        <option value="<?php echo $item[ 'status_position' ] ?>">
							<?php echo $item[ 'status_name' ] ?>
                        </option>
					<?php } ?>
                </select></label>
        </div>
		<?php foreach ( $properties as $item ) { ?>
			<?php if ( $item[ 'property_published' ] == 1 ) { ?>
				<?php if ( $item[ 'property_type' ] != 0 ) { ?>
                    <label for="prop_<?php echo $item[ 'property_id' ] ?>">
						<?php echo $item[ 'property_name' ] ?>
						<?php if ( ! empty( $item[ 'option_value' ] ) ) {
							$option = $item[ 'option_value' ];
							echo $option .= $item[ 'option_value' ] > 1 ? ' months' : ' month';
						} ?>
                        <input type="text" name="<?php echo $item[ 'property_id' ] ?>"
                               id="prop_<?php echo $item[ 'property_id' ] ?>"
                               value="">
                    </label>
				<?php } else { ?>
                    <label for="prop_<?php echo $item[ 'property_id' ] ?>" style="display: block">
						<?php echo $item[ 'property_name' ] ?>
                        <input type="checkbox" name="<?php echo $item[ 'property_id' ] ?>"
                               id="prop_<?php echo $item[ 'property_id' ] ?>">
                    </label>
				<?php } ?>
			<?php } else { ?>
                <input type="text" name="<?php echo $item[ 'property_id' ] ?>"
                       id="prop_<?php echo $item[ 'property_id' ] ?>"
                       value="" style="display: none">
			<?php } ?>
		<?php } ?>
        <div class="footer-modal">
            <input type="submit" name="submit" value="Save" class="btn btn-primary">
            <input type="hidden" name="action" value="create_status">
            <input type="hidden" name="status_id" id="status_id" value="">
            <input type="hidden" name="position_old_status" id="position_old_status" value="">
            <input type="hidden" name="type_user" id="type_user" value="<?php echo $type_user ?>">
        </div>
    </form>
</div>

<div id="modal_form_property" class="simple-little-table" name="modal_form"><!-- Сaмo oкнo -->
    <span id="modal_close" onclick="modalWindow.close('modal_form_property')">X</span> <!-- Кнoпкa зaкрыть -->
    <h2 name="title_prop">Property:</h2>
    <form id="property" action="<?php echo $path ?>" method="post">
        <div class="name_property">
            <label style="display: block;">Type:
                <label for='type_int' style="padding-left: 15px;">Integer
                    <input type="radio" name="type" id="type_int" value="1">
                </label>
                <label for='type_bool'>Bool <input type="radio" name="type" id="type_bool" value="0" checked></label>
                <label for='type_price'>Price <input type="radio" name="type" id="type_price" value="2"></label>
                <label for='type_option'>Option <input type="radio" name="type" id="type_option" value="3"></label>
            </label>

            <label style="display: block;">Display in the general list:
                <label for='display_y' style="padding-left: 15px;">Yes
                    <input type="radio" name="display" id="display_y" value="1" checked>
                </label>
                <label for='display_n'>No <input type="radio" name="display" id="display_n" value="0"></label>
            </label>

            <label style="display: block;">Published:
                <label for='published_y' style="padding-left: 15px;">Yes
                    <input type="radio" name="published" id="published_y" value="1" checked>
                </label>
                <label for='published_n'>No <input type="radio" name="published" id="published_n" value="0"></label>
            </label>

            <script>
                jQuery(function ($) {
                    $(document).ready(function () {
                        var display_y = document.querySelector('#modal_form_property input[id="display_y"]')
                        $(display_y).change(function () {
                            var published_n = document.querySelector('#modal_form_property input[id="published_n"]')
                            if (published_n.checked == true) {
                                document.querySelector('#modal_form_property input[id="display_n"]').checked = true
                                alert('First publish the property!');
                            }
                        })

                        var published_n = document.querySelector('#modal_form_property input[id="published_n"]')
                        $(published_n).change(function () {
                            document.querySelector('#modal_form_property input[id="display_n"]').checked = true
                        })
                    })
                })
            </script>

            <label for='name'>Name property<input type="text" name="name" id="name" placeholder="" value=""></label>

            <label for='time' style="display: none">Time for price in months<input type="number" name="time" id="time"
                                                                                   value="1"></label>
            <script>
                jQuery(function ($) {
                    $(document).ready(function () {
                        var all_price = document.querySelectorAll('#modal_form_property input[name="type"]')
                        $(all_price).change(function () {
                            var price = document.querySelector('#modal_form_property input[name="type"]:checked')
                            if (price.value == 2)
                                document.querySelector('label[for="time"]').setAttribute('style', 'display: inherit')
                            else
                                document.querySelector('label[for="time"]').setAttribute('style', 'display: none')
                        })
                    })
                })
            </script>

            <label for='position'>Insert after:<select name="position" id="position">
                    <option value="0">Top</option>
					<?php foreach ( $properties as $item ) { ?>
                        <option value="<?php echo $item[ 'property_position' ] ?>">
							<?php echo ! empty( $item[ 'option_value' ] ) ? $item[ 'property_name' ] . ' ' . $item[ 'option_value' ] : $item[ 'property_name' ] ?>
                        </option>
					<?php } ?>
                </select></label>
        </div>
        <div>
            <input type="submit" name="submit" value="Save">
            <input type="hidden" name="action" value="create_property">
            <input type="hidden" name="property_id" id="property_id" value="">
            <input type="hidden" name="type_user" id="type_user" value="<?php echo $type_user ?>">
            <input type="hidden" name="position_old_property" id="position_old_property" value="">
        </div>
    </form>
</div>

<div id="modal_form_option" class="simple-little-table" name="modal_form"><!-- Сaмo oкнo -->
    <span id="modal_close" onclick="modalWindow.close('modal_form_option')">X</span> <!-- Кнoпкa зaкрыть -->
    <h2 name="title_option">Option:</h2>
    <form id="option" action="<?php echo $path ?>" method="post">
        <div class="name_option">
			<?php foreach ( $options as $item ) { ?>
                <label for='currency'>Currency <input type="text" name="currency" id="currency" placeholder=""
                                                      value="<?php echo $item[ 'option_value' ] ?>"></label>
			<?php } ?>
        </div>
        <div>
            <input type="submit" name="submit" value="Save">
            <input type="hidden" name="action" value="edit_options">
        </div>
    </form>
</div>


<?php include_once( 'js/page-pro-properties.js' ) ?>

