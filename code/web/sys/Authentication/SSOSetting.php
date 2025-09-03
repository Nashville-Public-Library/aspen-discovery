<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/Authentication/SSOMapping.php';

class SSOSetting extends DataObject {
	public $__table = 'sso_setting';
	public $id;
	public $name;
	public $service;
	public $staffOnly;
	public $bypassAspenLogin;
	public $bypassAspenPatronLogin;
	public $ssoAuthOnly;
    public $forceReAuth;
	public $restrictByIP;
	public $updateAccount;
	public $createUserInIls;

	//oAuth
	public $clientId;
	public $clientSecret;
	public $oAuthGateway;
	public $mappingSettingId;
	public $oAuthStaffPTypeAttr;
	public $oAuthStaffPTypeAttrValue;
	public $oAuthStaffPType;

	//oAuth Custom Gateway
	public $oAuthAuthorizeUrl;
	public $oAuthAccessTokenUrl;
	public $oAuthResourceOwnerUrl;
	public $oAuthLogoutUrl;
	public $oAuthScope;
	public $oAuthGrantType;
	public $oAuthPrivateKeys;
	public $oAuthGatewayLabel;
	public $oAuthGatewayIcon;
	public $oAuthButtonBackgroundColor;
	public $oAuthButtonTextColor;

	//SAML
	public $ssoName;
	public $ssoXmlUrl;
	public $ssoUniqueAttribute;
	public $ssoILSUniqueAttribute;
	public $ssoMetadataFilename;
	public $ssoEntityId;
	public $ssoUseGivenUserId;
	public $ssoIdAttr;
	public $ssoUseGivenUsername;
	public $ssoUsernameAttr;
	public $ssoUsernameFormat;
	public $ssoFirstnameAttr;
	public $ssoLastnameAttr;
	public $ssoEmailAttr;
	public $ssoDisplayNameAttr;
	public $ssoPhoneAttr;
	public $ssoPatronTypeAttr;
	public $ssoPatronTypeFallback;
	public $ssoAddressAttr;
	public $ssoCityAttr;
	public $ssoLibraryIdAttr;
	public $ssoLibraryIdFallback;
	public $ssoCategoryIdAttr;
	public $samlStudentPTypeAttr;
	public $samlStudentPTypeAttrValue;
	public $samlStudentPType;
	public $samlStaffPTypeAttr;
	public $samlStaffPTypeAttrValue;
	public $samlStaffPType;
	public $ssoCategoryIdFallback;
	public $samlMetadataOption;
	public $samlBtnIcon;
	public $samlBtnBgColor;
	public $samlBtnTextColor;
	public $ssoSPLogoutUrl;

	//LDAP
	public $ldapHosts;
	public $ldapUsername;
	public $ldapPassword;
	public $ldapBaseDN;
	public $ldapIdAttr;
	public $ldapOrgUnit;
	public $ldapLabel;

	public $loginHelpText;
	public $loginOptions;

	private $_libraries;
	private $_dataMapping;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		global $serverName;
		$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Administer All Libraries'));
		$patronLocationList = Location::getLocationList(false, true);
		$fieldMapping = SSOMapping::getObjectStructure($context);
		$ptypeListForStaff = PType::getPatronTypeList(true, true);
		$ptypeListForPatrons = PType::getPatronTypeList(false, true);

		$services = [
			'0' => '',
			'oauth' => 'OAuth 2.0',
			'saml' => 'SAML 2',
			'ldap' => 'LDAP'
		];

		$saml_metadata_options = [
			'url' => 'By URL',
			'file' => 'Uploaded File'
		];

		$oauth_gateways = [
			'google' => 'Google',
			'custom' => 'Custom',
		];

		$login_options = [
			'0' => 'Both SSO and Local Login',
			'1' => 'Only SSO Login',
		];

		$authentication_grant_type = [
			'0' => 'By Authorization Code (Standard)',
			'1' => 'By Resource Owner Credentials',
			'2' => 'By Client Credentials',
		];

		$username_format = [
			'0' => 'Default for ILS',
			'1' => 'email',
			'2' => 'firstname.lastname',
		];

