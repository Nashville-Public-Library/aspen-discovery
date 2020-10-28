var Globals = (function () {
	//Do setup work here
	return {
		path: '',
		url:  '',
		loggedIn:  false,
		masqueradeMode: false,
		opac:  false, // true prevents browser storage of user viewing settings
		automaticTimeoutLength: 0,
		automaticTimeoutLengthLoggedOut: 0,
		repositoryUrl: '',
		encodedRepositoryUrl: '',
		activeAction: '',
		activeModule: ''
	}
})(Globals || {});