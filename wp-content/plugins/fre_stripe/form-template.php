
<div id="fre-payment-stripe" class="panel-collapse collapse fre-payment-proccess">
	<form class="modal-form form__block" id="stripe_form" autocomplete="on" data-ajax="false">
		<h3>Card information</h3>
		<div class="input-block">
			<label for="name_card"><?php _e('Name on card',ET_DOMAIN);?></label>
			<input type="text" class="form-control" name='name' id="name_card" data-stripe="name" placeholder="<?php _e('Name on card',ET_DOMAIN);?>">
		</div>
		<div class="row">
			<div class="col-md-6 col-xs-12 input-block">
				<label for="stripe_number"><?php _e('Card number',ET_DOMAIN);?></label>
				<input type="text" class="form-control" name="number" id="stripe_number" data-stripe="number" placeholder="****  ****  ****  ****">
			</div>
			<div class="col-md-3 col-xs-6 input-block">
				<label for="expiration">Expiration</label>
				<input type="text" class="form-control" name="expiration" id="expiration" value="" placeholder="MM / YY">				
				<input name='exp_month' data-stripe="exp-month" id="exp_month" type="hidden" placeholder="<?php _e('MM',ET_DOMAIN);?>">
				<input name='exp_year' data-stripe="exp-year" id="exp_year" type="hidden" placeholder="<?php _e('YY',ET_DOMAIN);?>">
			</div>
			<div class="col-md-3 col-xs-6 input-block">
				<label for="cvc">CVV/CVC Code</label>
				<input type="text" class="form-control" name='cvc' size="3" data-stripe="cvc" id="cvc" placeholder="<?php _e('CVC',ET_DOMAIN);?>">
			</div>
		</div>

		<div class="submit__button">
			<button class="btn-left select-payment fre-submit-btn" id="submit_stripe" ><?php _e('Make Payment',ET_DOMAIN);?></button>
		</div>
	</form>
</div>