		$uid_ils_options = [
			'' => 'Use Default',
			'cardnumber' => 'Cardnumber',
			'borrowernumber' => 'Borrower Number',
			'email' => 'Email',
			'sort1' => 'Sort 1',
			'sort2' => 'Sort 2',
		];

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'The name of the setting',
				'maxLength' => 50,
			],
			'service' => [
				'property' => 'service',
				'type' => 'enum',
				'label' => 'Service',
				'values' => $services,
				'description' => 'The service used for authenticating users',
				'default' => '0',
				'onchange' => 'return AspenDiscovery.Admin.getSSOFields();',
			],
			'loginOptions' => [
				'property' => 'loginOptions',
				'type' => 'enum',
				'label' => 'Available Options at Login',
				'values' => $login_options,
				'description' => 'The login options available to users when logging in',
				'default' => '0',
				'hideInLists' => true,
			],
			'loginHelpText' => [
				'property' => 'loginHelpText',
				'type' => 'textarea',
				'label' => 'Login Help Text',
				'description' => 'Additional information provided to users when logging in',
				'hideInLists' => true,
			],
			'staffOnly' => [
				'property' => 'staffOnly',
				'type' => 'checkbox',
				'label' => 'Only allow for staff',
				'description' => 'Whether or not only staff should be able to use single sign-on',
				'note' => 'This hides the single sign-on option from the patron-facing login screens',
			],
			'bypassAspenLogin' => [
				'property' => 'bypassAspenLogin',
				'type' => 'checkbox',
				'label' => 'Bypass the Aspen Discovery staff login page when using footer link',
				'description' => 'Whether or not the staff login link in the footer should first send the user to the Aspen Discovery login page',
				'note' => 'Enable this to skip the traditional Aspen Discovery login and redirect the staff user to your single sign-on portal'
			],
			'bypassAspenPatronLogin' => [
				'property' => 'bypassAspenPatronLogin',
				'type' => 'checkbox',
				'label' => 'Bypass the Aspen Discovery patron login page',
				'description' => 'Whether or not the patron login should first send the user to the Aspen Discovery login page',
				'note' => 'Enable this to skip the traditional Aspen Discovery login and redirect the user to your single sign-on portal'
			],
			'ssoAuthOnly' => [
				'property' => 'ssoAuthOnly',
				'type' => 'checkbox',
				'label' => 'Only authenticate users with single sign-on',
				'description' => 'Whether or not users are authenticated only by single sign-on',
				'note' => 'Aspen will not authenticate with the ILS when a user logs in with single sign-on. <em>This has potential security implications</em>',
				'onchange' => 'return AspenDiscovery.Admin.checkSSOAuthOnlyPatronTypes();',
			],
            'forceReAuth' => [
                'property' => 'forceReAuth',
                'type' => 'checkbox',
                'label' => 'Force users to re-authenticate with the IdP when logging into Aspen',
                'description' => 'Whether or not users must re-authenticate with the IdP each time they log into Aspen',
                'note' => 'If enabled, users will have to authenticate with the IdP each time they go to log back into Aspen. This is useful for single sign-on being used on public machines.',
            ],
			'restrictByIP' => [
				'property' => 'restrictByIP',
				'type' => 'enum',
				'values' => [
					0 => 'Allowed from all IP addresses',
					1 => 'Allowed from enabled IP addresses',
				],
				'label' => 'Restrict single sign-on by IP address',
				'description' => 'Whether or not to restrict the ability to use the SSO login by IP address.',
				'hideInLists' => true,
				'default' => true,
			],
			'updateAccount' => [
				'property' => 'updateAccount',
				'type' => 'checkbox',
				'label' => 'Update users ILS account information with data from the IdP when logging in using the data mapping provided',
				'description' => 'Whether or not users ILS account information is updated each time they log in using the data mapping provided',
				'default' => 0,
			  ],
			  'createUserInIls' => [
				'property' => 'createUserInIls',
				'type' => 'checkbox',
				'label' => 'Create ILS users when a matching user is not found from the IdP data in the ILS',
				'description' => 'Whether or not to automatically create the ILS user if no match is found between IdP data and the ILS.',
				'default' => 1,
				'note' => 'If the user does not exist in the ILS when we sign in to Aspen with SSO, whether we can create that user in the ILS'
			  ],
			'oAuthConfigSection' => [
				'property' => 'oAuthConfigSection',
				'type' => 'section',
				'label' => 'oAuth 2.0 Configuration',
				'renderAsHeading' => true,
				'showBottomBorder' => true,
				'properties' => [
					'oAuthGateway' => [
						'property' => 'oAuthGateway',
						'type' => 'enum',
						'label' => 'Gateway',
						'values' => $oauth_gateways,
						'description' => 'The gateway provider used for authenticating users',
						'default' => 'google',
						'hideInLists' => true,
						'onchange' => 'return AspenDiscovery.Admin.toggleOAuthGatewayFields();',
					],
					'clientId' => [
						'property' => 'clientId',
						'type' => 'text',
						'label' => 'Client ID',
						'description' => 'Client ID used for accessing the gateway provider',
						'hideInLists' => true,
					],
					'clientSecret' => [
						'property' => 'clientSecret',
						'type' => 'storedPassword',
						'label' => 'Client Secret',
						'required' => false,
						'description' => 'Client secret used for accessing the gateway provider',
						'hideInLists' => true,
					],
					'oAuthCustomGatewayOptionsSection' => [
						'property' => 'oAuthCustomGatewayOptionsSection',
						'type' => 'section',
						'label' => 'Custom Gateway Options',
						'renderAsHeading' => true,
						'headingLevel' => 'h3',
						'showBottomBorder' => true,
						'properties' => [
							'oAuthGatewayLabel' => [
								'property' => 'oAuthGatewayLabel',
								'type' => 'text',
								'label' => 'Custom Gateway Label',
								'description' => 'The public-facing name for the custom gateway',
								'hideInLists' => true,
							],
							'oAuthAuthorizeUrl' => [
								'property' => 'oAuthAuthorizeUrl',
								'type' => 'url',
								'label' => 'Custom Gateway Authorization Url',
								'description' => 'The API url used as the main entry point for requesting authorization',
								'hideInLists' => true,
							],
							'oAuthAccessTokenUrl' => [
								'property' => 'oAuthAccessTokenUrl',
								'type' => 'url',
								'label' => 'Custom Gateway Access Token Url',
								'description' => 'The API url used to connect and exchange the authorization code for an access token.',
								'hideInLists' => true,
							],
							'oAuthResourceOwnerUrl' => [
								'property' => 'oAuthResourceOwnerUrl',
								'type' => 'url',
								'label' => 'Custom Gateway Resource Owner Url',
								'description' => 'The API url used to access the user details',
								'hideInLists' => true,
							],
							'oAuthLogoutUrl' => [
								'property' => 'oAuthLogoutUrl',
								'type' => 'url',
								'label' => 'Custom Gateway Logout Url',
								'description' => 'The API url used to invalidate a session and force a user to logout',
								'hideInLists' => true,
							],
							'oAuthScope' => [
								'property' => 'oAuthScope',
								'type' => 'text',
								'label' => 'Custom Gateway Scopes',
								'description' => 'Granular permissions the API client needs to access data',
								'hideInLists' => true,
							],
							'oAuthGrantType' => [
								'property' => 'oAuthGrantType',
								'type' => 'enum',
								'label' => 'Authentication Grant Type',
								'values' => $authentication_grant_type,
								'description' => 'The grant type used when obtaining an access token.',
								'default' => 0,
								'hideInLists' => true,
								'onchange' => 'return AspenDiscovery.Admin.toggleOAuthPrivateKeysField();',
							],
							'oAuthPrivateKeys' => [
								'property' => 'oAuthPrivateKeys',
								'type' => 'file',
								'label' => 'Private Keys PEM File',
								'description' => 'A .PEM file that contains private keys to access a client secret.',
								'hideInLists' => true,
							],
							'oAuthLoginButtonOptionsSection' => [
								'property' => 'oAuthLoginButtonOptionsSection',
								'type' => 'section',
								'label' => 'Login Button Options',
								'hideInLists' => true,
								'renderAsHeading' => true,
								'headingLevel' => 'h4',
								'showBottomBorder' => true,
								'properties' => [
									'oAuthGatewayIcon' => [
										'property' => 'oAuthGatewayIcon',
										'type' => 'image',
										'label' => 'Button Icon',
										'description' => 'An icon representing the custom gateway',
										'hideInLists' => true,
										'thumbWidth' => 32,
									],
									'oAuthButtonBackgroundColor' => [
										'property' => 'oAuthButtonBackgroundColor',
										'type' => 'text',
										'label' => 'Button Background Color',
										'description' => 'Custom Gateway Button Background Color',
										'hideInLists' => true,
									],
									'oAuthButtonTextColor' => [
										'property' => 'oAuthButtonTextColor',
										'type' => 'text',
										'label' => 'Button Text Color',
										'description' => 'Custom Gateway Button Foreground Color',
										'hideInLists' => true,
									],
								]
							],
						]
					],
					'oAuthStaffSection' => [
						'property' => 'oAuthStaffSection',
						'type' => 'section',
						'label' => 'Staff Users',
						'renderAsHeading' => true,
						'showBottomBorder' => true,
						'headingLevel' => 'h3',
						'note' => 'Leave empty to assign all users the default self-registered patron type. Users will also be assigned any roles and permissions tied to this patron type',
						'properties' => [
							'oAuthStaffPTypeAttr' => [
								'property' => 'oAuthStaffPTypeAttr',
								'type' => 'text',
								'label' => 'oAuth property that determines if the user should be a staff patron type',
								'description' => 'The user\'s patron category id, this should be an id that is recognised by your LMS/ILS. If this is not supplied, please provide a fallback value below',
								'size' => '512',
								'hideInLists' => true,
							],
							'oAuthStaffPTypeAttrValue' => [
								'property' => 'oAuthStaffPTypeAttrValue',
								'type' => 'text',
								'label' => 'The value from the oAuth property that determines if the user should be a staff patron type',
								'description' => 'The value from the IdP attribute defined in the previous field that determines if the user should be assigned a staff patron type',
								'size' => '512',
								'hideInLists' => true,
							],
							'oAuthStaffPType' => [
								'property' => 'oAuthStaffPType',
								'type' => 'enum',
								'values' => $ptypeListForStaff,
								'label' => 'Default staff patron type',
								'description' => 'Assign staff users a different patron type than other users',
								'hideInLists' => true,
							]
						]
					],
				]
			],
			'samlConfigSection' => [
				'property' => 'samlConfigSection',
				'type' => 'section',
				'label' => 'SAML 2 Configuration',
				'renderAsHeading' => true,
				'showBottomBorder' => true,
				'properties' => [
					'serviceProviderSection' => [
						'property' => 'serviceProviderSection',
						'type' => 'section',
						'label' => 'Aspen Discovery Service Provider Details',
						'properties' => [
							'idpACSUrl' => [
								'property' => 'idpACSUrl',
								'type' => 'text',
								'label' => 'ACS Url',
								'readOnly' => true,
								'hideInLists' => true,
							],
							'idpEntityId' => [
								'property' => 'idpEntityId',
								'type' => 'text',
								'label' => 'Entity Id',
								'readOnly' => true,
								'hideInLists' => true,
							],
							'idpSLSUrl' => [
								'property' => 'idpSLSUrl',
								'type' => 'text',
								'label' => 'SLS Url',
								'readOnly' => true,
								'hideInLists' => true,
							],
							'idpNameIDFormat' => [
								'property' => 'idpNameIDFormat',
								'type' => 'text',
								'label' => 'Name ID Format',
								'readOnly' => true,
								'hideInLists' => true,
							],
						],
					],
					'ssoName' => [
						'property' => 'ssoName',
						'type' => 'text',
						'label' => 'SAML Service Label',
						'description' => 'The name to be displayed when referring to the authentication service',
						'size' => '512',
						'hideInLists' => true,
					],
					'samlMetadataOption' => [
						'property' => 'samlMetadataOption',
						'type' => 'enum',
						'label' => 'Provide XML metadata by URL or Uploaded File?',
						'values' => $saml_metadata_options,
						'description' => 'The gateway provider used for authenticating users',
						'default' => 'url',
						'hideInLists' => true,
						'onchange' => 'return AspenDiscovery.Admin.toggleSamlMetadataFields();',
					],
					'ssoXmlUrl' => [
						'property' => 'ssoXmlUrl',
						'type' => 'text',
						'label' => 'URL of service metadata XML',
						'description' => 'The URL at which the metadata XML document for this identity provider can be obtained',
						'size' => '512',
						'hideInLists' => true,
					],
					'ssoMetadataFilename' => [
						'path' => "/data/aspen-discovery/$serverName/sso_metadata/",
						'property' => 'ssoMetadataFilename',
						'type' => 'file',
						'label' => 'XML metadata file',
						'description' => 'The XML metadata file if no URL is available',
						'hideInLists' => true,
					],
					'ssoEntityId' => [
						'property' => 'ssoEntityId',
						'type' => 'text',
						'label' => 'Entity ID of SSO provider',
						'description' => 'The entity ID of the SSO IdP. This can be found in the IdP\'s metadata',
						'note' => 'This can be found in the IdP\'s metadata',
						'size' => '512',
						'hideInLists' => true,
					],
					'ssoSPLogoutUrl' => [
						'property' => 'ssoSPLogoutUrl',
						'type' => 'text',
						'label' => 'Logout Url for SP',
						'description' => 'Provide the URL to logout the user from the service provider if needed',
						'hideInLists' => true,
						'note' => 'In some cases such as Google SAML, we may need to logout the user from Google separately to force a new authentication when logging back in. Leave blank to not use.'
					],
					'ssoProfileSection' => [
						'property' => 'ssoProfileSection',
						'type' => 'section',
						'label' => 'User Data Mapping',
						'hideInLists' => true,
						'renderAsHeading' => true,
						'headingLevel' => 'h3',
						'showBottomBorder' => true,
						'properties' => [
							'ssoUniqueAttribute' => [
								'property' => 'ssoUniqueAttribute',
								'type' => 'text',
								'label' => 'IdP attribute that uniquely identifies a user',
								'description' => 'This should be unique to each user',
								'note' => 'This should be unique to each user',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoILSUniqueAttribute' => [
								'property' => 'ssoILSUniqueAttribute',
								'type' => 'enum',
								'values' => $uid_ils_options,
								'label' => 'ILS attribute that uniquely identifies a user',
								'description' => 'This should be unique to each user',
								'note' => 'This should be unique to each user and match the value that is provided by the unique IdP attribute.',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoUserIdSection' => [
								'property' => 'ssoUserIdSection',
								'type' => 'section',
								'label' => 'Barcode / User Id',
								'hideInLists' => true,
								'expandByDefault' => true,
								'properties' => [
									'ssoUseGivenUserId' => [
										'property' => 'ssoUseGivenUserId',
										'type' => 'checkbox',
										'label' => 'Create new users with a cardnumber (user id) provided by the IdP',
										'description' => 'Whether or not new users should use a cardnumber (user id) provided by the IdP',
										'hideInLists' => true,
										'onchange' => 'return AspenDiscovery.Admin.toggleSamlUserIdFields();',
									],
									'ssoIdAttr' => [
										'property' => 'ssoIdAttr',
										'type' => 'text',
										'label' => 'IdP attribute that contains the user id',
										'description' => 'This should be unique to each user',
										'size' => '512',
										'hideInLists' => true,
									]
								]
							],
							'ssoUsernameSection' => [
								'property' => 'ssoUsernameSection',
								'type' => 'section',
								'label' => 'Username',
								'hideInLists' => true,
								'expandByDefault' => true,
								'properties' => [
									'ssoUseGivenUsername' => [
										'property' => 'ssoUseGivenUsername',
										'type' => 'checkbox',
										'label' => 'Create new users with a username provided by the IdP',
										'description' => 'Whether or not new users should use a username provided by the IdP',
										'hideInLists' => true,
										'onchange' => 'return AspenDiscovery.Admin.toggleSamlUsernameFormatFields();',
									],
									'ssoUsernameAttr' => [
										'property' => 'ssoUsernameAttr',
										'type' => 'text',
										'label' => 'IdP attribute that contains the user\'s username',
										'description' => 'The user\'s username',
										'size' => '512',
										'hideInLists' => true,
									],
									'ssoUsernameFormat' => [
										'property' => 'ssoUsernameFormat',
										'type' => 'enum',
										'values' => $username_format,
										'label' => 'Format of username',
										'description' => 'How the username for the new user should be formatted',
										'hideInLists' => true,
									],
								]
							],
							'ssoFirstnameAttr' => [
								'property' => 'ssoFirstnameAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s first name',
								'description' => 'The user\'s first name',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoLastnameAttr' => [
								'property' => 'ssoLastnameAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s last name',
								'description' => 'The user\'s last name',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoEmailAttr' => [
								'property' => 'ssoEmailAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s email address',
								'description' => 'The user\'s email address',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoDisplayNameAttr' => [
								'property' => 'ssoDisplayNameAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s display name',
								'description' => 'The user\'s display name, if one is not supplied, a name for display will be assembled from first and last names',
								'note' => 'If not provided a name for display will be assembled from first and last names',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoPhoneAttr' => [
								'property' => 'ssoPhoneAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s phone number',
								'description' => 'The user\'s phone number',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoAddressAttr' => [
								'property' => 'ssoAddressAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s address',
								'description' => 'The user\'s address',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoCityAttr' => [
								'property' => 'ssoCityAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s city',
								'description' => 'The user\'s city',
								'size' => '512',
								'hideInLists' => true,
							],
							/*'ssoPatronTypeSection' => [
								'property' => 'ssoPatronTypeSection',
								'type' => 'section',
								'label' => 'Patron type',
								'hideInLists' => true,
								'properties' => [
									'ssoPatronTypeAttr' => [
										'property' => 'ssoPatronTypeAttr',
										'type' => 'text',
										'label' => 'IdP attribute that contains the user\'s patron type',
										'description' => 'The user\'s patron type, this should be a value that is recognised by Aspen. If this is not supplied, please provide a fallback value below',
										'note' => 'This should be a value that is recognised by Aspen. If this is not supplied, please provide a fallback value below.',
										'size' => '512',
										'hideInLists' => true,
									],
									'ssoPatronTypeFallback' => [
										'property' => 'ssoPatronTypeFallback',
										'type' => 'text',
										'label' => 'A fallback value for patron type',
										'description' => 'A value to be used in the event the IdP does not supply a patron type attribute, this should be a value that is recognised by Aspen.',
										'note' => 'This should be a value that is recognised by Aspen',
										'size' => '512',
										'hideInLists' => true,
									],
								],
							],*/
						]
					],
					'ssoCategoryIdSection' => [
						'property' => 'ssoCategoryIdSection',
						'type' => 'section',
						'label' => 'Default Patron Type',
						'hideInLists' => true,
						'note' => 'Users will also be granted any roles/permissions assigned to this patron type',
						'renderAsHeading' => true,
						'headingLevel' => 'h3',
						'showBottomBorder' => true,
						'properties' => [
							'ssoCategoryIdAttr' => [
								'property' => 'ssoCategoryIdAttr',
								'type' => 'text',
								'label' => 'IdP attribute that contains the user\'s patron category id',
								'description' => 'The user\'s patron category id, this should be an id that is recognised by your LMS/ILS. If this is not supplied, please provide a fallback value below',
								'note' => 'This should be an id that is recognised by your LMS/ILS. If this is not supplied, please provide a fallback value below.',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoCategoryIdFallback' => [
								'property' => 'ssoCategoryIdFallback',
								'type' => 'enum',
								'values' => $ptypeListForPatrons,
								'label' => 'A fallback value for category ID',
								'description' => 'A value to be used in the event the IdP does not supply a category id attribute, this should be an id that is recognised by your LMS/ILS',
								'hideInLists' => true,
							],
						],
					],
					'samlStudentSection' => [
						'property' => 'samlStudentSection',
						'type' => 'section',
						'label' => 'Student Users',
						'renderAsHeading' => true,
						'showBottomBorder' => true,
						'headingLevel' => 'h3',
						'note' => 'Users will also be granted any roles/permissions assigned to this patron type',
						'properties' => [
							'samlStudentPTypeAttr' => [
								'property' => 'samlStudentPTypeAttr',
								'type' => 'text',
								'label' => 'IdP attribute that determines if the user is a student',
								'description' => 'The attribute that the IdP sends that Aspen should look at when determining if the user is a student',
								'size' => '512',
								'hideInLists' => true,
							],
							'samlStudentPTypeAttrValue' => [
								'property' => 'samlStudentPTypeAttrValue',
								'type' => 'text',
								'label' => 'The value from the IdP attribute that determines if the user is a student',
								'description' => 'The value from the IdP attribute defined in the previous field that determine sif the user should be given the patron type for students',
								'size' => '512',
								'note' => 'Use a comma to check for multiple possible values',
								'hideInLists' => true,
							],
							'samlStudentPType' => [
								'property' => 'samlStudentPType',
								'type' => 'enum',
								'values' => $ptypeListForStaff,
								'label' => 'Patron type given to student users',
								'hideInLists' => true,
							]
						],
					],
					'samlStaffSection' => [
						'property' => 'samlStaffSection',
						'type' => 'section',
						'label' => 'Staff Users',
						'renderAsHeading' => true,
						'showBottomBorder' => true,
						'headingLevel' => 'h3',
						'note' => 'Users will also be granted any roles/permissions assigned to this patron type',
						'properties' => [
							'ssoStaffPTypeAttr' => [
								'property' => 'samlStaffPTypeAttr',
								'type' => 'text',
								'label' => 'IdP attribute that determines if the user is a staff member',
								'description' => 'The attribute that the IdP sends that Aspen should look at when determining if the user is a staff member',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoStaffPTypeAttrValue' => [
								'property' => 'samlStaffPTypeAttrValue',
								'type' => 'text',
								'label' => 'The value from the IdP attribute that determines if the user is a staff member',
								'description' => 'The value from the IdP attribute defined in the previous field that determines if the user should be given the patron type for staff',
								'size' => '512',
								'note' => 'Use a comma to check for multiple possible values',
								'hideInLists' => true,
							],
							'ssoStaffPType' => [
								'property' => 'samlStaffPType',
								'type' => 'enum',
								'values' => $ptypeListForStaff,
								'label' => 'Patron type given to staff users',
								'hideInLists' => true,
							]
						]
					],
					'ssoLibraryIdSection' => [
						'property' => 'ssoLibraryIdSection',
						'type' => 'section',
						'label' => 'Library Branch',
						'hideInLists' => true,
						'renderAsHeading' => true,
						'headingLevel' => 'h3',
						'showBottomBorder' => true,
						'properties' => [
							'ssoLibraryIdAttr' => [
								'property' => 'ssoLibraryIdAttr',
								'type' => 'text',
								'label' => "IdP attribute that contains the user's library branch code/id",
								'description' => "The user's library branch code/id. If this is not supplied, please provide a fallback value below",
								'note' => 'This should be an id that is recognised by your LMS/ILS. If this is not supplied, please provide a fallback value below.',
								'size' => '512',
								'hideInLists' => true,
							],
							'ssoLibraryIdFallback' => [
								'property' => 'ssoLibraryIdFallback',
								'type' => 'enum',
								'label' => 'A fallback value for branch code/id',
								'values' => $patronLocationList,
								'description' => 'A value to be used in the event the IdP does not supply a library branch id attribute',
								'size' => '512',
								'hideInLists' => true,
							],
						],
					],
					'samlLoginButtonOptionsSection' => [
						'property' => 'samlLoginButtonOptionsSection',
						'type' => 'section',
						'label' => 'Login Button Options',
						'hideInLists' => true,
						'renderAsHeading' => true,
						'headingLevel' => 'h3',
						'showBottomBorder' => true,
						'properties' => [
							'samlBtnIcon' => [
								'property' => 'samlBtnIcon',
								'type' => 'image',
								'label' => 'Button Icon',
								'description' => 'An icon representing the SAML service',
								'hideInLists' => true,
								'thumbWidth' => 32,
							],
							'samlBtnBgColor' => [
								'property' => 'samlBtnBgColor',
								'type' => 'text',
								'label' => 'Button Background Color',
								'description' => 'Background color for SAML service login button',
								'hideInLists' => true,
							],
							'samlBtnTextColor' => [
								'property' => 'samlBtnTextColor',
								'type' => 'text',
								'label' => 'Button Text Color',
								'description' => 'Text color for SAML service login button',
								'hideInLists' => true,
							],
						]
					],
				]
			],
			'ldapConfigSection' => [
				'property' => 'ldapConfigSection',
				'type' => 'section',
				'label' => 'LDAP Configuration',
				'renderAsHeading' => true,
				'showBottomBorder' => true,
				'properties' => [
					'ldapLabel' => [
						'property' => 'ldapLabel',
						'type' => 'text',
						'label' => 'LDAP User-facing Name',
						'description' => 'What this LDAP single sign-on service will be called on the interface',
					],
					'ldapHosts' => [
						'property' => 'ldapHosts',
						'type' => 'text',
						'label' => 'LDAP Host(s)',
						'description' => 'The LDAP host(s) to connect to. To use more than one, use a space between each host name.',
						'note' => 'Example: ldaps://hostname:port',
					],
					'ldapUsername' => [
						'property' => 'ldapUsername',
						'type' => 'text',
						'label' => 'LDAP Username',
						'description' => 'LDAP RDN or DN',
					],
					'ldapPassword' => [
						'property' => 'ldapPassword',
						'type' => 'storedPassword',
						'label' => 'LDAP Password',
						'description' => 'Associated password for LDAP username',
					],
					'ldapBaseDN' => [
						'property' => 'ldapBaseDN',
						'type' => 'text',
						'label' => 'LDAP Base DN',
						'description' => 'The Base DN is the starting point an LDAP server uses when searching for users authentication within your Directory',
						'note' => 'Example: DC=example-domain,DC=com'
					],
					'ldapIdAttr' => [
						'property' => 'ldapIdAttr',
						'type' => 'text',
						'label' => 'LDAP Attribute for Id',
						'description' => 'LDAP attribute that is used to identify who the user is in the ILS',
					],
					'ldapOrgUnit' => [
						'property' => 'ldapOrgUnit',
						'type' => 'text',
						'label' => 'Applicable LDAP Org Units (OU)',
						'description' => 'Specifies which LDAP Org Units (OU) will use this authentication',
						'note' => 'Useful when the same username could be found in multiple libraries. Leave blank to not use.'
					],
				],
			],
			'dataMappingSection' => [
				'property' => 'dataMappingSection',
				'type' => 'section',
				'label' => 'Data Mapping',
				'renderAsHeading' => true,
				'showBottomBorder' => true,
				'properties' => [
					'dataMapping' => [
						'property' => 'dataMapping',
						'type' => 'oneToMany',
						'label' => 'User Profile',
						'description' => 'Define how data matches up in Aspen and/or ILS',
						'keyThis' => 'id',
						'keyOther' => 'id',
						'subObjectType' => 'SSOMapping',
						'structure' => $fieldMapping,
						'sortable' => false,
						'storeDb' => true,
						'allowEdit' => false,
						'canEdit' => false,
						'hideInLists' => true,
						'canAddNew' => false,
						'canDelete' => true,
						'additionalOneToManyActions' => [
							0 => [
								'text' => 'Reset Data Mapping to Defaults',
								'url' => '/Admin/SSOSettings?id=$id&amp;objectAction=resetDataMappingToDefault',
								'class' => 'btn-danger',
							],
						],
					],
				],
			],
			'libraries' => [
				'property' => 'libraries',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Libraries',
				'description' => 'Define libraries that use this setting',
				'values' => $libraryList,
				'hideInLists' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function __get($name) {
		global $configArray;
		if ($name == "libraries") {
			if (!isset($this->_libraries) && $this->id) {
				$this->_libraries = [];
				$obj = new Library();
				$obj->ssoSettingId = $this->id;
				$obj->find();
				while ($obj->fetch()) {
					$this->_libraries[$obj->libraryId] = $obj->libraryId;
				}
			}
			return $this->_libraries;
		} elseif ($name == "dataMapping") {
			return $this->getFieldMappings();
		} elseif ($name == 'idpEntityId') {
			return $configArray['Site']['url'] . '/Authentication/SAML2?metadata';
		} elseif ($name == 'idpACSUrl') {
			return $configArray['Site']['url'] . '/Authentication/SAML2?acs';
		} elseif ($name == 'idpSLSUrl') {
			return $configArray['Site']['url'] . '/Authentication/SAML2?sls';
		} elseif ($name == 'idpNameIDFormat') {
			return 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
		} else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == "libraries") {
			$this->_libraries = $value;
		} elseif ($name == "dataMapping") {
			$this->_dataMapping = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	public function getFieldMappings() {
		if (!isset($this->_dataMapping) && $this->id) {
			$this->_dataMapping = [];
			$dataMapping = new SSOMapping();
			$dataMapping->ssoSettingId = $this->id;
			if ($dataMapping->find()) {
				while ($dataMapping->fetch()) {
					$this->_dataMapping[$dataMapping->id] = clone $dataMapping;
				}
			} else {
				/** @var SSOMapping[] $defaultMappings */
				// Default mappings to use when no mappings are defined
				$defaultMappings = SSOMapping::getDefaults($this->id);
				foreach ($defaultMappings as $index => $attributeMap) {
					$this->_dataMapping[$attributeMap->id] = clone $attributeMap;
				}
			}
		}
		return $this->_dataMapping;
	}

	public function update(string $context = '') : int|bool {
		/* We process the SSO additional work before the DB is updated because we set 
			a value on this object which needs to be persisted to the DB */
		if($this->ssoXmlUrl) {
			$filename = $this->fetchMetadataFile();
			if(!$filename instanceof AspenError) {
				$this->ssoMetadataFilename = $filename;
			} else {
				$this->setLastError($filename->getMessage());
			}
		}
		
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveLibraries();
			$this->saveFieldMappings();
		}
		return true;
	}

	public function saveLibraries() : void {
		if (isset ($this->_libraries) && is_array($this->_libraries)) {
			$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Administer All Libraries'));
			foreach ($libraryList as $libraryId => $displayName) {
				$library = new Library();
				$library->libraryId = $libraryId;
				$library->find(true);
				if (in_array($libraryId, $this->_libraries)) {
					//We want to apply the scope to this library
					if ($library->ssoSettingId != $this->id) {
						$library->ssoSettingId = $this->id;
						$library->update();
					}
				} else {
					//It should not be applied to this scope. Only change if it was applied to the scope
					if ($library->ssoSettingId == $this->id) {
						$library->ssoSettingId = -1;
						$library->update();
					}
				}
			}
			unset($this->_libraries);
		}
	}

	public function saveFieldMappings() {
		if (isset($this->_dataMapping) && is_array($this->_dataMapping)) {
			$this->saveOneToManyOptions($this->_dataMapping, 'ssoSettingId');
			unset($this->_dataMapping);
		}
	}

	public function insert(string $context = '') : int|bool {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveLibraries();
			$this->saveFieldMappings();
		}
		return $ret;
	}

	public function getNumericColumnNames(): array {
		return ['localLogin', 'staffOnly', 'oAuthGrantType', 'ssoUseGivenUserId', 'ssoUseGivenUsername', 'ssoUsernameFormat', 'restrictByIP'];
	}

	public function genericOAuthProvider() {
		global $configArray;
		$redirectUri = $configArray['Site']['url'] . '/Authentication/OAuth';
		return [
			'urlAuthorize' => $this->oAuthAuthorizeUrl,
			'urlAccessToken' => $this->oAuthAccessTokenUrl,
			'clientId' => $this->clientId,
			'clientSecret' => $this->clientSecret ?? '',
			'redirectUri' => $redirectUri,
			'urlResourceOwnerDetails' => $this->oAuthResourceOwnerUrl,
			'scopes' => $this->oAuthScope,
		];
	}

	public function getAuthorizationUrl() {
		if ($this->oAuthGateway == "google") {
			return "https://accounts.google.com/o/oauth2/v2/auth";
		}

		return $this->oAuthAuthorizeUrl;
	}

	public function getAccessTokenUrl() {
		if ($this->oAuthGateway == "google") {
			return "https://oauth2.googleapis.com/token";
		}

		return $this->oAuthAccessTokenUrl;
	}

	public function getResourceOwnerDetailsUrl() {
		if ($this->oAuthGateway == "google") {
			return "https://openidconnect.googleapis.com/v1/userinfo";
		}

		return $this->oAuthResourceOwnerUrl;
	}

	public function getLogoutUrl() {
		if ($this->oAuthGateway == 'google') {
			return 'https://oauth2.googleapis.com/revoke?token=';
		}

		return $this->oAuthLogoutUrl;
	}

	public function getScope() {
		if ($this->oAuthGateway == "google") {
			return "openid email profile";
		}

		return $this->oAuthScope;
	}

	public function getRedirectUrl() {
		global $configArray;
		$baseUrl = $configArray['Site']['url'];
		if ($this->service == "oauth") {
			return $baseUrl . '/Authentication/OAuth';
		}

		return false;
	}

	public function getMatchpoints() {
		if($this->service == 'saml') {
			$matchpoints = [
				'ssoUniqueAttribute' => [],
				'ssoIdAttr' => [],
				'ssoUsernameAttr' => [
					'aspenUser' => 'username',
				],
				'ssoFirstnameAttr' => [],
				'ssoLastnameAttr' => [],
				'ssoEmailAttr' => [],
				'ssoDisplayNameAttr' => [
					'fallback' => [
						'propertyName' => 'ssoDisplayNameFallback',
						'func' => function ($attributes) {
							$firstName = array_key_exists($this->ssoFirstnameAttr, $attributes) ? $attributes[$this->ssoFirstnameAttr][0] : '';
							$lastName = array_key_exists($this->ssoLastnameAttr, $attributes) ? $attributes[$this->ssoLastnameAttr][0] : '';
							$comp = [
								$firstName,
								$lastName,
							];
							return implode(' ', $comp);
						},
					],
				],
				'ssoPhoneAttr' => [],
				'ssoPatronTypeAttr' => [
					'fallback' => [
						'propertyName' => 'ssoPatronTypeFallback',
					],
				],
				'ssoAddressAttr' => [],
				'ssoCityAttr' => [],
				'ssoLibraryIdAttr' => [
					'fallback' => [
						'propertyName' => 'ssoLibraryIdFallback',
					],
				],
				'ssoCategoryIdAttr' => [
					'fallback' => [
						'propertyName' => 'ssoCategoryIdFallback',
					],
				],
			];
		} else {
			$matchpoints = [
				'email' => 'email',
				'userId' => 'sub',
				'firstName' => 'given_name',
				'lastName' => 'family_name',
				'displayName' => '',
				'username' => '',
				'patronType' => '',
				'libraryCode' => ''
			];

			$mappings = new SSOMapping();
			$mappings->ssoSettingId = $this->id;
			$mappings->find();
			while ($mappings->fetch()) {
				if ($mappings->aspenField == 'email') {
					$matchpoints['email'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'user_id') {
					$matchpoints['userId'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'first_name') {
					$matchpoints['firstName'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'last_name') {
					$matchpoints['lastName'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'display_name') {
					$matchpoints['displayName'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'username') {
					$matchpoints['username'] = $mappings->responseField;
				} elseif ($mappings->aspenField == 'patron_type') {
					$matchpoints['patronType'] = $mappings->responseField;
					$matchpoints['patronType_fallback'] = $mappings->fallbackValue;
				} elseif ($mappings->aspenField == 'library_code') {
					$matchpoints['libraryCode'] = $mappings->responseField;
					$matchpoints['libraryCode_fallback'] = $mappings->fallbackValue;
				}
			}
		}

		return $matchpoints;
	}

	public function getBasicAuthToken() {
		return base64_encode($this->clientId . ":" . $this->clientSecret);
	}

	public function getAuthenticationGrantType() {
		if ($this->oAuthGateway == 'google') {
			return 0;
		}
		return $this->oAuthGrantType;
	}

	public function fetchMetadataFile() {
		global $logger;
		global $configArray;
		global $serverName;
		$xmlDataPath = '/data/aspen-discovery/' . $serverName . '/sso_metadata/';
		if(!file_exists($xmlDataPath)) {
			mkdir($xmlDataPath, 0775, true);
			chgrp($xmlDataPath, 'aspen_apache');
			chmod($xmlDataPath, 0775);
		}
		$url = trim($this->ssoXmlUrl);
		if (strlen($url) > 0) {
			// We've got a new or updated URL
			// First try and retrieve the metadata
			$curlWrapper = new CurlWrapper();
			$curlWrapper->setTimeout(10);
			$xml = $curlWrapper->curlGetPage($url);
			if (strlen($xml) > 0) {
				// Check it's a valid SAML message
				try {
					require_once ROOT_DIR . '/services/Authentication/SAML/lib/Saml2/Utils.php';
					OneLogin_Saml2_Utils::validateXML($xml, 'saml-meta');
				} catch (Exception $e) {
					$logger->log($e, Logger::LOG_ERROR);
					return new AspenError('Unable to use SSO IdP metadata, please check "URL of service metadata XML"');
				}
				$fileName = $serverName . '.xml';
				$ssoMetadataFilename = $xmlDataPath . $fileName;
				$written = file_put_contents($ssoMetadataFilename, $xml);
				if ($written === false) {
					$logger->log('Failed to write SSO metadata to ' . $ssoMetadataFilename . ' for site ' . $configArray['Site']['title'], Logger::LOG_ERROR);
					return new AspenError('Unable to use SSO IdP metadata, cannot create XML file');
				} else {
					chmod($ssoMetadataFilename, 0764);
				}
			} else {
				$logger->log('Failed to retrieve any SSO metadata from ' . $url . ' for site ' . $configArray['Site']['title'], Logger::LOG_ERROR);
				return new AspenError('Unable to use SSO IdP metadata, did not receive any metadata, please check "URL of service metadata XML"');
			}
			return $fileName;
		} else {
			// The URL has been removed
			// We don't remove the metadata file because
			// another site may use it
			return '';
		}
	}

	/** @noinspection PhpUnused */
	function resetDataMappingToDefault() : void {
		$dataMapping = new SSOMapping();
		$ssoSettingsId = $_REQUEST['id'];
		$dataMapping->ssoSettingId = $ssoSettingsId;
		if($dataMapping->find(true)) {
			$dataMapping->clearExistingDataMapping();
			$defaultMapping = SSOMapping::getDefaults($ssoSettingsId);
			$dataMapping->update();
		}
		header('Location: /Admin/SSOSettings?objectAction=edit&id=' . $ssoSettingsId);
		die();
	}

	public function clearExistingDataMapping() : void {
		$this->clearOneToManyOptions('SSOMapping', 'ssoSettingId');
		$this->_dataMapping = [];
	}

	function setDataMappingValues($value) : void {
		$this->_dataMapping = $value;
	}

}