/* eslint-disable no-useless-escape */
const fs = require("fs");
const path = require("path");
const https = require("https");
const http = require("http");

const OUTPUT_XML = path.join(process.cwd(), "texts-wxr.xml");
const BUTTER_TOKEN = process.env.BUTTER_TOKEN || "";

if (!BUTTER_TOKEN) {
  console.error("❌ Set BUTTER_TOKEN env var to your ButterCMS token");
  process.exit(1);
}

const cdata = (s = "") => `<![CDATA[${s}]]>`;
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

function postmeta(key, value) {
  return [
    "    <wp:postmeta>",
    `      <wp:meta_key>${key}</wp:meta_key>`,
    `      <wp:meta_value>${cdata(String(value))}</wp:meta_value>`,
    "    </wp:postmeta>",
  ].join("\n");
}

function buildItem(t, i) {
  const title = String(t.title || t.content_key || "Text");
  const contentKey = String(t.content_key || "");
  const text = String(t.text || "");
  const butterId = t.meta && t.meta.id ? String(t.meta.id) : "";
  const slug = contentKey
    ? contentKey.toLowerCase().replace(/[^a-z0-9]+/g, "-")
    : title.toLowerCase().replace(/[^a-z0-9]+/g, "-");
  // Use stable past date with seconds offset to avoid importer de-duping on identical timestamps
  const base = new Date("2024-01-01T09:00:00Z");
  const d = new Date(base.getTime() + i * 1000);
  const date = `${d.getUTCFullYear()}-${String(d.getUTCMonth() + 1).padStart(
    2,
    "0"
  )}-${String(d.getUTCDate()).padStart(2, "0")} ${String(
    d.getUTCHours()
  ).padStart(2, "0")}:${String(d.getUTCMinutes()).padStart(2, "0")}:${String(
    d.getUTCSeconds()
  ).padStart(2, "0")}`;

  const metas = [];
  metas.push(postmeta("content_key", contentKey));
  metas.push(postmeta("_content_key", "field_rm_text_content_key"));
  metas.push(postmeta("butter_title", title));
  metas.push(postmeta("_butter_title", "field_rm_text_title"));
  metas.push(postmeta("text", text));
  metas.push(postmeta("_text", "field_rm_text_text"));
  if (butterId) {
    metas.push(postmeta("butter_id", butterId));
    metas.push(postmeta("_butter_id", "field_rm_text_butter_id"));
  }

  return [
    "  <item>",
    `    <title>${cdata(title)}</title>`,
    "    <dc:creator><![CDATA[admin]]></dc:creator>",
    `    <wp:post_date>${date}</wp:post_date>`,
    `    <wp:post_date_gmt>${date}</wp:post_date_gmt>`,
    "    <wp:comment_status><![CDATA[closed]]></wp:comment_status>",
    "    <wp:ping_status><![CDATA[closed]]></wp:ping_status>",
    `    <wp:post_name>${slug}</wp:post_name>`,
    "    <wp:status><![CDATA[publish]]></wp:status>",
    "    <wp:post_parent>0</wp:post_parent>",
    "    <wp:menu_order>0</wp:menu_order>",
    "    <wp:post_type><![CDATA[rm_text]]></wp:post_type>",
    "    <wp:post_password><![CDATA[]]></wp:post_password>",
    "    <wp:is_sticky>0</wp:is_sticky>",
    metas.join("\n"),
    "  </item>",
  ].join("\n");
}

async function main() {
  const url = `https://api.buttercms.com/v2/content/?keys=texts&auth_token=${BUTTER_TOKEN}`;
  console.log("⬇️ Fetching Butter texts…");
  const json = await fetchJson(url);
  const texts = (json.data && json.data.texts) || [];
  console.log(`📋 Texts: ${texts.length}`);

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
    "  <title><![CDATA[Texts Import]]></title>",
    "  <link>https://example.com</link>",
    "  <description><![CDATA[Texts export from ButterCMS]]></description>",
    "  <language>en</language>",
    "  <wp:wxr_version>1.2</wp:wxr_version>",
    "  <wp:base_site_url>https://example.com</wp:base_site_url>",
    "  <wp:base_blog_url>https://example.com</wp:base_blog_url>",
    "",
  ].join("\n");

  const itemsXml = texts.map((t, i) => buildItem(t, i)).join("\n\n");
  const footer = ["", "</channel>", "</rss>", ""].join("\n");

  const xml = [header, itemsXml, footer].join("\n");
  console.log(`💾 Writing XML to: ${OUTPUT_XML}`);
  fs.writeFileSync(OUTPUT_XML, xml, "utf8");
  const fileSize = (fs.statSync(OUTPUT_XML).size / 1024 / 1024).toFixed(2);
  console.log("✅ Texts WXR export complete!");
  console.log(`  - File: ${OUTPUT_XML}`);
  console.log(`  - Texts: ${texts.length}`);
  console.log(`  - Size: ${fileSize} MB`);
  console.log("");
  console.log("📋 Next steps:");
  console.log("  1. WP Admin → Tools → Import → WordPress");
  console.log("  2. Upload texts-wxr.xml");
  console.log("  3. Assign to an author and run import");
  console.log("  4. Verify CPT posts `rm_text` and ACF fields");
}

main().catch((err) => {
  console.error(err.stack || err.message || String(err));
  process.exit(1);
});
