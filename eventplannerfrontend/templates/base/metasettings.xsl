<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content" class="content">
		<xsl:call-template name="msgbox"/>
		<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="meta/tabs"/>
				<div id="meta" class="booking-container">
					<div class="pure-control-group">
						<label for="field_metatag_author">
							<xsl:value-of select="php:function('lang', 'Author')"/>
						</label>
						<input id="field_metatag_author" name="metatag_author" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/metatag_author"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_metatag_robots">
							<xsl:value-of select="php:function('lang', 'Robots')"/>
						</label>
						<input id="field_metatag_robots" name="metatag_robots" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/metatag_robots"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_metatag_keywords">
							<xsl:value-of select="php:function('lang', 'keywords')"/>
						</label>
						<input id="field_metatag_keywords" name="metatag_keywords" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/metatag_keywords"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_metatag_description">
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>
						<div class="pure-custom">
							<textarea id="field_metatag_description" class="full-width" name="metatag_description">
								<xsl:value-of select="config_data/metatag_description"/>
							</textarea>
						</div>
					</div>

					<div class="pure-control-group">
						<label for="field_frontpage_text">
							<xsl:value-of select="php:function('lang', 'Frontpage text')"/>
						</label>
						<div class="pure-custom">
							<textarea id="field_frontpage_text" class="full-width" name="frontpage_text">
								<xsl:value-of disable-output-escaping="yes" select="config_data/frontpage_text"/>
							</textarea>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="application_condition">
							<xsl:value-of select="php:function('lang', 'application condition')"/>
						</label>
						<div class="pure-custom">
							<textarea id="application_condition" class="full-width" name="application_condition">
								<xsl:value-of disable-output-escaping="yes" select="config_data/application_condition"/>
							</textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="form-buttons">
				<input type="submit" class="button pure-button pure-button-primary">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Save')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>








