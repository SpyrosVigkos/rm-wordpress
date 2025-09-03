/* eslint-disable no-useless-escape */
const fs = require("fs");
const path = require("path");
const https = require("https");
const http = require("http");

// Config
const OUTPUT_XML = path.join(process.cwd(), "buying-guide-wxr.xml");
const BUTTER_TOKEN = process.env.BUTTER_TOKEN || "";

if (!BUTTER_TOKEN) {
  console.error("❌ Set BUTTER_TOKEN env var to your ButterCMS token");
  process.exit(1);
}

// Utilities
const cdata = (s = "") => `<![CDATA[${s}]]>`;
function toSlug(s) {
  return (s || "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");
}
function fetchJson(url) {
  const lib = url.startsWith("https") ? https : http;
  return new Promise((resolve, reject) => {
    const req = lib.request(url, { method: "GET" }, (res) => {
      let data = "";
      res.on("data", (c) => (data += c));
      res.on("end", () => {
        if (res.statusCode < 200 || res.statusCode >= 300) {
          return reject(new Error(`GET ${url} -> ${res.statusCode}: ${data}`));
        }
        try {
          resolve(JSON.parse(data));
        } catch (e) {
          reject(e);
        }
      });
    });
    req.on("error", reject);
    req.end();
  });
}

// Builders
function termmeta(key, value) {
  return [
    "    <wp:termmeta>",
    `      <wp:meta_key>${key}</wp:meta_key>`,
    `      <wp:meta_value>${cdata(String(value))}</wp:meta_value>`,
    "    </wp:termmeta>",
  ].join("\n");
}

function postmeta(key, value) {
  return [
    "    <wp:postmeta>",
    `      <wp:meta_key>${key}</wp:meta_key>`,
    `      <wp:meta_value>${cdata(String(value))}</wp:meta_value>`,
    "    </wp:postmeta>",
  ].join("\n");
}

function buildTerm(name, index, description, active) {
  const slug = toSlug(name);
  const lines = [
    "  <wp:term>",
    "    <wp:term_id>0</wp:term_id>",
    "    <wp:term_taxonomy>buying_guide_category</wp:term_taxonomy>",
    `    <wp:term_slug>${slug}</wp:term_slug>`,
    "    <wp:term_parent></wp:term_parent>",
    `    <wp:term_name>${cdata(name)}</wp:term_name>`,
    `    <wp:term_description>${cdata(
      description || ""
    )}</wp:term_description>`,
    // ACF term meta: value + _value (field key)
    termmeta("description", description || ""),
    termmeta("_description", "field_rm_bg_cat_description"),
    termmeta("order", String(index || 0)),
    termmeta("_order", "field_rm_bg_cat_order"),
    termmeta("active", active ? "1" : "0"),
    termmeta("_active", "field_rm_bg_cat_active"),
    "  </wp:term>",
  ];
  return lines.join("\n");
}

