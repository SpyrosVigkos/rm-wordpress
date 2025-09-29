(function () {
  var cfgPromise = null;
  function getConfig() {
    if (window.RM_OKTA) return Promise.resolve(window.RM_OKTA);
    if (!cfgPromise) {
      cfgPromise = fetch("/wp-json/rm/v1/oidc-info", { credentials: "same-origin" })
        .then(function (r) { return r.json(); });
    }
    return cfgPromise;
  }
  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }

  // Delegate submit so it works for popup-injected content
  document.addEventListener("submit", function (e) {
    var form = e.target;
    if (!form || !form.matches || !form.matches('form.rm-okta-login')) return;
    e.preventDefault();
    getConfig().then(function (cfg) {
      if (!window.OktaAuth) { console.error("OktaAuth not loaded"); return; }
      var issuer = cfg.oidc_base_url.replace(/\/$/, "") + "/oauth2/" + (cfg.oidc_issuer || "default");
      var email = qs('input[type="email"]', form);
      var pass  = qs('input[type="password"]', form);
      var error = qs('.rm-okta-error', form);
      var forgot = qs('.rm-okta-forgot', form);
      if (forgot && cfg.oidc_forgot_password_url) forgot.href = cfg.oidc_forgot_password_url;

      if (error) error.textContent = "";
      var client = new OktaAuth({
        issuer: issuer,
        clientId: cfg.oidc_identifier,
        redirectUri: cfg.oidc_redirect_uri,
        scopes: ["openid","email","profile","address","phone","groups","offline_access"],
        pkce: true
      });
      var btn = qs('button[type="submit"]', form); if (btn) btn.disabled = true;
      client.signIn({ username: (email && email.value) || '', password: (pass && pass.value) || '' })
        .then(function (tx) {
          return client.token.getWithRedirect({ sessionToken: tx.sessionToken, responseType: "code" });
        })
        .catch(function (err) {
          if (error) error.textContent = (err && (err.errorSummary || err.message)) || "Login failed";
        })
        .finally(function () { if (btn) btn.disabled = false; });
    });
  }, true);
})();
