(function () {
  function getConfig() {
    if (window.RM_OKTA) return Promise.resolve(window.RM_OKTA);
    return fetch("/wp-json/rm/v1/oidc-info", {
      credentials: "same-origin",
    }).then(function (r) {
      return r.json();
    });
  }
  function qs(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }
  function qsa(sel, ctx) {
    return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
  }

  function init(forms, cfg) {
    if (!forms.length) return;
    if (!window.OktaAuth) {
      console.error("OktaAuth not loaded");
      return;
    }
    var issuer =
      cfg.oidc_base_url.replace(/\/$/, "") +
      "/oauth2/" +
      (cfg.oidc_issuer || "default");

    forms.forEach(function (form) {
      var email = qs('input[type="email"]', form);
      var pass = qs('input[type="password"]', form);
      var error = qs(".rm-okta-error", form);
      var forgot = qs(".rm-okta-forgot", form);
      if (forgot && cfg.oidc_forgot_password_url)
        forgot.href = cfg.oidc_forgot_password_url;
      var req = qs(".rm-okta-request", form);

      form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (error) error.textContent = "";
        var client = new OktaAuth({
          issuer: issuer,
          clientId: cfg.oidc_identifier,
          redirectUri: cfg.oidc_redirect_uri,
          scopes: [
            "openid",
            "email",
            "profile",
            "address",
            "phone",
            "groups",
            "offline_access",
          ],
          pkce: true,
        });
        var btn = qs('button[type="submit"]', form);
        if (btn) {
          btn.disabled = true;
        }
        client
          .signIn({ username: email.value, password: pass.value })
          .then(function (tx) {
            return client.token.getWithRedirect({
              sessionToken: tx.sessionToken,
              responseType: "code",
            });
          })
          .catch(function (err) {
            if (error) {
              error.textContent =
                (err && (err.errorSummary || err.message)) || "Login failed";
            }
          })
          .finally(function () {
            if (btn) {
              btn.disabled = false;
            }
          });
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    var forms = qsa("form.rm-okta-login");
    if (!forms.length) return;
    getConfig().then(function (cfg) {
      init(forms, cfg);
    });
  });
})();