function buildItem(item, index) {
  const name = String(item.name || "");
  const slug = toSlug(name);
  const status = item.published ? "publish" : "draft";
  const parameters = String(item.parameters || "");
  const catName = String((item.category && item.category.name) || "");
  const catSlug = toSlug(catName);

  const metas = [];
  // ACF post meta value + _value (field key) pattern
  metas.push(postmeta("parameters", parameters));
  metas.push(postmeta("_parameters", "field_rm_bg_parameters"));
  metas.push(postmeta("order", String(index || 0)));
  metas.push(postmeta("_order", "field_rm_bg_order"));
  if (item.meta && item.meta.id) {
    metas.push(postmeta("butter_id", String(item.meta.id)));
    metas.push(postmeta("_butter_id", "field_rm_bg_butter_id"));
  }

  const now = new Date();
  const date = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(
    2,
    "0"
  )}-${String(now.getDate()).padStart(2, "0")} 09:00:00`;

  return [
    "  <item>",
    `    <title>${cdata(name)}</title>`,
    "    <dc:creator><![CDATA[admin]]></dc:creator>",
    `    <wp:post_date>${date}</wp:post_date>`,
    `    <wp:post_date_gmt>${date}</wp:post_date_gmt>`,
    "    <wp:comment_status><![CDATA[closed]]></wp:comment_status>",
    "    <wp:ping_status><![CDATA[closed]]></wp:ping_status>",
    `    <wp:post_name>${slug}</wp:post_name>`,
    `    <wp:status><![CDATA[${status}]]></wp:status>`,
    "    <wp:post_parent>0</wp:post_parent>",
    "    <wp:menu_order>0</wp:menu_order>",
    "    <wp:post_type><![CDATA[buying_guide_item]]></wp:post_type>",
    "    <wp:post_password><![CDATA[]]></wp:post_password>",
    "    <wp:is_sticky>0</wp:is_sticky>",
    `    <category domain="buying_guide_category" nicename="${catSlug}">${cdata(
      catName
    )}</category>`,
    metas.join("\n"),
    "  </item>",
  ].join("\n");
}

async function main() {
  const catsUrl = `https://api.buttercms.com/v2/content/?keys=categories&auth_token=${BUTTER_TOKEN}`;
  const itemsUrl = `https://api.buttercms.com/v2/content/?keys=category_items&auth_token=${BUTTER_TOKEN}`;

  console.log("⬇️ Fetching Butter categories…");
  const catsJson = await fetchJson(catsUrl);
  const categories = (catsJson.data && catsJson.data.categories) || [];

  console.log("⬇️ Fetching Butter category_items…");
  const itemsJson = await fetchJson(itemsUrl);
  const items = (itemsJson.data && itemsJson.data.category_items) || [];

  console.log(`📋 Categories: ${categories.length}, Items: ${items.length}`);

  // Header
  const header = [
    '<?xml version="1.0" encoding="UTF-8" ?>',
    '<rss version="2.0"',
    ' xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"',
    ' xmlns:content="http://purl.org/rss/1.0/modules/content/"',
    ' xmlns:wfw="http://wellformedweb.org/CommentAPI/"',
    ' xmlns:dc="http://purl.org/dc/elements/1.1/"',
    ' xmlns:wp="http://wordpress.org/export/1.2/"',
    ">",
    "<channel>",
    "  <title><![CDATA[Buying Guide Import]]></title>",
    "  <link>https://example.com</link>",
    "  <description><![CDATA[Buying Guide export from ButterCMS]]></description>",
    "  <language>en</language>",
    "  <wp:wxr_version>1.2</wp:wxr_version>",
    "  <wp:base_site_url>https://example.com</wp:base_site_url>",
    "  <wp:base_blog_url>https://example.com</wp:base_blog_url>",
    "",
  ].join("\n");

  // Terms
  console.log("🔄 Building taxonomy terms…");
  const termsXml = categories
    .map((c, i) => buildTerm(c.name, i, c.description || "", !!c.published))
    .join("\n\n");

  // Items
  console.log("🔄 Building items…");
  const itemsXml = items.map((it, i) => buildItem(it, i)).join("\n\n");

  const footer = ["", "</channel>", "</rss>", ""].join("\n");

  const xml = [header, termsXml, "", itemsXml, footer].join("\n");
  console.log(`💾 Writing XML to: ${OUTPUT_XML}`);
  fs.writeFileSync(OUTPUT_XML, xml, "utf8");
  const fileSize = (fs.statSync(OUTPUT_XML).size / 1024 / 1024).toFixed(2);
  console.log("✅ Buying Guide WXR export complete!");
  console.log(`  - File: ${OUTPUT_XML}`);
  console.log(`  - Categories: ${categories.length}`);
  console.log(`  - Items: ${items.length}`);
  console.log(`  - Size: ${fileSize} MB`);
  console.log("");
  console.log("📋 Next steps:");
  console.log("  1. WP Admin → Tools → Import → WordPress");
  console.log("  2. Upload buying-guide-wxr.xml");
  console.log("  3. Assign to an author and run import");
  console.log("  4. Verify CPT posts and taxonomy terms with ACF meta");
}

main().catch((err) => {
  console.error(err.stack || err.message || String(err));
  process.exit(1);
});
