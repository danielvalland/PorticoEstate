
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="start">
			<xsl:apply-templates select="start"/>
		</xsl:when>
		<xsl:when test="monthly">
			<xsl:apply-templates select="monthly"/>
		</xsl:when>
		<xsl:when test="notification">
			<xsl:apply-templates select="notification"/>
		</xsl:when>
		<xsl:when test="start_inspection">
			<xsl:apply-templates select="start_inspection"/>
		</xsl:when>
		<xsl:when test="inspection_history">
			<xsl:apply-templates select="inspection_history"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="start" xmlns:php="http://php.net/xsl">
	<style>
		.table-hover-cells > tbody > tr > th:hover,
		.table-hover-cells > tbody > tr > td:hover {
		background-color: #f5f5f5;
		}

		.table-hover-cells > tbody > tr > th.active:hover,
		.table-hover-cells > tbody > tr > td.active:hover,
		.table-hover-cells > tbody > tr.active > th:hover,
		.table-hover-cells > tbody > tr.active > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr:hover > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr.active:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr.active:hover > td:hover {
		background-color: #d8d8d8;
		}

		h1 > .divider:before,
		h2 > .divider:before,
		h3 > .divider:before,
		h4 > .divider:before,
		h5 > .divider:before,
		h6 > .divider:before,
		.h1 > .divider:before,
		.h2 > .divider:before,
		.h3 > .divider:before,
		.h4 > .divider:before,
		.h5 > .divider:before,
		.h6 > .divider:before {
		color: #777;
		content: "\0223E\0020";
		}


	</style>
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="current_year">
		<xsl:value-of select="current_year"/>
	</xsl:variable>

	<form method="post" id="form" action="{form_action}">

		<div class="row">
			<div class="mt-5 container">
				<div class="form-group">
					<fieldset>
						<legend>Velg kontroll</legend>

						<label for="control_area_id">
							<xsl:value-of select="php:function('lang', 'control types')"/>
						</label>
						<select id="control_area_id" name="control_area_id" class="form-control">
							<xsl:apply-templates select="control_area_list/options"/>
						</select>

						<label for="control_id">
							<xsl:value-of select="php:function('lang', 'control')"/>
						</label>
						<select id="control_id" name="control_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select control type')"/>
							</xsl:attribute>
							<xsl:apply-templates select="control_type_list/options"/>
						</select>

						<!--						<label for="entity_group_id">
							<xsl:value-of select="php:function('lang', 'entity group')"/>
						</label>
						<select id="entity_group_id" name="entity_group_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<xsl:apply-templates select="entity_group_list/options"/>
						</select>-->

						<label for="part_of_town_id">
							<xsl:value-of select="php:function('lang', 'part of town')"/>
						</label>
						<select id="part_of_town_id" name="part_of_town_id[]" class="form-control">
							<xsl:attribute name="multiple">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select part of town')"/>
							</xsl:attribute>
							<xsl:apply-templates select="part_of_town_list/options"/>
						</select>

					</fieldset>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="mt-5 container">
				<div class="text-center clearfix">
					<span class="float-left">
						<a href="#">
							<button type="submit" name="prev_year" value="1" class="btn btn-secondary">&lt;
								<xsl:value-of select="prev_year"/>
							</button>
						</a>
					</span>

					<span class="float-right">
						<a href="#">
							<button type="submit" name="next_year" value="1" class="btn btn-secondary">
								<xsl:value-of select="next_year"/> &gt;
							</button>
						</a>
					</span>
					<span class="float-none">
						<h4>
							<xsl:value-of select="$current_year"/>
							<input type="hidden" name="current_year" value="{$current_year}"/>
						</h4>
					</span>
				</div>
				<div class="mt-3 row datagrid table-responsive">
					<table class="table table-bordered table-hover-cells">
						<thead>
							<tr>
								<th>
									<h5>Bydel</h5>
								</th>
								<xsl:for-each select="first_half_year">
									<th>
										<h5>
											<xsl:value-of select="name"/>
										</h5>
									</th>
								</xsl:for-each>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="calendar_content1/rows">
								<xsl:with-param name="current_year" select ='$current_year'/>
							</xsl:apply-templates>

						</tbody>
						<thead>
							<tr>
								<th>
									<h5>Bydel</h5>
								</th>
								<xsl:for-each select="second_half_year">
									<th>
										<h5>
											<xsl:value-of select="name"/>
										</h5>
									</th>
								</xsl:for-each>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="calendar_content2/rows">
								<xsl:with-param name="current_year" select ='$current_year'/>
							</xsl:apply-templates>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>

	<div class="container">
		<div class="clearfix">
			<span class="float-left">
				<a href="#">
					<button type="button" class="btn btn-warning">Nullstill kalender</button>
				</a>
			</span>
		</div>
	</div>

