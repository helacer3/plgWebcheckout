<div id="frm_reserva">
	<div id="frm_itmreserva">
		<form method="post" action="<?php echo URL_GATEWAY; ?>">
			<label>Nombres:</label><br/>
			<input type="text" name="buyerFullName" id="buyerFullName" placeholder="Digite su nombre completo" value="" required /><br/>

			<label>Correo:</label><br/>
			<input type="email" name="buyerEmail" id="buyerEmail" placeholder="Digite su correo electrónico" value="" required /><br/>

			<label>Celular:</label><br/>
			<input type="number" name="mobilePhone" id="mobilePhone" placeholder="Digite su correo electrónico" 
			value="" required /><br/>

			<input name="merchantId"      type="hidden" value="<?php echo PAY_MERCHANT; ?>" />
			<input name="accountId"       type="hidden" value="<?php echo PAY_ACCOUNT; ?>" />
			<input name="description"     type="hidden" value="Reserva Vehículo" />
			<input name="referenceCode"   type="hidden" value="<?php echo $strReference; ?>" />
			<input name="amount"          type="hidden" value="<?php echo VALUE_PAYMENT; ?>" />
			<input name="tax"             type="hidden" value="0" />
			<input name="taxReturnBase"   type="hidden" value="0" />
			<input name="extra1"          type="hidden" value="<?php echo $_GET['car']; ?>" />
			<input name="currency"        type="hidden" value="<?php echo VALUE_CURRENCY; ?>" />
			<input name="signature"       type="hidden" value="<?php echo $strSignature; ?>" />
			<input name="test"            type="hidden" value="1" />
			<input name="responseUrl"     type="hidden" value="<?php echo $urlResponse; ?>" />
			<input name="confirmationUrl" type="hidden" value="<?php echo $urlConfirm; ?>" />
			<input type="submit" value="Reservar">
		</form>
	</div>
</div>