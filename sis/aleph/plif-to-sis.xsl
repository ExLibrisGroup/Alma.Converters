<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" standalone="yes"/> 	
	<xsl:template match="/">	
		<users>
			<xsl:for-each select="p-file-20/patron-record">
				<user>
					<record_type>PUBLIC</record_type>	
					<primary_id>
						<xsl:value-of select="z303/z303-id"/>
					</primary_id>
					<first_name>
						<xsl:value-of select="z303/Z303-FIRST-NAME"/>
					</first_name>
					<middle_name>
						<!--N/A -->
					</middle_name>
					<last_name>
						<xsl:value-of select="z303/Z303-LAST-NAME"/>
					</last_name>
					<full_name>
						<xsl:value-of select="z303/z303-name"/>	
					</full_name>
					<pin_number>	
						<xsl:value-of select="z308/Z308-VERIFICATION"/>
					</pin_number>	
					<job_category>
						<!--N/A -->	
					</job_category>
					<job_description>
						<!--N/A -->
					</job_description>
					<gender>	
						<xsl:value-of select="z303/z303-gender"/>
					</gender>		
					<xsl:for-each select="./z305">		
						<xsl:if test="contains(z305-sub-library,'50')">				
							<xsl:if test="z305-bor-status = 07">			
								<user_group>FACULTY</user_group>
							</xsl:if>
						</xsl:if>
					</xsl:for-each>		
					<campus_code>
						<!--we have made assamption that we can use z303-home-library-->	
						<xsl:value-of select="z303/z303-home-library"/>
					</campus_code>
					<web_site_url>
						<!--N/A -->	
					</web_site_url>
					<cataloger_level>
						<!--Come from Z66-->	
					</cataloger_level>								

					<preferred_language>
						<xsl:choose>
							<xsl:when test="z303/z303-con-lng ='HEB'">
							he
							</xsl:when>
							<xsl:when test="z303/z303-con-lng ='ENG'">
							en
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="z303/z303-con-lng"/>
							</xsl:otherwise>
						</xsl:choose>	
					</preferred_language>	

					<xsl:if test="z303/z303-birth-date[string-length(text()) > 0]">
						<birth_date>
							<xsl:value-of select="concat(substring(z303/z303-birth-date, 1, 4), '-', substring(z303/z303-birth-date, 5, 2), '-', substring(z303/z303-birth-date, 7, 2))"/>
						</birth_date>
					</xsl:if>					
					<xsl:for-each select="./z305">		
						<xsl:if test="contains(z305-sub-library,'50')">
							<!--from XXX50 z305, need to add condition-->
							<xsl:if test="z305/z305-expiry-date[string-length(text()) > 0]">
								<expiry_date>
									<xsl:value-of select="concat(substring(z305/z305-expiry-date, 1, 4), '-', substring(z305/z305-expiry-date, 5, 2), '-', substring(z305/z305-expiry-date, 7, 2))"/>							
								</expiry_date>
							</xsl:if>	
						</xsl:if>
					</xsl:for-each>	
					<xsl:for-each select="./z305">			
						<xsl:sort select="z305-expiry-date" order="descending" />					
						<xsl:if test="position() = 1">
							<xsl:if test="z305/z305-expiry-date[string-length(text()) > 0]">
								<purge_date>
									<xsl:value-of select="concat(substring(z305/z305-expiry-date, 1, 4), '-', substring(z305/z305-expiry-date, 5, 2), '-', substring(z305/z305-expiry-date, 7, 2))"/>							
								</purge_date>
							</xsl:if>	
						</xsl:if>          						
					</xsl:for-each>		
					<account_type>INTERNAL</account_type>
					<external_id>
						<!--The external system from which the user was loaded into Alma. Relevant only for External users.
		This field is mandatory during the POST and PUT actions for external users, and must match a valid SIS external system profile.
		On SIS load, it is filled with the SIS profile code.-->	
					</external_id>
					<xsl:for-each select="./z308">	
						<xsl:if test="contains(z308-key-type,'00')">
							<password>
								<!-- from which key type of z308 -->
								<xsl:value-of select="z308-verification"/>
							</password>
						</xsl:if>          						
					</xsl:for-each>	
					<force_password_change>
						<!--N/A -->
					</force_password_change>
					<status>ACTIVE</status>
					<contact_info>		
						<addresses>
							<address preferred="true">
								<line1>
									<xsl:value-of select="z304/z304-address-0"/>
								</line1>					
								<xsl:if test="z304/z304-address-1[string-length(text()) > 0]">
									<line2>
										<xsl:value-of select="z304/z304-address-1"/>
									</line2>
								</xsl:if>
								<xsl:if test="z304/z304-address-2[string-length(text()) > 0]">
									<line3>
										<xsl:value-of select="z304/z304-address-2"/>
									</line3>
								</xsl:if>
								<xsl:if test="z304/z304-address-3[string-length(text()) > 0]">
									<line4>
										<xsl:value-of select="z304/z304-address-3"/>	
									</line4>
								</xsl:if>
								<xsl:if test="z304/z304-address-4[string-length(text()) > 0]">
									<line5>
										<xsl:value-of select="z304/z304-address-4"/>
									</line5>								
								</xsl:if>
								<city>
									<!--stored in adress-->
								</city>				
								<state_province>
									<!--stored in adress-->
								</state_province>				
								<postal_code>
									<!--stored in adress-->
								</postal_code>
								<country>
									<!--stored in adress-->
								</country>
								<address_note>
									<!--stored in adress-->
								</address_note>
								<xsl:if test="z304/z304-date-from[string-length(text()) > 0]">
									<start_date>
										<xsl:value-of select="concat(substring(z304/z304-date-from, 1, 4), '-', substring(z304/z304-date-from, 5, 2), '-', substring(z304/z304-date-from, 7, 2))"/>															
									</start_date>
								</xsl:if>	
								<xsl:if test="z304/z304-date-to[string-length(text()) > 0]">
									<end_date>
										<xsl:value-of select="concat(substring(z304/z304-date-to, 1, 4), '-', substring(z304/z304-date-to, 5, 2), '-', substring(z304/z304-date-to, 7, 2))"/>							
									</end_date>	
								</xsl:if>									
								<!--Type of address.Values are: 01 = Permanent address 02 = Mailing address nn = Other-->
								<address_types>	                   
									<address_type>home</address_type>					
								</address_types>
							</address>					
						</addresses>
						<emails>
							<email preferred="true">
								<email_address>
									<xsl:value-of select="z304/z304-email-address"/>
								</email_address>
								<description>
									<!--N/A -->
								</description>
								<email_types>
									<!--Type of address.Values are: 01 = Permanent address 02 = Mailing address nn = Other-->
									<email_type>personal</email_type>
								</email_types>
							</email>
						</emails>
						<phones>
							<xsl:if test="z304/z304-telephone[string-length(text()) > 0]">
								<phone preferred="true">
									<phone_number>
										<xsl:value-of select="z304/z304-telephone"/>
									</phone_number>
									<phone_types>
										<!--Type of address.Values are: 01 = Permanent address 02 = Mailing address nn = Other-->
										<phone_type>home</phone_type>
									</phone_types>
								</phone>				
							</xsl:if>	
							<xsl:if test="z304/z304-telephone-2[string-length(text()) > 0]">
								<phone>
									<phone_number>
										<xsl:value-of select="z304/z304-telephone-2"/>
									</phone_number>
									<phone_types>
										<!--Type of address.Values are: 01 = Permanent address 02 = Mailing address nn = Other-->
										<phone_type>mobile</phone_type>
									</phone_types>
								</phone>				
							</xsl:if>	
							<xsl:if test="z304/z304-telephone-3[string-length(text()) > 0]">
								<phone>
									<phone_number>
										<xsl:value-of select="z304/z304-telephone-3"/>
									</phone_number>
									<phone_types>
										<!--Type of address.Values are: 01 = Permanent address 02 = Mailing address nn = Other-->
										<phone_type>office</phone_type>
									</phone_types>
								</phone>				
							</xsl:if>	
						</phones>				
					</contact_info>
					<xsl:if test="z308/z308-id != z303/z303-id">	
						<user_identifiers>
							<user_identifier>	
								<id_type>
									<xsl:value-of select="z308/z308-key-type"/>
								</id_type>				
								<value>
									<xsl:value-of select="z308/z308-key-data"/>
								</value>
								<xsl:if test="z308/z308-status= 'AC'">								
									<status>ACTIVE</status>
								</xsl:if>
							</user_identifier>						
						</user_identifiers>
					</xsl:if>		
					<!--ask migration team-->
					<user_blocks>
						<xsl:if test="z303/z303-delinq-1[string-length(text()) > 0]">					
							<user_block>
								<block_type>
									<xsl:value-of select="z303/z303-delinq-1"/>
								</block_type>
								<block_description>
									<xsl:value-of select="z303/z303-delinq-n-1"/>
								</block_description>
								<block_status>ACTIVE</block_status>
								<created_by>exl_impl</created_by>			
							</user_block>
						</xsl:if>	
						<xsl:if test="z303/z303-delinq-2[string-length(text()) > 0]">		
							<user_block>
								<block_type>
									<xsl:value-of select="z303/z303-delinq-2"/>
								</block_type>
								<block_description>
									<xsl:value-of select="z303/z303-delinq-n-2"/>
								</block_description>
								<block_status>ACTIVE</block_status>
								<created_by>exl_impl</created_by>			
							</user_block>
						</xsl:if>		
						<xsl:if test="z303/z303-delinq-3[string-length(text()) > 0]">		
							<user_block>
								<block_type>
									<xsl:value-of select="z303/z303-delinq-3"/>
								</block_type>
								<block_description>
									<xsl:value-of select="z303/z303-delinq-n-3"/>
								</block_description>
								<block_status>ACTIVE</block_status>
								<created_by>exl_impl</created_by>			
							</user_block>
						</xsl:if>
						<xsl:for-each select="./z305">		
							<xsl:if test="contains(z305-sub-library,'50')">				

								<xsl:if test="z305-delinq-1[string-length(text()) > 0]">							
									<user_block>
										<block_type>
											<xsl:value-of select="z305-delinq-1"/>
										</block_type>
										<block_description>
											<xsl:value-of select="z305-delinq-n-1"/>
										</block_description>
										<block_status>ACTIVE</block_status>
										<created_by>exl_impl</created_by>			
									</user_block>	
								</xsl:if>
								<xsl:if test="z305-delinq-2[string-length(text()) > 0]">					
									<user_block>
										<block_type>
											<xsl:value-of select="z305-delinq-2"/>
										</block_type>
										<block_description>
											<xsl:value-of select="z305-delinq-n-2"/>
										</block_description>
										<block_status>ACTIVE</block_status>
										<created_by>exl_impl</created_by>			
									</user_block>
								</xsl:if>		
								<xsl:if test="z305-delinq-3[string-length(text()) > 0]">
									<user_block>
										<block_type>
											<xsl:value-of select="z305-delinq-3"/>
										</block_type>
										<block_description>
											<xsl:value-of select="z305-delinq-n-3"/>
										</block_description>
										<block_status>ACTIVE</block_status>
										<created_by>exl_impl</created_by>			
									</user_block>					
								</xsl:if>
							</xsl:if>		
						</xsl:for-each>							
					</user_blocks>
					<user_notes>
						<xsl:if test="z303/z303-field-1[string-length(text()) > 0]">					
							<user_note>
								<note_type>Library</note_type>
								<note_text>
									<xsl:value-of select="z303/z303-field-1"/>
								</note_text>
								<user_viewable>false</user_viewable>
								<created_by>exl_impl</created_by>				
							</user_note>
						</xsl:if>
						<xsl:if test="z303/z303-field-2[string-length(text()) > 0]">					
							<user_note>
								<note_type>Library</note_type>
								<note_text>
									<xsl:value-of select="z303/z303-field-2"/>
								</note_text>
								<user_viewable>false</user_viewable>
								<created_by>exl_impl</created_by>				
							</user_note>
						</xsl:if>		
						<xsl:if test="z303/z303-field-3[string-length(text()) > 0]">
							<user_note>
								<note_type>Library</note_type>
								<note_text>
									<xsl:value-of select="z303/z303-field-3"/>
								</note_text>
								<user_viewable>false</user_viewable>
								<created_by>exl_impl</created_by>				
							</user_note>
						</xsl:if>
						<xsl:for-each select="./z305">		
							<xsl:if test="contains(z305-sub-library,'50')">				

								<xsl:if test="z303-field-1[string-length(text()) > 0]">
									<user_note>
										<note_type>Library</note_type>
										<note_text>
											<xsl:value-of select="z305-field-1"/>
										</note_text>
										<user_viewable>false</user_viewable>
										<created_by>exl_impl</created_by>				
									</user_note>
								</xsl:if>		
								<xsl:if test="z305-field-2[string-length(text()) > 0]">
									<user_note>
										<note_text>
											<xsl:value-of select="z305-field-2"/>
										</note_text>
										<user_viewable>true</user_viewable>
										<created_by>exl_impl</created_by>				
									</user_note>
								</xsl:if>		
								<xsl:if test="z305-field-3[string-length(text()) > 0]">
									<user_note>
										<note_text>
											<xsl:value-of select="z305-field-3"/>
										</note_text>
										<user_viewable>true</user_viewable>
										<created_by>exl_impl</created_by>				
									</user_note>
								</xsl:if>		
								<xsl:if test="z303/z303-field-1[string-length(text()) > 0]">
									<user_note>				
										<note_text>
											<xsl:value-of select="z305/z305-field-1"/>
										</note_text>
										<user_viewable>true</user_viewable>
										<created_by>exl_impl</created_by>				
									</user_note>
								</xsl:if>	
							</xsl:if>		
						</xsl:for-each>								
					</user_notes>
					<user_statistics>
						<xsl:for-each select="./z305">		
							<xsl:if test="contains(z305-sub-library,'50')">				
								<xsl:if test="z305-bor-type= 105">
									<user_statistic>										
										<statistic_category>Graduate</statistic_category>
										<statistic_note/>
										<!--N/A -->										
									</user_statistic>	
								</xsl:if>
							</xsl:if>		
						</xsl:for-each>		
					</user_statistics>		
				</user>
			</xsl:for-each>
		</users>

	</xsl:template>
</xsl:stylesheet>