</xsl:template>


<xsl:template match="inspection_history" xmlns:php="http://php.net/xsl">
	<style>
		.table-hover-cells > tbody > tr > th:hover,
		.table-hover-cells > tbody > tr > td:hover {
		background-color: #f5f5f5;
		}

		.table-hover-cells > tbody > tr > th.active:hover,
		.table-hover-cells > tbody > tr > td.active:hover,
		.table-hover-cells > tbody > tr.active > th:hover,
		.table-hover-cells > tbody > tr.active > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr:hover > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr.active:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr.active:hover > td:hover {
		background-color: #d8d8d8;
		}

		h1 > .divider:before,
		h2 > .divider:before,
		h3 > .divider:before,
		h4 > .divider:before,
		h5 > .divider:before,
		h6 > .divider:before,
		.h1 > .divider:before,
		.h2 > .divider:before,
		.h3 > .divider:before,
		.h4 > .divider:before,
		.h5 > .divider:before,
		.h6 > .divider:before {
		color: #777;
		content: "\0223E\0020";
		}
		.table-plain tbody tr,
		.table-plain tbody tr:hover,
		.table-plain tbody td {
		background-color:transparent;
		border:none;
		}

	</style>
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="current_year">
		<xsl:value-of select="current_year"/>
	</xsl:variable>

	<form method="post" id="form" action="{form_action}">

		<div class="row">
			<div class="mt-5 container">
				<div class="form-group">
					<fieldset>
						<legend>Velg kontroll</legend>

						<label for="control_area_id">
							<xsl:value-of select="php:function('lang', 'control types')"/>
						</label>
						<select id="control_area_id" name="control_area_id" class="form-control">
							<xsl:apply-templates select="control_area_list/options"/>
						</select>

						<label for="control_id">
							<xsl:value-of select="php:function('lang', 'control')"/>
						</label>
						<select id="control_id" name="control_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select control type')"/>
							</xsl:attribute>
							<xsl:apply-templates select="control_type_list/options"/>
						</select>

						<label for="part_of_town_id">
							<xsl:value-of select="php:function('lang', 'part of town')"/>
						</label>
						<select id="part_of_town_id" name="part_of_town_id[]" class="form-control">
							<xsl:attribute name="multiple">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select part of town')"/>
							</xsl:attribute>
							<xsl:apply-templates select="part_of_town_list/options"/>
						</select>

						<div class="form-inline mt-1">
							<input class="form-control" type="text" name="query" value="{query}"/>

							<div class="form-check">
								<label class="form-check-label ml-2">
									<input class="form-check-input ml-2" type="checkbox" name="deviation" value="1">
										<xsl:if test="deviation = 1">
											<xsl:attribute name="checked">
												<xsl:text>true</xsl:text>
											</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'deviation')"/>
								</label>
							</div>
							<button class="btn btn-primary ml-2" type="submit">
								<xsl:value-of select="php:function('lang', 'search')"/>
							</button>
						</div>

					</fieldset>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="mt-5 container">
				<h2 class="text-center">Siste utførte rapporter</h2>
				<div class="row">
					<xsl:call-template name="nextmatchs"/>
				</div>

				<div class="mt-3 row">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>
									<h5>Enhet</h5>
								</th>
								<th>
									<h5>Se rapport</h5>
								</th>
								<th>
									<h5>Siste rapportdato</h5>
								</th>
								<th colspan = '3'>
									<table class="table-plain">
										<tr>
											<td colspan = '3'>
												<h5>Tilstandsgrader</h5>
											</td>
										</tr>
										<tr>
											<td>
												<h5>1</h5>
											</td>
											<td>
												<h5>2</h5>
											</td>
											<td>
												<h5>3</h5>
											</td>

										</tr>
									</table>
								</th>
								<th>
									<h5>Åpne avvik</h5>
								</th>
								<th>
									<h5>Korrigert ved kontroll</h5>
								</th>
								<th>
									<h5>Handlinger</h5>
								</th>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="history_content/history_rows">
								<xsl:with-param name="date_format" select ='$date_format'/>
							</xsl:apply-templates>
						</tbody>
					</table>
				</div>
			</div>
			<div class="mt-5 container text-right">
				Eksport <a href="#">
					<img src="images/excel.png" width="35"/>
				</a>
			</div>
		</div>
	</form>

