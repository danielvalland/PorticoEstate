<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:variable name="lang_select_activity">
				<xsl:value-of select="php:function('lang', '-- select an activity --')" />
			</xsl:variable>
			<xsl:value-of disable-output-escaping="yes" select="agegroup/tabs"/>
			<div id="agegroup_add" class="booking-container">
				<div class="pure-control-group">
					<label for="field_activity">
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<select name="activity_id" id="field_activity">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please select an activity')" />
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="$lang_select_activity" />
						</option>
						<xsl:for-each select="activities">
							<option>
								<xsl:if test="selected = 1">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="value">
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<xsl:value-of select="name"/>
							</option>
						</xsl:for-each>
					</select>
				</div>
				<div class="pure-control-group">
					<label for="field_name">
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<input id="field_name" name="name" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a name')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="agegroup/name"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_sort">
						<xsl:value-of select="php:function('lang', 'Sort order')" />
					</label>
					<input id="field_sort" name="sort" type="text" value="{agegroup/sort}">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a sort order')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_description">
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<textarea rows="5" id="field_description" name="description">
						<xsl:value-of select="agegroup/description"/>
					</textarea>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Create')" />
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="agegroup/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</xsl:template>
