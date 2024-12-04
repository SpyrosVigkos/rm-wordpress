jQuery(document).ready(function($) {
    // Functions are assigned to the window to ensure they are accessible from inline event handlers
    window.handleUsernameChange = function(e) {
        $('#loginError').text('');

    };

    window.handlePasswordChange = function(e) {
        $('#loginError').text('');
    };

    window.handleSubmit = function(e) {
        e.preventDefault(); // Prevent the form from submitting traditionally
        $('#loginButton').prop('disabled', true); // Disable the submit button to prevent multiple submissions

        // Configuration for the Okta authentication
        const authConfig = {
            url: "https://reelmetrics.oktapreview.com",
            clientId: "XQKtyJFC8Z7rFxdzwBLi",
            issuer: "https://reelmetrics.oktapreview.com/oauth2/default",
            authorizeUrl: "https://reelmetrics.oktapreview.com/oauth2/default/v1/authorize",
            userinfoUrl: "https://reelmetrics.oktapreview.com/oauth2/default/v1/userinfo",
            redirectUri: "https://operators-integration.reelmetrics.com/session/oidc_redirect",
            forgotPasswordUrl: "https://operators-integration.reelmetrics.com/password_reset/new",
            pkce: false,
            authParams: {
                responseType: "id_token, token",
                responseMode: "fragment",
                scope: ["openid", "email", "profile", "address", "phone"]
            }
        };

        // Creating an instance of OktaAuth with the config
        const authClient = new OktaAuth(authConfig);

        // Performing the sign-in operation
        authClient.signIn({
            username: $('#username').val(),
            password: $('#password').val()
        }).then(transaction => {
            $('#loginButton').prop('disabled', false); // Re-enable the submit button
            if (transaction.status === 'SUCCESS') {
                // Redirect to handle session tokens if login is successful
                authClient.token.getWithRedirect({
                    sessionToken: transaction.sessionToken,
                    responseType: ["code"],
                    scopes: ["openid", "email", "profile", "address", "phone", "groups", "offline_access"]
                });
            }
        }).catch(err => {
            $('#loginButton').prop('disabled', false); // Re-enable the submit button on error
            $('#loginError').text(err.errorSummary); // Display the error message
            console.error("Login Error:", err.errorSummary);
        });
    };
});