</xsl:template>


<xsl:template match="monthly" xmlns:php="http://php.net/xsl">
	<style>
		.table-hover-cells > tbody > tr > th:hover,
		.table-hover-cells > tbody > tr > td:hover {
		background-color: #f5f5f5;
		}

		.table-hover-cells > tbody > tr > th.active:hover,
		.table-hover-cells > tbody > tr > td.active:hover,
		.table-hover-cells > tbody > tr.active > th:hover,
		.table-hover-cells > tbody > tr.active > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr:hover > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr.active:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr.active:hover > td:hover {
		background-color: #d8d8d8;
		}

		h1 > .divider:before,
		h2 > .divider:before,
		h3 > .divider:before,
		h4 > .divider:before,
		h5 > .divider:before,
		h6 > .divider:before,
		.h1 > .divider:before,
		.h2 > .divider:before,
		.h3 > .divider:before,
		.h4 > .divider:before,
		.h5 > .divider:before,
		.h6 > .divider:before {
		color: #777;
		content: "\0223E\0020";
		}

		table th, table td {
		width: 200px;
		}
		table span {
		height: 30px;
		width: 100%;
		}
	</style>

	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<div class="mt-5 container">
		<div class="row">
			<div class="col">
				<h3>
					<xsl:value-of select="current_month"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="current_year"/>
				</h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p style="font-size: 14px">I <xsl:value-of select="current_month"/> er det satt opp befaring i <xsl:value-of select="part_of_town"/>:
				</p>
			</div>
			<div class="col">
				<div class="clearfix">
					<span class="float-right" style="font-size: 14px">Legg til ny <i class="far fa-plus-square"></i></span>
				</div>
				<div class="mt-2 clearfix">
					<span class="float-right" style="font-size: 14px">Merk som inaktiv <i class="far fa-trash-alt"></i></span>
				</div>
			</div>
		</div>


		<div class="text-center clearfix">
			<span class="float-left">
				<a href="{prev_month_url}">
					<button type="button" name="prev_year" value="1" class="btn btn-secondary">&lt;
						<xsl:value-of select="prev_month"/>
					</button>
				</a>
			</span>

			<span class="float-right">
				<a href="{next_month_url}">
					<button type="button" name="next_year" value="1" class="btn btn-secondary">
						<xsl:value-of select="next_month"/> &gt;
					</button>
				</a>
			</span>
			<span class="float-none">
				<h4>
					<xsl:value-of select="current_month"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="current_year"/>
				</h4>
			</span>
		</div>

		<div class="container datagrid table-responsive">
			<xsl:value-of disable-output-escaping="yes" select="calendar"/>
		</div>

		<!--https://jsfiddle.net/d1wnk1bg/8/-->


		<div class="container">
			<div class="clearfix">
				<span class="float-left">
					<a href="{start_url}">
						<button type="button" class="btn btn-warning">Gå tilbake</button>
					</a>
				</span>
				<!--				<span class="ml-2 float-left">
					<a href="#">
						<button type="button" class="btn btn-warning">Nullstill kalender</button>
					</a>
				</span>-->
				<span class="float-right">
					<a href="{send_notification_url}">
						<button type="button" class="btn btn-success">Gå til utsending</button>
					</a>
				</span>
				<span class="mr-2 float-right">
					<button type="button" class="btn btn-success" onclick="save_schedule();">Lagre</button>
				</span>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="notification" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<form method="post" id="send_notification" action="{form_action}" onsubmit="return submitSendNotificationForm(event, this);">
		<div class="mt-5 container">
			<div class="row">
				<div class="col">
					<h3>Send varsel</h3>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<p style="font-size: 14px">Det vil sendes varsel til følgende skoler og barnehager
					</p>
				</div>
			</div>

			<div class="container datagrid table-responsive form-group">
				<table class="mt-2 table table-hover">
					<thead>
						<tr>
							<th>
								<h5 id="checkall_flag" onclick="checkall();" checkall_flag="1">#</h5>
							</th>
							<th>
								<h5>Enhet</h5>
							</th>
							<th>
								<h5>
									<i class="far fa-envelope"></i>
								</h5>
							</th>
							<th>
								<h5>Dato</h5>
							</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="control_info">
							<tr>
								<td>
									<div class="input-group">
										<input id="send_email_{location_id}_{id}" name="send_email[{location_id}_{id}]" type="checkbox" value="1" class="mychecks">
											<xsl:if test="selected = 1">
												<xsl:attribute name="checked">
													<xsl:text>true</xsl:text>
												</xsl:attribute>
											</xsl:if>
									
										</input>
									</div>
								</td>
								<td>
									<xsl:value-of disable-output-escaping="yes" select="address"/>
								</td>
								<td>
									<div class="input-group">
										<!--										<div class="input-group-addon">
											<i class="far fa-envelope"></i>
										</div>-->
										<input type="hidden" name="timestamp[{location_id}_{id}]" value="{timestamp}"/>
										<input type="text" class="form-control" name="email[{location_id}_{id}]" value="{email}"/>
									</div>
								</td>
								<td>
									<xsl:value-of select="date"/>
								</td>
							</tr>

						</xsl:for-each>
					</tbody>
				</table>
			</div>

			<div class="container">
				<div class="clearfix">
					<span class="float-left">
						<a href="{monthly_url}">
							<button type="button" class="btn btn-warning">Gå tilbake</button>
						</a>
					</span>
					<span class="float-right">
						<a href="">
							<button type="submit" class="btn btn-success">Send varsel</button>
						</a>
					</span>
				</div>
			</div>
		</div>
	</form>
