<div class="wrap">
	
	<div>
		<p>
			<?php _e( 'The following is a system report containing useful technical information for troubleshooting issues. If you need further help after viewing the report, click on the "Copy System Report" button below to copy the report and paste it in your message to support.', 'cs-wppb' ); ?><
		</p>
		<p>		
			<textarea
				id="system-report"
				name="system_report"
				cols="80" rows="10" class="large-text" readonly="readonly">
				<?php echo esc_html( $system_report_text ); ?>
			</textarea>
		</p>
	</div>

	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title">
					<?php esc_attr_e( 'Table header cell #1', 'WpAdminStyle' ); ?>
				</th>
				<th>
					<?php esc_attr_e( 'Table header cell #2', 'WpAdminStyle' ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td class="row-title">
					<label for="tablecell">
						<?php esc_attr_e('', 'WpAdminStyle'); ?>
					</label>
				</td>
				<td>
					<?php esc_attr_e( 'Table Cell #2', 'WpAdminStyle' ); ?>
				</td>
			</tr>

			<tr class="alternate">
				<td class="row-title">
					<label for="tablecell">
						<?php esc_attr_e('Table Cell ', 'WpAdminStyl'); ?>
						<code>alternate</code>
					</label>
				</td>
				<td>
					<?php esc_attr_e( 'Table Cell #4', 'WpAdminStyle' ); ?>
				</td>
			</tr>
		</tbody>

		<tfoot>
			<tr>
				<th class="row-title">
					<?php esc_attr_e( 'Table header cell #1', 'WpAdminStyle' ); ?>
				</th>
				<th>
					<?php esc_attr_e( 'Table header cell #2', 'WpAdminStyle' ); ?>
				</th>
			</tr>
		</tfoot>
	</table>
</div>