function updateProductMetaTags(productData) {
  if (!productData) return

  const title = `${productData.name} - Premium Ethnic Wear | Ethnic NX`
  const description = `${productData.name} - ${productData.description || "Premium quality ethnic wear with custom fitting and assured quality."}`
  const imageUrl = productData.image || "/ethnic-nx/assets/images/logo.png"
  const currentUrl = window.location.href

  // Update page title
  document.title = title

  // Update or create meta tags
  updateMetaTag("description", description)
  updateMetaTag("og:title", title, "property")
  updateMetaTag("og:description", description, "property")
  updateMetaTag("og:image", imageUrl, "property")
  updateMetaTag("og:url", currentUrl, "property")
  updateMetaTag("twitter:title", title)
  updateMetaTag("twitter:description", description)
  updateMetaTag("twitter:image", imageUrl)
}

function updateMetaTag(name, content, attribute = "name") {
  let meta = document.querySelector(`meta[${attribute}="${name}"]`)

  if (meta) {
    meta.setAttribute("content", content)
  } else {
    meta = document.createElement("meta")
    meta.setAttribute(attribute, name)
    meta.setAttribute("content", content)
    document.head.appendChild(meta)
  }
}

// Function to be called when product data is loaded
window.updateProductMeta = updateProductMetaTags