</xsl:template>


<xsl:template match="start_inspection" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<form method="post" id="form" action="{form_action}">

		<div class="row">
			<div class="mt-5 container">
				<div class="form-group">
					<fieldset>
						<legend>Velg kontroll</legend>

						<label for="control_area_id">
							<xsl:value-of select="php:function('lang', 'control types')"/>
						</label>
						<select id="control_area_id" name="control_area_id" class="form-control">
							<xsl:apply-templates select="control_area_list/options"/>
						</select>

						<label for="control_id">
							<xsl:value-of select="php:function('lang', 'control')"/>
						</label>
						<select id="control_id" name="control_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select control type')"/>
							</xsl:attribute>
							<xsl:apply-templates select="control_type_list/options"/>
						</select>


						<label for="part_of_town_id">
							<xsl:value-of select="php:function('lang', 'part of town')"/>
						</label>
						<select id="part_of_town_id" name="part_of_town_id[]" class="form-control">
							<xsl:attribute name="multiple">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select part of town')"/>
							</xsl:attribute>
							<xsl:apply-templates select="part_of_town_list/options"/>
						</select>

					</fieldset>
				</div>
			</div>
		</div>

		<div class="row mt-2">
			<div class="container">
				<h4>Velg blant dagens oppførte kontroller</h4>
			</div>
		</div>
		<div class="row mt-2">

			<div class="container">
				<div class="text-center clearfix">
					<span class="float-left">
						<a href="#">
							<button type="submit" name="prev_day" value="1" class="btn btn-secondary">&lt;
								<xsl:value-of select="prev_day"/>
							</button>
						</a>
					</span>

					<span class="float-right">
						<a href="#">
							<button type="submit" name="next_day" value="1" class="btn btn-secondary">
								<xsl:value-of select="next_day"/> &gt;
							</button>
						</a>
					</span>
					<span class="float-none">
						<h4>
							<input type="text" id="current_day_str" name="current_day_str" class="form-check form-check-inline" readonly= "true" style="border:none;text-align: center">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(current_day))"/>
								</xsl:attribute>
							</input>

						</h4>
					</span>
				</div>
				<div class="mt-2">
					<select id="check_list_id" name="check_list_id" class="form-control custom-select">
						<xsl:apply-templates select="todo_list/options"/>
					</select>
					<div class="float-right mt-2">
						<button type="button" class="btn btn-success" onClick="start_inspection();">
							<xsl:value-of select="php:function('lang', 'next')"/>
						</button>
					</div>
				</div>

			</div>
		</div>

		<xsl:if test="count(completed_list) != 0">
			<div class="row mt-2">
				<div class="container">
					<h5>Fullførte kontroller</h5>
					<ul>
						<xsl:for-each select="completed_list">
							<li style="display: block;">
								<img src="{//img_green_check}" width="16"/>
								<xsl:value-of disable-output-escaping="yes" select="node()"/>
							</li>
						</xsl:for-each>
					</ul>
				</div>
			</div>
		</xsl:if>

		<!--
		<div class="row mt-5">
			<div class="container">
				<h4>Velg blant alle enheter</h4>
			</div>
		</div>

		<div class="row mt-2">

			<div class="container">

				 Søkefelt som viser valg etterhvert som man skriver inn
				<select name="unitSelected" class="custom-select">
					<option value="1">Christi Krybbe skoler
					</option>
					<option value="2">Haukeland skole
					</option>
					<option value="3">Hellen skole
					</option>
					<option value="4">Krohnegen skole
					</option>
					<option value="5">Møhlenpris oppveksttun skole
					</option>
					<option value="5">Nordnes
					</option>

				</select>
				<div class="float-right mt-2">
					<a href="inspect-playground.html">
						<button type="button" class="btn btn-success">Neste</button>
					</a>
				</div>

			</div>
		</div>
		-->
	</form>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


