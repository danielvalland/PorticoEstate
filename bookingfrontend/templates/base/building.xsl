<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="building-page-content">
	<div class="info-content">
	<div class="container wrapper">
		
		<div class="location">
			<span><a>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Home')" />
				</a>
			</span>
		</div>

		<div class="row p-3">
			<div class="col-lg-6">

				<div class="row">
					<div class="col-md-4 mb-4 col-item-img">
						<img class="img-fluid image-circle" id="item-main-picture" src=""/>
					</div>
					<div class="col-md-8 col-xs-12 building-place-info">
						<h3>
							<xsl:value-of select="building/name"/>
						</h3>
						<i class="fas fa-map-marker d-inline"> </i>
						<div class="building-place-adr">
							<span>
								<xsl:value-of select="building/street"/>
							</span>
							<span class="d-block">
								<xsl:value-of select="building/zip_code"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="building/city"/>
							</span>
						</div>
					</div>

					<div class="col-12 mt-4 mb-4" id="item-description">
						<xsl:value-of disable-output-escaping="yes" select="building/description"/>
					</div>

							<xsl:if test="building/deactivate_application=0">
					<div class="col-auto">
							<button class="btn btn-light" id="newApplicationBtn">
								<i class="fas fa-plus"></i>&#160;
										<xsl:value-of select="php:function('lang', 'Book resource')" />
							</button>
					</div>						
							</xsl:if>

							<xsl:if test="building/deactivate_calendar=0">
					<div class="col-auto">
							<div>
								<button class="btn btn-light goToCal">
									<i class="fa fa-calendar"></i>&#160;
									<xsl:value-of select="php:function('lang', 'Calendar')" />
								</button>
							</div>
					</div>
							</xsl:if>

					<div class="building-accordion">
						<div class="building-card card-img-thumbs">
							<div class="building-card-header">
								<h5 class="mb-0">
									<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false">
										Bilder
									</button>
									<button data-toggle="collapse" data-target="#collapseTwo" class="btn fas fa-plus float-right"></button>
								</h5>
							</div>
							<div id="collapseTwo" class="collapse">
								<div class="card-body building-images" id="list-img-thumbs">
								</div>
							</div>
						</div>

						<xsl:if test="building/opening_hours and normalize-space(building/opening_hours)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
											Åpningstider
										</button>
										<button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseThree" class="collapse">
									<div class="card-body">
										<xsl:value-of disable-output-escaping="yes" select="building/opening_hours"/>
									</div>
								</div>
							</div>
						</xsl:if>

						<xsl:if test="building/contact_info and normalize-space(building/contact_info)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false">
											Kontaktinformasjon
										</button>
										<button data-toggle="collapse" data-target="#collapseFour" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseFour" class="collapse">
									<div class="card-body">
											<xsl:value-of disable-output-escaping="yes" select="building/contact_info"/>
												<xsl:if test="building/deactivate_sendmessage=0">
													<button class="btn btn-light" onclick="window.location.href='{building/message_link}'">
														<i class="fas fa-envelope"></i>&#160;
														<xsl:value-of select="php:function('lang', 'Send message')" />
													</button>
													- Melding til saksbehandler for bygg
												</xsl:if>
								</div>
								</div>
							</div>
						</xsl:if>
					</div>
				</div>

			</div>

			<div class="col-lg-6 building-bookable">
				<h3 class="">
					<xsl:value-of select="php:function('lang', 'Bookable resources (2018)')" />
				</h3>
				<div data-bind="foreach: bookableResource">
					<div class="custom-card">
						<a class="bookable-resource-link-href" href="" data-bind="">
							<span data-bind="text: name"></span>
						</a>
						
						<div data-bind="foreach: activitiesList">							
									<span class="tagTitle" data-bind="if: $index() == 0">Tilrettelagt for: </span>
							<span class="mr-2 textTagsItems" data-bind="text: $data"></span>
						</div>

								<div class="mt-2" data-bind="foreach: facilitiesList">
									<span class="tagTitle" data-bind="if: $index() == 0">Fasiliteter: </span>
									<span class="textTagsItems" data-bind="text: $data"></span>
								</div>

					</div>
				</div>

			</div>
		</div>
		</div>
		</div>

		<div class="container wrapper">
		<div class="row margin-top-and-bottom">		
			<xsl:if test="building/deactivate_calendar=0">
				<div class="col-6">
					<div class="button-group dropdown calendar-tool invisible">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								Velg lokaler
								<span class="caret"></span>
							</button>

							<ul class="dropdown-menu px-2" data-bind="foreach: bookableResource">
								<li>
									<div class="form-check checkbox checkbox-primary">

										<label class="check-box-label">
											<input class="form-check-input choosenResource" type="checkbox"  checked="checked" data-bind="text: name"/>
											<span class="label-text" data-bind="text: name"></span>
										</label>
									</div>
								</li>
							</ul>

							<button class="btn btn-default datepicker-btn mr-1 mt-1 mb-1">
								<i class="far fa-calendar-alt"></i> Velg dato
							</button>
						</div>
				</div>

					<div class="col-6 event-color-desc mt-2">
						<div class="d-block">
							<div class="square allocation"></div>
							<span>
								<xsl:value-of select="php:function('lang', 'allocation')"/>
							</span>
						</div>
						<div class="d-block">
							<div class="square booking"></div>
							<span>
								<xsl:value-of select="php:function('lang', 'Booking (2018)')"/>
							</span>
						</div>
						<div class="d-block">
							<div class="square event"></div>
							<span>
								<xsl:value-of select="php:function('lang', 'event')"/>
							</span>
						</div>
					</div>
			</xsl:if>



			<!--<div class="input-group date" id="datepicker" data-provide="datepicker">
				<input type="text" class="form-control" />
				<div class="input-group-addon">
					<span class="glyphicon glyphicon-th"></span>
				</div>
			</div>-->


			<xsl:if test="building/deactivate_calendar=0">
			<div id="myScheduler" class="d-none d-lg-block margin-top-and-bottom"></div>
			<div id="mySchedulerSmallDeviceView" class="d-lg-none margin-top-and-bottom"></div>
			</xsl:if>

		</div>


		<div class="push"></div>
	</div>


	<div id="lightbox" class="modal hide" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-body lightbox-body">
				<a href="#" class="close">&#215;</a>
				<img src="" alt="" />
			</div>
		</div>
	</div>

</div>
	<script type="text/javascript">
		var deactivate_calendar = <xsl:value-of select="building/deactivate_calendar" />;
		var script = document.createElement("script");
		script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/building.js";

		document.head.appendChild(script);
	</script>
</xsl:template>