<xsl:template match="rows">
	<xsl:param name="current_year"/>
	<tr>
		<th scope="row">
			<xsl:value-of select="header"/>
		</th>
		<xsl:for-each select="cell_data">
			<td onClick="open_monthly('{part_of_town_id}', '{$current_year}', '{month}');">
				<xsl:if test="registered &gt; 0">
					<span class="ml-3 float-left">
						<xsl:value-of select="registered"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="planned"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="completed"/>
					</span>
					<xsl:if test="registered = completed">
						<span class="float-right">
							<i class="fas fa-check float-right"></i>
						</span>
					</xsl:if>
				</xsl:if>

			</td>
		</xsl:for-each>
	</tr>
</xsl:template>

<xsl:template match="history_rows">
	<xsl:param name="date_format"/>
	<xsl:variable name="completed_date">
		<xsl:value-of select="completed_date"/>
	</xsl:variable>
	<xsl:variable name="check_list_id">
		<xsl:value-of select="id"/>
	</xsl:variable>

	<tr>
		<th scope="row">
			<xsl:value-of select="loc1_name"/>
		</th>
		<td class="text-center">
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.get_report')" />
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="$check_list_id"/>
				</xsl:attribute>
				<xsl:attribute name="target">
					<xsl:text>_blank</xsl:text>
				</xsl:attribute>
				<i class="far fa-file-pdf fa-3x"></i>
			</a>
		</td>
		<td>
			<xsl:value-of select="php:function('date', $date_format, number($completed_date))"/>
		</td>
		<td>
			<xsl:value-of select="findings_summary/condition_degree_1"/>
		</td>
		<td>
			<xsl:value-of select="findings_summary/condition_degree_2"/>
		</td>
		<td>
			<xsl:value-of select="findings_summary/condition_degree_3"/>
		</td>
		<td>
			<xsl:value-of select="num_open_cases"/>
		</td>
		<td>
			<xsl:value-of select="num_corrected_cases"/>
		</td>

		<td class="text-center">
			<a >
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.edit_check_list')" />
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="$check_list_id"/>
				</xsl:attribute>
				<xsl:attribute name="target">
					<xsl:text>_blank</xsl:text>
				</xsl:attribute>
				<i class="far fa-edit fa-3x"></i>
			</a>
		</td>
	</tr>
</xsl:template>

