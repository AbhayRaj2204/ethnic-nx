// API Integration
const API_BASE = "api/"

// Global variables
let allCategories = []
let allProducts = []
let currentProductsLoaded = 6
let searchModal = null
let wishlistModal = null // Declare wishlistModal globally

async function fetchCategories() {
  try {
    console.log("Fetching categories from:", `${API_BASE}categories.php`)
    const response = await fetch(`${API_BASE}categories.php`)
    const data = await response.json()
    console.log("Categories response:", data)
    return data.success ? data.data : []
  } catch (error) {
    console.error("Error fetching categories:", error)
    return []
  }
}

async function fetchProducts(params = {}) {
  try {
    const queryString = new URLSearchParams(params).toString()
    const response = await fetch(`${API_BASE}products.php?${queryString}`)
    const data = await response.json()
    return data.success ? data.data : []
  } catch (error) {
    console.error("Error fetching products:", error)
    return []
  }
}

async function fetchProductById(id) {
  try {
    const response = await fetch(`${API_BASE}products.php?action=single&id=${id}`)
    const data = await response.json()
    if (data.success) {
      return data.data
    } else {
      console.error("Product not found:", data.message)
      return null
    }
  } catch (error) {
    console.error("Error fetching product:", error)
    return null
  }
}

async function fetchFeaturedProducts() {
  return await fetchProducts({ action: "featured" })
}

async function fetchProductsByCategory(categorySlug) {
  return await fetchProducts({ action: "by-category", category: categorySlug })
}

// DOM Content Loaded Event
document.addEventListener("DOMContentLoaded", () => {
  // Initialize all components
  initializeLoading()
  initializeNavigation()
  initializeCarousel()
  initializeScrollEffects()
  initializeAnimations()
  initializeProductCards()
  initializeMobileMenu()
  initializeProductDetail()
  initializeContact()
  initializeZoom()
  initializeSearch()
  initializeLoadMore()
  initializeWishlist()
  initializeSorting()
  initializeWishlistModal()

  // New mobile optimizations
  initializeTouchSupport()
  optimizeImagesForMobile()
  improveScrollPerformance()

  // Load dynamic content
  initializePageProducts()
  loadDynamicCategories()
})

// Loading Screen
function initializeLoading() {
  const loadingScreen = document.getElementById("loading-screen")

  if (loadingScreen) {
    // Hide loading screen after 2.5 seconds
    setTimeout(() => {
      loadingScreen.classList.add("hidden")

      // Remove loading screen from DOM after animation completes
      setTimeout(() => {
        loadingScreen.remove()
      }, 500)
    }, 2500)
  }
}

// Load Dynamic Categories
async function loadDynamicCategories() {
  try {
    console.log("Loading dynamic categories...")
    allCategories = await fetchCategories()
    console.log("Loaded categories:", allCategories)

    if (allCategories.length > 0) {
      // Update navigation dropdown
      updateNavigationDropdown()

      // Update home page categories grid
      updateCategoriesGrid()

      // Update products page category filters
      updateCategoryFilters()
    }
  } catch (error) {
    console.error("Error loading dynamic categories:", error)
  }
}

// Update Navigation Dropdown
function updateNavigationDropdown() {
  const dropdownMenus = document.querySelectorAll("#products-dropdown")

  dropdownMenus.forEach((dropdown) => {
    if (dropdown && allCategories.length > 0) {
      dropdown.innerHTML = allCategories
        .map(
          (category) => `<a href="products.php?category=${category.slug}" class="dropdown-item">${category.name}</a>`,
        )
        .join("")
    }
  })
}

// Update Categories Grid (Home Page)
function updateCategoriesGrid() {
  const categoriesGrid = document.getElementById("categories-grid")

  if (categoriesGrid && allCategories.length > 0) {
    categoriesGrid.innerHTML = allCategories
      .map(
        (category) => `
    <div class="category-card" data-category="${category.slug}">
      <div class="category-image">
        <div class="category-overlay">
          <h3 class="category-title">${category.name.toUpperCase()}</h3>
        </div>
      </div>
    </div>
  `,
      )
      .join("")

    // Add click handlers to category cards
    const categoryCards = categoriesGrid.querySelectorAll(".category-card")
    categoryCards.forEach((card) => {
      card.addEventListener("click", () => {
        const category = card.dataset.category
        if (category) {
          window.location.href = `products.php?category=${category}`
        }
      })
    })
  }
}

// Update Category Filters (Products Page)
function updateCategoryFilters() {
  const categoryFilter = document.querySelector(".category-filter")

  if (categoryFilter && allCategories.length > 0) {
    // Keep the "All Products" button and add dynamic category buttons
    const allButton = categoryFilter.querySelector('[data-category="all"]')
    categoryFilter.innerHTML = ""

    // Re-add "All Products" button
    if (allButton) {
      categoryFilter.appendChild(allButton)
    } else {
      const allBtn = document.createElement("button")
      allBtn.className = "category-filter-btn active"
      allBtn.dataset.category = "all"
      allBtn.textContent = "All Products"
      categoryFilter.appendChild(allBtn)
    }

    // Add dynamic category buttons
    allCategories.forEach((category) => {
      const button = document.createElement("button")
      button.className = "category-filter-btn"
      button.dataset.category = category.slug
      button.dataset.categoryId = category.id
      button.textContent = category.name
      categoryFilter.appendChild(button)
    })

    // Initialize category filter functionality
    initializeCategoryFilter()
  }
}

// Navigation
function initializeNavigation() {
  const header = document.querySelector(".header")
  const navLinks = document.querySelectorAll(".nav-link")

  // Header scroll effect
  window.addEventListener(
    "scroll",
    debounce(() => {
      if (window.scrollY > 50) {
        header.classList.add("scrolled")
      } else {
        header.classList.remove("scrolled")
      }
    }, 10),
  )

  // Smooth scrolling for navigation links
  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      const href = link.getAttribute("href")

      // Only prevent default for anchor links
      if (href.startsWith("#")) {
        e.preventDefault()

        const targetSection = document.querySelector(href)

        if (targetSection) {
          const headerHeight = header.offsetHeight
          const targetPosition = targetSection.offsetTop - headerHeight

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          })

          // Update active nav link
          updateActiveNavLink(link)
        }
      }
    })
  })

  // Update active nav link on scroll
  window.addEventListener(
    "scroll",
    debounce(() => {
      updateActiveNavOnScroll()
    }, 100),
  )
}

function updateActiveNavLink(activeLink) {
  const navLinks = document.querySelectorAll(".nav-link")
  navLinks.forEach((link) => link.classList.remove("active"))
  activeLink.classList.add("active")
}

function updateActiveNavOnScroll() {
  const sections = document.querySelectorAll("section[id]")
  const navLinks = document.querySelectorAll(".nav-link")
  const headerHeight = document.querySelector(".header").offsetHeight

  let currentSection = ""

  sections.forEach((section) => {
    const sectionTop = section.offsetTop - headerHeight - 100
    const sectionHeight = section.offsetHeight

    if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
      currentSection = section.getAttribute("id")
    }
  })

  navLinks.forEach((link) => {
    link.classList.remove("active")
    if (link.getAttribute("href") === `#${currentSection}`) {
      link.classList.add("active")
    }
  })
}

// Enhanced Category Filter with Dynamic Loading
function initializeCategoryFilter() {
  const filterBtns = document.querySelectorAll(".category-filter-btn")
  let currentCategory = "all"

  console.log("Initializing category filter with buttons:", filterBtns.length)

  // Add event listeners to all filter buttons
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", async () => {
      const category = btn.dataset.category
      console.log("Category filter clicked:", category)

      // Update active filter button
      filterBtns.forEach((b) => b.classList.remove("active"))
      btn.classList.add("active")

      // Reset current products loaded counter
      currentProductsLoaded = 6
      currentCategory = category

      // Show loading
      showLoadingProducts()

      try {
        let products = []

        if (category === "all") {
          products = allProducts
        } else {
          // Filter products by category slug
          const categoryData = allCategories.find((cat) => cat.slug === category)
          if (categoryData) {
            products = allProducts.filter((product) => product.category_id == categoryData.id)
          }
        }

        console.log("Filtered products:", products)

        // Update product display
        await updateProductDisplay(products)

        // Update page title and breadcrumb
        updatePageTitle(category, btn.textContent)

        // Show/hide load more button based on products count
        const loadMoreSection = document.querySelector(".load-more")
        if (loadMoreSection) {
          if (products.length > 6) {
            loadMoreSection.style.display = "block"
          } else {
            loadMoreSection.style.display = "none"
          }
        }

        // Show notification
        const categoryName = category === "all" ? "All Products" : btn.textContent
        showNotification(`Showing ${categoryName} (${products.length} products)`)
      } catch (error) {
        console.error("Error filtering products:", error)
        showNotification("Error loading products", "error")
        hideLoadingProducts()
      }
    })
  })

  // Handle URL parameters for category filtering
  const urlParams = new URLSearchParams(window.location.search)
  const categoryParam = urlParams.get("category")

  if (categoryParam) {
    console.log("URL category parameter:", categoryParam)
    const targetBtn = document.querySelector(`[data-category="${categoryParam}"]`)
    if (targetBtn) {
      // Wait a bit for everything to load, then trigger the click
      setTimeout(() => {
        targetBtn.click()
      }, 1000)
    } else {
      // If category not found, show all products
      setTimeout(() => {
        const allBtn = document.querySelector(`[data-category="all"]`)
        if (allBtn) allBtn.click()
      }, 1000)
    }
  } else {
    // Load all products by default
    setTimeout(() => {
      const allBtn = document.querySelector(`[data-category="all"]`)
      if (allBtn) allBtn.click()
    }, 1000)
  }
}

function updatePageTitle(category, categoryName) {
  const pageTitle = document.getElementById("page-title")
  const breadcrumbCategory = document.getElementById("breadcrumb-category")

  if (category === "all") {
    if (pageTitle) pageTitle.textContent = "Our Premium Collection"
    if (breadcrumbCategory) breadcrumbCategory.textContent = "Products"
  } else {
    if (pageTitle) pageTitle.textContent = `${categoryName} Collection`
    if (breadcrumbCategory) breadcrumbCategory.textContent = categoryName
  }
}

function showLoadingProducts() {
  const productsContainer = document.querySelector(".products-grid-page")
  const loadingDiv = document.querySelector(".loading-products")
  const noProductsDiv = document.querySelector(".no-products")

  if (productsContainer) {
    productsContainer.innerHTML = ""
  }

  if (loadingDiv) {
    loadingDiv.style.display = "block"
  }

  if (noProductsDiv) {
    noProductsDiv.style.display = "none"
  }
}

function hideLoadingProducts() {
  const loadingDiv = document.querySelector(".loading-products")
  if (loadingDiv) {
    loadingDiv.style.display = "none"
  }
}

async function updateProductDisplay(products) {
  const productsContainer = document.querySelector(".products-grid, .products-grid-page")
  const loadingDiv = document.querySelector(".loading-products")
  const noProductsDiv = document.querySelector(".no-products")

  if (!productsContainer) return

  // Hide loading
  hideLoadingProducts()

  // Clear container
  productsContainer.innerHTML = ""

  if (products.length === 0) {
    // Show no products message
    if (noProductsDiv) {
      noProductsDiv.style.display = "block"
    }
    return
  } else {
    // Hide no products message
    if (noProductsDiv) {
      noProductsDiv.style.display = "none"
    }
  }

  // Show first 6 products initially
  const displayProducts = products.slice(0, 6)

  displayProducts.forEach((product) => {
    const productCard = createProductCard(product)
    productsContainer.appendChild(productCard)
  })

  // Reinitialize product card functionality
  initializeProductCards()

  // Add animation to new products
  const newCards = productsContainer.querySelectorAll(".product-card")
  newCards.forEach((card, index) => {
    card.style.opacity = "0"
    card.style.transform = "translateY(20px)"

    setTimeout(() => {
      card.style.transition = "all 0.3s ease"
      card.style.opacity = "1"
      card.style.transform = "translateY(0)"
    }, index * 100)
  })
}

function createProductCard(product) {
  const card = document.createElement("div")
  card.className = "product-card"
  card.dataset.product = product.id
  card.dataset.category = product.category_slug || ""

  // Get wishlist status
  const wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
  const isWishlisted = wishlistItems.includes(product.id.toString())

  card.innerHTML = `
      <div class="product-image" style="background-image: url('${product.images || "/placeholder.svg?height=350&width=350"}');">
        
          ${product.featured == 1 ? '<div class="product-badge">Featured</div>' : ""}
          ${product.stock == 0 ? '<div class="product-badge sold-out">Sold Out</div>' : ""}
         
      </div>
      <div class="product-info">
          <h3 class="product-title">${product.name}</h3>
          <p class="product-price">₹${Number.parseFloat(product.price).toLocaleString()}</p>
          
          ${
            product.stock == 0
              ? '<p class="stock-status out-of-stock">Out of Stock</p>'
              : product.stock < 5
                ? '<p class="stock-status low-stock">Only ' + product.stock + " left!</p>"
                : ""
          }
          
         
      </div>
  `

  return card
}

// Global function to show all products
window.showAllProducts = () => {
  const allBtn = document.querySelector('[data-category="all"]')
  if (allBtn) {
    allBtn.click()
  }
}

// Sorting functionality
function initializeSorting() {
  const sortSelect = document.querySelector(".sort-select")
  if (sortSelect) {
    sortSelect.addEventListener("change", (e) => {
      const sortValue = e.target.value
      sortProducts(sortValue)
    })
  }
}

async function sortProducts(sortType) {
  const productsContainer = document.querySelector(".products-grid-page")
  if (!productsContainer) return

  const productCards = Array.from(productsContainer.querySelectorAll(".product-card"))
  const sortSelect = document.querySelector(".sort-select")

  productCards.sort((a, b) => {
    const priceA = Number.parseFloat(a.querySelector(".product-price").textContent.replace("₹", "").replace(",", ""))
    const priceB = Number.parseFloat(b.querySelector(".product-price").textContent.replace("₹", "").replace(",", ""))
    const nameA = a.querySelector(".product-title").textContent
    const nameB = b.querySelector(".product-title").textContent

    switch (sortType) {
      case "price-low":
        return priceA - priceB
      case "price-high":
        return priceB - priceA
      case "newest":
        return b.dataset.product - a.dataset.product
      case "popular":
        return Math.random() - 0.5 // Random for demo
      default:
        return 0
    }
  })

  // Clear and re-append sorted cards
  productsContainer.innerHTML = ""
  productCards.forEach((card) => {
    productsContainer.appendChild(card)
  })

  // Reinitialize functionality
  initializeProductCards()

  if (sortSelect) {
    showNotification(`Products sorted by ${sortSelect.options[sortSelect.selectedIndex].text}`)
  }
}

// Carousel
function initializeCarousel() {
  const slides = document.querySelectorAll(".carousel-slide")
  const indicators = document.querySelectorAll(".indicator")
  const prevBtn = document.querySelector(".prev-btn")
  const nextBtn = document.querySelector(".next-btn")

  if (!slides.length) return

  let currentSlide = 0
  const totalSlides = slides.length

  function showSlide(index) {
    // Remove active class from all slides and indicators
    slides.forEach((slide) => slide.classList.remove("active"))
    indicators.forEach((indicator) => indicator.classList.remove("active"))

    // Add active class to current slide and indicator
    slides[index].classList.add("active")
    if (indicators[index]) {
      indicators[index].classList.add("active")
    }
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides
    showSlide(currentSlide)
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides
    showSlide(currentSlide)
  }

  // Event listeners
  if (nextBtn) {
    nextBtn.addEventListener("click", nextSlide)
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", prevSlide)
  }

  // Indicator clicks
  indicators.forEach((indicator, index) => {
    indicator.addEventListener("click", () => {
      currentSlide = index
      showSlide(currentSlide)
    })
  })

  // Auto-play carousel
  setInterval(nextSlide, 10000)

  // Touch/swipe support
  let startX = 0
  let endX = 0

  const carousel = document.querySelector(".carousel-container")
  if (carousel) {
    carousel.addEventListener("touchstart", (e) => {
      startX = e.touches[0].clientX
    })

    carousel.addEventListener("touchend", (e) => {
      endX = e.changedTouches[0].clientX
      handleSwipe()
    })

    function handleSwipe() {
      const swipeThreshold = 50
      const diff = startX - endX

      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          nextSlide()
        } else {
          prevSlide()
        }
      }
    }
  }
}

// Scroll Effects
function initializeScrollEffects() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate-on-scroll", "animated")
      }
    })
  }, observerOptions)

  // Observe elements for scroll animations
  const animateElements = document.querySelectorAll(
    ".product-card, .feature-card, .category-card, .contact-card, .faq-item",
  )
  animateElements.forEach((el) => {
    el.classList.add("animate-on-scroll")
    observer.observe(el)
  })
}

// Animations
function initializeAnimations() {
  // Floating elements animation
  const floatingElements = document.querySelectorAll(".decorative-arch")
  floatingElements.forEach((element, index) => {
    const speed = 0.5 + index * 0.2
    const amplitude = 10 + index * 5

    function animate() {
      const time = Date.now() * 0.001
      const y = Math.sin(time * speed) * amplitude
      const rotation = Math.sin(time * speed * 0.5) * 2

      element.style.transform = `translateY(${y}px) rotate(${rotation}deg)`
      requestAnimationFrame(animate)
    }

    animate()
  })

  // Model cards hover effect
  const modelCards = document.querySelectorAll(".model-card")
  modelCards.forEach((card, index) => {
    card.addEventListener("mouseenter", () => {
      card.style.transform += " scale(1.05)"
      card.style.zIndex = 10
    })

    card.addEventListener("mouseleave", () => {
      // Reset to original transform
      const transforms = [
        "translateX(-120px) translateY(-20px) rotate(-5deg)",
        "translateX(-40px) translateY(10px) rotate(2deg)",
        "translateX(40px) translateY(-10px) rotate(-2deg)",
        "translateX(120px) translateY(20px) rotate(5deg)",
      ]

      card.style.transform = transforms[index % 4]
      card.style.zIndex = 4 - index
    })
  })
}

// Product Cards (Enhanced with proper wishlist functionality)
function initializeProductCards() {
  const productCards = document.querySelectorAll(".product-card")
  const wishlistBtns = document.querySelectorAll(".wishlist-btn")
  const zoomBtns = document.querySelectorAll(".zoom-btn")
  const enquireBtns = document.querySelectorAll(".enquire-btn")

  // Add hover effects and direct navigation to product cards
  productCards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.style.transform = "translateY(-10px) scale(1.02)"
    })

    card.addEventListener("mouseleave", () => {
      card.style.transform = "translateY(0) scale(1)"
    })

    // Click to go to product detail page directly
    card.addEventListener("click", (e) => {
      if (!e.target.closest("button")) {
        const productId = card.dataset.product
        if (productId) {
          window.location.href = `product-detail.php?id=${productId}`
        }
      }
    })
  })

  // Quick view - go directly to product detail
  const quickViewBtns = document.querySelectorAll(".quick-view-btn")
  quickViewBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()
      const productCard = btn.closest(".product-card")
      const productId = productCard.dataset.product
      if (productId) {
        window.location.href = `product-detail.php?id=${productId}`
      }
    })
  })

  // Wishlist functionality
  wishlistBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()
      toggleWishlist(btn)
    })
  })

  // Zoom functionality - go to product detail
  zoomBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()
      const productCard = btn.closest(".product-card")
      const productId = productCard.dataset.product
      if (productId) {
        window.location.href = `product-detail.php?id=${productId}`
      }
    })
  })

  //Enquire functionality
  enquireBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()
      handleWhatsAppEnquiry(btn)
    })
  })
}

// Load products on page initialization
async function initializePageProducts() {
  const isHomePage = window.location.pathname.includes("index.php") || window.location.pathname === "/"
  const isProductsPage = window.location.pathname.includes("products.php")
  const isProductDetailPage = window.location.pathname.includes("product-detail.php")

  // Load all products first
  allProducts = await fetchProducts()
  console.log("Loaded all products:", allProducts.length)

  if (isHomePage) {
    await loadHomePageProducts()
  } else if (isProductsPage) {
    // Products page initialization is handled by category filter
    console.log("Products page detected - category filter will handle loading")
  } else if (isProductDetailPage) {
    await loadProductDetailPage()
  }
}

// Load product detail page
async function loadProductDetailPage() {
  const urlParams = new URLSearchParams(window.location.search)
  const productId = urlParams.get("id")

  console.log("Loading product detail for ID:", productId)

  if (!productId) {
    showNotification("Product ID not found", "error")
    setTimeout(() => {
      window.location.href = "products.php"
    }, 2000)
    return
  }

  try {
    // Show loading state
    showLoadingState()

    const product = await fetchProductById(productId)
    console.log("Fetched product:", product)

    if (!product) {
      showNotification("Product not found", "error")
      setTimeout(() => {
        window.location.href = "products.php"
      }, 2000)
      return
    }

    // Update product details
    updateProductDetailDisplay(product)

    updateProductMetaTags(product)

    // Load related products
    await loadRelatedProducts(product.category_id)

    showNotification("Product loaded successfully", "success")
  } catch (error) {
    console.error("Error loading product detail:", error)
    showNotification("Error loading product details", "error")
    setTimeout(() => {
      window.location.href = "products.php"
    }, 3000)
  }
}

function updateProductMetaTags(product) {
  if (!product) return

  const title = `${product.name} - Premium Ethnic Wear | Ethnic NX`
  const description = `${product.name} - ${product.description || "Premium quality ethnic wear with custom fitting and assured quality."} Price: ₹${Number.parseFloat(product.price).toLocaleString()}`
  const imageUrl = product.images || "/assets/images/logo.png"
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

function showLoadingState() {
  // Update breadcrumb
  const breadcrumbProduct = document.querySelector(".breadcrumb-nav span:last-child")
  if (breadcrumbProduct) {
    breadcrumbProduct.textContent = "Loading..."
  }

  // Update product title
  const productTitle = document.querySelector(".product-title-detail")
  if (productTitle) {
    productTitle.textContent = "Loading Product..."
  }

  // Update product price
  const productPrice = document.querySelector(".product-price-detail")
  if (productPrice) {
    productPrice.textContent = "₹0.00"
  }

  // Show loading in details
  const detailsList = document.querySelector(".product-details-list")
  if (detailsList) {
    detailsList.innerHTML = "<li><strong>Loading product details...</strong></li>"
  }
}

function updateProductDetailDisplay(product) {
  console.log("Updating product display with:", product)

  // Update page title
  document.title = `${product.name} - Ethnic NX`

  // Update breadcrumb
  const breadcrumbProduct = document.querySelector(".breadcrumb-nav span:last-child")
  if (breadcrumbProduct) {
    breadcrumbProduct.textContent = product.name
  }

  // Update main image
  const mainImage = document.getElementById("main-product-image")
  if (mainImage && product.images) {
    mainImage.src = product.images
    mainImage.alt = product.name
  }

  // Update thumbnails (use same image for demo, in real scenario you'd have multiple images)
  const thumbnails = document.querySelectorAll(".thumbnail")
  thumbnails.forEach((thumb, index) => {
    if (product.images) {
      thumb.src = product.images
      thumb.alt = `${product.name} - Image ${index + 1}`
    }
  })

  // Update product title
  const productTitle = document.querySelector(".product-title-detail")
  if (productTitle) {
    productTitle.textContent = product.name
  }

  // Update product price
  const productPrice = document.querySelector(".product-price-detail")
  if (productPrice) {
    productPrice.textContent = `₹${Number.parseFloat(product.price).toLocaleString()}`
  }

  // Update product details
  const detailsList = document.querySelector(".product-details-list")
  if (detailsList) {
    detailsList.innerHTML = `
    <li><strong>Fabric:</strong> ${product.fabric || "Premium Quality"}</li>
    <li><strong>SKU:</strong> ${product.sku || "N/A"}</li>
    <li><strong>Stock:</strong> ${product.stock > 0 ? `${product.stock} pieces available` : "Out of Stock"}</li>
    <li><strong>Occasion:</strong> ${product.occasion || "Wedding, Festival, Party"}</li>
    <li><strong>Care Instructions:</strong> ${product.care_instructions || "Dry Clean Only"}</li>
    ${product.description ? `<li><strong>Description:</strong> ${product.description}</li>` : ""}
    ${product.category_name ? `<li><strong>Category:</strong> ${product.category_name}</li>` : ""}
  `
  }

  // Update sizes if available
  const sizeOptions = document.querySelector(".size-options")
  if (sizeOptions && product.sizes) {
    const sizes = product.sizes
      .split(",")
      .map((s) => s.trim())
      .filter((s) => s)
    if (sizes.length > 0) {
      sizeOptions.innerHTML = sizes
        .map((size, index) => `<button class="size-btn ${index === 1 ? "active" : ""}">${size}</button>`)
        .join("")

      // Reinitialize size selection
      const sizeBtns = sizeOptions.querySelectorAll(".size-btn")
      sizeBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
          sizeBtns.forEach((b) => b.classList.remove("active"))
          btn.classList.add("active")
        })
      })
    }
  }

  // Update wishlist button with product ID
  const wishlistBtn = document.querySelector(".wishlist-detail")
  if (wishlistBtn) {
    wishlistBtn.dataset.productId = product.id

    // Check if already in wishlist
    const wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
    if (wishlistItems.includes(product.id.toString())) {
      wishlistBtn.classList.add("wishlisted")
      wishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Remove from Wishlist'
    } else {
      wishlistBtn.classList.remove("wishlisted")
      wishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Add to Wishlist'
    }
  }
}

async function loadRelatedProducts(categoryId) {
  try {
    const allProducts = await fetchProducts()
    const relatedProducts = allProducts.filter((product) => product.category_id == categoryId).slice(0, 4)

    const relatedGrid = document.querySelector(".related-products-grid")
    if (relatedGrid) {
      if (relatedProducts.length > 0) {
        relatedGrid.innerHTML = relatedProducts
          .map(
            (product) => `
        <div class="product-card" data-product="${product.id}">
          <div class="product-image" style="background-image: url('${product.images}');">
            <div class="product-overlay">
              <button class="quick-view-btn">Quick View</button>
            </div>
            ${product.featured == 1 ? '<div class="product-badge">Featured</div>' : ""}
            ${product.stock == 0 ? '<div class="product-badge sold-out">Sold Out</div>' : ""}
           
          </div>
          <div class="product-info">
            <h3 class="product-title">${product.name}</h3>
            <p class="product-price">₹${Number.parseFloat(product.price).toLocaleString()}</p>
          </div>
        </div>
      `,
          )
          .join("")
      } else {
        relatedGrid.innerHTML = "<p>No related products found.</p>"
      }

      // Reinitialize product cards
      initializeProductCards()
    }
  } catch (error) {
    console.error("Error loading related products:", error)
    const relatedGrid = document.querySelector(".related-products-grid")
    if (relatedGrid) {
      relatedGrid.innerHTML = "<p>Error loading related products.</p>"
    }
  }
}

// Load products for home page (featured products)
async function loadHomePageProducts() {
  try {
    const products = await fetchFeaturedProducts()
    const productsContainer = document.querySelector(".products-grid")
    const productsLoading = document.querySelector(".products-loading")

    if (productsLoading) {
      productsLoading.style.display = "none"
    }

    if (productsContainer && products.length > 0) {
      productsContainer.innerHTML = ""

      // Show first 6 products on home page
      const displayProducts = products.slice(0, 6)

      displayProducts.forEach((product) => {
        const productCard = createProductCard(product)
        productsContainer.appendChild(productCard)
      })

      // Reinitialize product card functionality
      initializeProductCards()
    } else if (productsContainer) {
      productsContainer.innerHTML = "<p>No featured products available.</p>"
    }
  } catch (error) {
    console.error("Error loading home page products:", error)
    const productsContainer = document.querySelector(".products-grid")
    const productsLoading = document.querySelector(".products-loading")

    if (productsLoading) {
      productsLoading.style.display = "none"
    }

    if (productsContainer) {
      productsContainer.innerHTML = "<p>Error loading products.</p>"
    }
  }
}

// Load more products functionality
async function loadMoreProducts() {
  try {
    const loadMoreBtn = document.querySelector(".load-more .btn")
    const originalText = loadMoreBtn.textContent

    loadMoreBtn.textContent = "Loading..."
    loadMoreBtn.disabled = true

    // Get current category filter
    const activeFilter = document.querySelector(".category-filter-btn.active")
    const category = activeFilter ? activeFilter.dataset.category : "all"

    let products
    if (category === "all") {
      products = allProducts
    } else {
      const categoryData = allCategories.find((cat) => cat.slug === category)
      if (categoryData) {
        products = allProducts.filter((product) => product.category_id == categoryData.id)
      }
    }

    // Get next batch of products
    const nextBatch = products.slice(currentProductsLoaded, currentProductsLoaded + 6)

    if (nextBatch.length > 0) {
      const productsContainer = document.querySelector(".products-grid-page, .products-grid")

      nextBatch.forEach((product) => {
        const productCard = createProductCard(product)
        productsContainer.appendChild(productCard)
      })

      currentProductsLoaded += nextBatch.length

      // Reinitialize product card functionality for new cards
      initializeProductCards()

      // Hide load more button if no more products
      if (currentProductsLoaded >= products.length) {
        document.querySelector(".load-more").style.display = "none"
      }
    } else {
      document.querySelector(".load-more").style.display = "none"
    }

    loadMoreBtn.textContent = originalText
    loadMoreBtn.disabled = false
  } catch (error) {
    console.error("Error loading more products:", error)
    const loadMoreBtn = document.querySelector(".load-more .btn")
    loadMoreBtn.textContent = "Load More Products"
    loadMoreBtn.disabled = false
  }
}

// Initialize load more button
function initializeLoadMore() {
  const loadMoreBtn = document.querySelector(".load-more .btn")
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", loadMoreProducts)
  }
}

// Enhanced Wishlist Functionality
function initializeWishlist() {
  // Load wishlist from localStorage
  const wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
  updateWishlistCount()

  // Mark wishlist items as active
  wishlistItems.forEach((productId) => {
    const wishlistBtn = document.querySelector(`[data-product-id="${productId}"]`)
    if (wishlistBtn) {
      wishlistBtn.classList.add("wishlisted")
    }
  })
}

function toggleWishlist(btn) {
  const wishlistIcon = document.querySelector(".wishlist-icon")
  const productId = btn.dataset.productId || btn.closest(".product-card").dataset.product

  let wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
  const isWishlisted = btn.classList.contains("wishlisted")

  if (isWishlisted) {
    // Remove from wishlist
    btn.classList.remove("wishlisted")
    wishlistItems = wishlistItems.filter((id) => id !== productId)
    showNotification("Removed from wishlist", "info")

    // Update button text if it's the detail page button
    if (btn.classList.contains("wishlist-detail")) {
      btn.innerHTML = '<i class="fas fa-heart"></i> Add to Wishlist'
    }
  } else {
    // Add to wishlist
    btn.classList.add("wishlisted")
    if (!wishlistItems.includes(productId)) {
      wishlistItems.push(productId)
    }
    showNotification("Added to wishlist!", "success")

    // Update button text if it's the detail page button
    if (btn.classList.contains("wishlist-detail")) {
      btn.innerHTML = '<i class="fas fa-heart"></i> Remove from Wishlist'
    }
  }

  // Save to localStorage
  localStorage.setItem("wishlistItems", JSON.stringify(wishlistItems))

  // Update wishlist count
  updateWishlistCount()

  // Add animation to wishlist icon
  if (wishlistIcon) {
    wishlistIcon.style.transform = "scale(1.2)"
    setTimeout(() => {
      wishlistIcon.style.transform = "scale(1)"
    }, 200)
  }

  // Refresh wishlist modal if it's open
  if (wishlistModal && wishlistModal.classList.contains("active")) {
    displayWishlistItemsInModal()
  }
}

function updateWishlistCount() {
  const wishlistCount = document.querySelector(".wishlist-count")
  const bottomWishlistBadge = document.querySelector(".bottom-nav .nav-badge")
  const wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
  const count = wishlistItems.length

  if (wishlistCount) {
    wishlistCount.textContent = count
    if (count > 0) {
      wishlistCount.classList.add("show")
    } else {
      wishlistCount.classList.remove("show")
    }
  }

  if (bottomWishlistBadge) {
    bottomWishlistBadge.textContent = count
    if (count > 0) {
      bottomWishlistBadge.classList.add("show")
    } else {
      bottomWishlistBadge.classList.remove("show")
    }
  }
}

// Enhanced Mobile Menu
function initializeMobileMenu() {
  const hamburger = document.querySelector(".hamburger")
  const navMenu = document.querySelector(".nav-menu")
  const navLinks = document.querySelectorAll(".nav-link")
  const dropdownItems = document.querySelectorAll(".nav-item")

  if (!hamburger || !navMenu) return

  // Toggle mobile menu
  hamburger.addEventListener("click", (e) => {
    e.stopPropagation()
    toggleMobileMenu()
  })

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (navMenu.classList.contains("active") && !navMenu.contains(e.target) && !hamburger.contains(e.target)) {
      closeMobileMenu()
    }
  })

  // Handle dropdown toggles on mobile
  dropdownItems.forEach((item) => {
    const link = item.querySelector(".nav-link")
    const dropdown = item.querySelector(".dropdown-menu")

    if (dropdown && link) {
      link.addEventListener("click", (e) => {
        if (window.innerWidth <= 1024) {
          e.preventDefault()

          // Close other dropdowns
          dropdownItems.forEach((otherItem) => {
            if (otherItem !== item) {
              otherItem.classList.remove("dropdown-open")
            }
          })

          // Toggle current dropdown
          item.classList.toggle("dropdown-open")
        }
      })
    }
  })

  // Close menu when clicking on dropdown items
  const dropdownLinks = document.querySelectorAll(".dropdown-item")
  dropdownLinks.forEach((link) => {
    link.addEventListener("click", () => {
      closeMobileMenu()
    })
  })

  // Close menu when clicking on regular nav links (non-dropdown)
  navLinks.forEach((link) => {
    if (!link.closest(".nav-item").querySelector(".dropdown-menu")) {
      link.addEventListener("click", () => {
        closeMobileMenu()
      })
    }
  })

  // Handle window resize
  window.addEventListener("resize", () => {
    if (window.innerWidth > 1024) {
      closeMobileMenu()
      // Reset dropdown states
      dropdownItems.forEach((item) => {
        item.classList.remove("dropdown-open")
      })
    }
  })

  function toggleMobileMenu() {
    const isActive = hamburger.classList.contains("active")

    if (isActive) {
      closeMobileMenu()
    } else {
      openMobileMenu()
    }
  }

  function openMobileMenu() {
    hamburger.classList.add("active")
    navMenu.classList.add("active")
    document.body.style.overflow = "hidden" // Prevent background scrolling

    // Animate hamburger
    animateHamburger(true)
  }

  function closeMobileMenu() {
    hamburger.classList.remove("active")
    navMenu.classList.remove("active")
    document.body.style.overflow = "" // Restore scrolling

    // Close all dropdowns
    dropdownItems.forEach((item) => {
      item.classList.remove("dropdown-open")
    })

    // Animate hamburger
    animateHamburger(false)
  }

  function animateHamburger(isOpen) {
    const spans = hamburger.querySelectorAll("span")
    if (isOpen) {
      spans[0].style.transform = "rotate(45deg) translate(5px, 5px)"
      spans[1].style.opacity = "0"
      spans[2].style.transform = "rotate(-45deg) translate(7px, -6px)"
    } else {
      spans[0].style.transform = "none"
      spans[1].style.opacity = "1"
      spans[2].style.transform = "none"
    }
  }
}

// Touch and Swipe Support
function initializeTouchSupport() {
  let touchStartX = 0
  let touchStartY = 0
  let touchEndX = 0
  let touchEndY = 0

  // Add touch support for product cards
  const productCards = document.querySelectorAll(".product-card")
  productCards.forEach((card) => {
    card.addEventListener("touchstart", handleTouchStart, { passive: true })
    card.addEventListener("touchend", handleTouchEnd, { passive: true })
  })

  // Add touch support for category cards
  const categoryCards = document.querySelectorAll(".category-card")
  categoryCards.forEach((card) => {
    card.addEventListener("touchstart", handleTouchStart, { passive: true })
    card.addEventListener("touchend", handleTouchEnd, { passive: true })
  })

  function handleTouchStart(e) {
    touchStartX = e.touches[0].clientX
    touchStartY = e.touches[0].clientY
  }

  function handleTouchEnd(e) {
    touchEndX = e.changedTouches[0].clientX
    touchEndY = e.changedTouches[0].clientY

    // Check if it's a tap (not a swipe)
    const deltaX = Math.abs(touchEndX - touchStartX)
    const deltaY = Math.abs(touchEndY - touchStartY)

    if (deltaX < 10 && deltaY < 10) {
      // It's a tap, trigger click
      e.target.closest(".product-card, .category-card")?.click()
    }
  }

  // Improve button touch targets
  const buttons = document.querySelectorAll("button, .btn")
  buttons.forEach((button) => {
    button.style.minHeight = "44px" // Minimum touch target size
    button.style.minWidth = "44px"
  })
}

// Optimize images for mobile
function optimizeImagesForMobile() {
  if (window.innerWidth <= 768) {
    const images = document.querySelectorAll("img, [style*='background-image']")
    images.forEach((img) => {
      if (img.tagName === "IMG") {
        img.loading = "lazy"
      }
    })
  }
}

// Improve scroll performance on mobile
function improveScrollPerformance() {
  let ticking = false

  function updateScrollEffects() {
    // Update header scroll effect
    const header = document.querySelector(".header")
    if (header) {
      if (window.scrollY > 50) {
        header.classList.add("scrolled")
      } else {
        header.classList.remove("scrolled")
      }
    }

    // Update scroll animations
    updateActiveNavOnScroll()

    ticking = false
  }

  window.addEventListener(
    "scroll",
    () => {
      if (!ticking) {
        requestAnimationFrame(updateScrollEffects)
        ticking = true
      }
    },
    { passive: true },
  )
}

function initializeImageZoom() {
  const mainImage = document.getElementById("main-product-image")
  if (!mainImage) return

  const imageContainer = mainImage.parentElement
  const zoomResult = document.getElementById("zoom-result")
  const zoomResultImg = document.getElementById("zoom-result-img")
  let zoomLens

  function createZoomLens() {
    if (zoomLens) return

    zoomLens = document.createElement("div")
    zoomLens.className = "zoom-lens"
    imageContainer.appendChild(zoomLens)
  }

  function removeZoomLens() {
    if (zoomLens) {
      zoomLens.remove()
      zoomLens = null
    }
  }

  function getCursorPos(e) {
    const rect = mainImage.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top
    return { x, y }
  }

  function moveLens(e) {
    if (!zoomLens || !zoomResult || !zoomResultImg) return

    const pos = getCursorPos(e)
    const imageRect = mainImage.getBoundingClientRect()
    const lensWidth = zoomLens.offsetWidth
    const lensHeight = zoomLens.offsetHeight

    // Calculate lens position (centered on cursor)
    let x = pos.x
    let y = pos.y

    // Prevent lens from going outside image boundaries
    const halfLensWidth = lensWidth / 2
    const halfLensHeight = lensHeight / 2

    if (x < halfLensWidth) x = halfLensWidth
    if (x > imageRect.width - halfLensWidth) x = imageRect.width - halfLensWidth
    if (y < halfLensHeight) y = halfLensHeight
    if (y > imageRect.height - halfLensHeight) y = imageRect.height - halfLensHeight

    // Set lens position
    zoomLens.style.left = x + "px"
    zoomLens.style.top = y + "px"
    zoomLens.style.display = "block"

    // Calculate zoom ratio based on the zoom result size vs main image size
    const zoomRatioX = zoomResultImg.offsetWidth / mainImage.offsetWidth
    const zoomRatioY = zoomResultImg.offsetHeight / mainImage.offsetHeight

    // Calculate the position for the zoomed image
    const zoomedX = -(x - halfLensWidth) * zoomRatioX
    const zoomedY = -(y - halfLensHeight) * zoomRatioY

    zoomResultImg.style.left = zoomedX + "px"
    zoomResultImg.style.top = zoomedY + "px"
  }

  // Mouse enter - show zoom
  mainImage.addEventListener("mouseenter", (e) => {
    if (window.innerWidth <= 1024) return // Disable on tablets and mobile

    createZoomLens()
    zoomResult.classList.add("active")
    zoomResult.style.display = "block"

    // Initialize lens position
    moveLens(e)
  })

  // Mouse move - update zoom position
  mainImage.addEventListener("mousemove", moveLens)

  // Mouse leave - hide zoom
  mainImage.addEventListener("mouseleave", () => {
    removeZoomLens()
    zoomResult.classList.remove("active")
    setTimeout(() => {
      if (!zoomResult.classList.contains("active")) {
        zoomResult.style.display = "none"
      }
    }, 300)
  })

  // Update zoom image when main image changes
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "attributes" && mutation.attributeName === "src") {
        if (zoomResultImg) {
          zoomResultImg.src = mainImage.src
        }
      }
    })
  })

  observer.observe(mainImage, { attributes: true })
}

// Product Detail Page
function initializeProductDetail() {
  // Tab functionality
  const tabBtns = document.querySelectorAll(".tab-btn")
  const tabPanes = document.querySelectorAll(".tab-pane")

  tabBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetTab = btn.dataset.tab

      // Remove active class from all tabs and panes
      tabBtns.forEach((b) => b.classList.remove("active"))
      tabPanes.forEach((p) => p.classList.remove("active"))

      // Add active class to clicked tab and corresponding pane
      btn.classList.add("active")
      const targetPane = document.getElementById(targetTab)
      if (targetPane) {
        targetPane.classList.add("active")
      }
    })
  })

  // Size selection
  const sizeBtns = document.querySelectorAll(".size-btn")
  sizeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      sizeBtns.forEach((b) => b.classList.remove("active"))
      btn.classList.add("active")
    })
  })

  // Quantity controls
  const qtyInput = document.querySelector(".qty-input")
  const minusBtn = document.querySelector(".qty-btn.minus")
  const plusBtn = document.querySelector(".qty-btn.plus")

  if (minusBtn && qtyInput) {
    minusBtn.addEventListener("click", () => {
      const currentValue = Number.parseInt(qtyInput.value)
      if (currentValue > 1) {
        qtyInput.value = currentValue - 1
      }
    })
  }

  if (plusBtn && qtyInput) {
    plusBtn.addEventListener("click", () => {
      const currentValue = Number.parseInt(qtyInput.value)
      qtyInput.value = currentValue + 1
    })
  }

  // Thumbnail images
  const thumbnails = document.querySelectorAll(".thumbnail")
  const mainImage = document.getElementById("main-product-image")

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      thumbnails.forEach((t) => t.classList.remove("active"))
      thumb.classList.add("active")

      if (mainImage) {
        mainImage.src = thumb.src
      }
    })
  })

  // Wishlist button on product detail page
  const wishlistDetailBtn = document.querySelector(".wishlist-detail")
  if (wishlistDetailBtn) {
    wishlistDetailBtn.addEventListener("click", () => {
      toggleWishlist(wishlistDetailBtn)
    })
  }

  initializeImageZoom()
}

// Contact Page
function initializeContact() {
  // Contact form
  const contactForm = document.querySelector(".contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Get form data
      const formData = new FormData(contactForm)
      const data = Object.fromEntries(formData)

      // Show loading state
      const submitBtn = contactForm.querySelector('button[type="submit"]')
      const originalText = submitBtn.textContent
      submitBtn.textContent = "Sending..."
      submitBtn.disabled = true

      // Simulate form submission
      setTimeout(() => {
        showNotification("Message sent successfully! We'll get back to you soon.", "success")
        contactForm.reset()

        submitBtn.textContent = originalText
        submitBtn.disabled = false
      }, 2000)
    })
  }

  // FAQ functionality
  const faqItems = document.querySelectorAll(".faq-item")
  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question")
    question.addEventListener("click", () => {
      const isActive = item.classList.contains("active")

      // Close all FAQ items
      faqItems.forEach((faq) => faq.classList.remove("active"))

      // Open clicked item if it wasn't active
      if (!isActive) {
        item.classList.add("active")
      }
    })
  })
}

// Zoom Modal
function initializeZoom() {
  const zoomModal = document.getElementById("zoom-modal")
  const zoomImage = document.getElementById("zoom-image")
  const zoomClose = document.querySelector(".zoom-close")

  if (!zoomModal) return

  // Close modal
  const closeZoom = () => {
    zoomModal.style.display = "none"
  }

  if (zoomClose) {
    zoomClose.addEventListener("click", closeZoom)
  }

  zoomModal.addEventListener("click", (e) => {
    if (e.target === zoomModal) {
      closeZoom()
    }
  })

  // Global zoom function
  window.openZoomModal = function openZoomModal(btn) {
    const productCard = btn.closest(".product-card")
    const productImage = productCard.querySelector(".product-image")

    // For demo, use placeholder image
    const imageSrc = "/placeholder.svg?height=600&width=500"

    if (zoomImage) {
      zoomImage.src = imageSrc
    }

    zoomModal.style.display = "block"
  }

  // Zoom functionality for product detail page
  const zoomBtnDetail = document.querySelector(".zoom-btn-detail")
  if (zoomBtnDetail) {
    zoomBtnDetail.addEventListener("click", () => {
      const mainImage = document.getElementById("main-product-image")
      if (mainImage && zoomImage) {
        zoomImage.src = mainImage.src
        zoomModal.style.display = "block"
      }
    })
  }
}

// Enhanced Search Functionality
function initializeSearch() {
  const searchIcon = document.querySelector(".search-icon")
  const bottomSearchItem = document.querySelector(".bottom-nav-item:last-child")

  // Create search modal
  searchModal = createSearchModal()
  document.body.appendChild(searchModal)

  // Search icon click
  if (searchIcon) {
    searchIcon.addEventListener("click", () => {
      openSearchModal()
    })
  }

  // Bottom nav search click
  if (bottomSearchItem) {
    bottomSearchItem.addEventListener("click", () => {
      openSearchModal()
    })
  }

  function createSearchModal() {
    const modal = document.createElement("div")
    modal.className = "search-modal"
    modal.innerHTML = `
    <div class="search-modal-content">
      <button class="search-close">&times;</button>
      <input type="text" class="search-input" placeholder="Search for products...">
      <div class="search-results"></div>
    </div>
  `

    // Close modal functionality
    const closeBtn = modal.querySelector(".search-close")
    closeBtn.addEventListener("click", closeSearchModal)

    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeSearchModal()
      }
    })

    // Search input functionality
    const searchInput = modal.querySelector(".search-input")
    searchInput.addEventListener("input", debounce(performSearch, 300))

    return modal
  }

  function openSearchModal() {
    searchModal.classList.add("active")
    const searchInput = searchModal.querySelector(".search-input")
    searchInput.focus()
  }

  function closeSearchModal() {
    searchModal.classList.remove("active")
    const searchInput = searchModal.querySelector(".search-input")
    const searchResults = searchModal.querySelector(".search-results")
    searchInput.value = ""
    searchResults.innerHTML = ""
  }

  async function performSearch(e) {
    const searchTerm = e.target.value.trim()
    const searchResults = searchModal.querySelector(".search-results")

    if (searchTerm.length < 2) {
      searchResults.innerHTML = ""
      return
    }

    try {
      // Filter products by search term
      const filteredProducts = allProducts.filter(
        (product) =>
          product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
          product.category_name?.toLowerCase().includes(searchTerm.toLowerCase()),
      )

      displaySearchResults(filteredProducts, searchTerm)
    } catch (error) {
      console.error("Search error:", error)
      searchResults.innerHTML = "<p>Error performing search. Please try again.</p>"
    }
  }

  function displaySearchResults(products, searchTerm) {
    const searchResults = searchModal.querySelector(".search-results")

    if (products.length === 0) {
      searchResults.innerHTML = `<p>No products found for "${searchTerm}"</p>`
      return
    }

    searchResults.innerHTML = products
      .slice(0, 8)
      .map(
        (product) => `
    <div class="search-result-item" data-product-id="${product.id}">
      <div class="search-result-image" style="background-image: url('${product.images}')"></div>
      <div class="search-result-info">
        <h4>${product.name}</h4>
        <p>₹${Number.parseFloat(product.price).toLocaleString()}</p>
      </div>
    </div>
  `,
      )
      .join("")

    // Add click handlers to search results
    searchResults.querySelectorAll(".search-result-item").forEach((item) => {
      item.addEventListener("click", () => {
        const productId = item.dataset.productId
        closeSearchModal()
        window.location.href = `product-detail.php?id=${productId}`
      })
    })
  }
}

// Product Detail Navigation
function openProductDetail(productId) {
  window.location.href = `product-detail.php?id=${productId}`
}

// Enhanced Notification System
function showNotification(message, type = "success") {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll(".notification")
  existingNotifications.forEach((notification) => notification.remove())

  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  notification.classList.add("show")
  document.body.appendChild(notification)

  // Auto hide after 3 seconds
  setTimeout(() => {
    notification.classList.remove("show")
    setTimeout(() => {
      if (document.body.contains(notification)) {
        notification.remove()
      }
    }, 300)
  }, 3000)
}

// Utility Functions
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Make functions globally available
window.showNotification = showNotification
window.toggleWishlist = toggleWishlist
window.openProductDetail = openProductDetail

// Wishlist Modal Functionality
function initializeWishlistModal() {
  wishlistModal = createWishlistModalHtml()
  document.body.appendChild(wishlistModal)

  const wishlistIconHeader = document.querySelector(".wishlist-icon")
  const wishlistIconBottomNav = document.querySelector(".bottom-nav-item.wishlist-bottom-nav") // Use the specific class

  if (wishlistIconHeader) {
    wishlistIconHeader.addEventListener("click", openWishlistModal)
  }
  if (wishlistIconBottomNav) {
    wishlistIconBottomNav.addEventListener("click", openWishlistModal)
  }

  const closeBtn = wishlistModal.querySelector(".wishlist-modal-close")
  if (closeBtn) {
    closeBtn.addEventListener("click", closeWishlistModal)
  }

  wishlistModal.addEventListener("click", (e) => {
    if (e.target === wishlistModal) {
      closeWishlistModal()
    }
  })
}

function createWishlistModalHtml() {
  const modal = document.createElement("div")
  modal.id = "wishlist-modal"
  modal.className = "wishlist-modal"
  modal.innerHTML = `
  <div class="wishlist-modal-content">
    <span class="wishlist-modal-close">&times;</span>
    <h2>Your Wishlist</h2>
    <div class="wishlist-items-grid">
      <!-- Wishlist items will be loaded here -->
    </div>
    <p class="empty-wishlist-message" style="display: none;">Your wishlist is empty.</p>
  </div>
`
  return modal
}

function openWishlistModal() {
  if (wishlistModal) {
    wishlistModal.classList.add("active")
    displayWishlistItemsInModal()
  }
}

function closeWishlistModal() {
  if (wishlistModal) {
    wishlistModal.classList.remove("active")
  }
}

async function displayWishlistItemsInModal() {
  const wishlistGrid = wishlistModal.querySelector(".wishlist-items-grid")
  const emptyMessage = wishlistModal.querySelector(".empty-wishlist-message")
  wishlistGrid.innerHTML = "" // Clear previous items

  const wishlistItems = JSON.parse(localStorage.getItem("wishlistItems") || "[]")

  if (wishlistItems.length === 0) {
    emptyMessage.style.display = "block"
    return
  } else {
    emptyMessage.style.display = "none"
  }

  for (const productId of wishlistItems) {
    const product = await fetchProductById(productId)
    if (product) {
      const wishlistItemCard = createWishlistItemCard(product)
      wishlistGrid.appendChild(wishlistItemCard)
    }
  }

  // Add event listeners for remove buttons in the modal
  wishlistGrid.querySelectorAll(".remove-from-wishlist-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const productIdToRemove = e.target.dataset.productId
      const productCardInModal = e.target.closest(".wishlist-item-card")
      if (productCardInModal) {
        productCardInModal.remove() // Remove from modal display
      }
      // Update localStorage and count
      let currentWishlist = JSON.parse(localStorage.getItem("wishlistItems") || "[]")
      currentWishlist = currentWishlist.filter((id) => id !== productIdToRemove)
      localStorage.setItem("wishlistItems", JSON.stringify(currentWishlist))
      updateWishlistCount()
      showNotification("Removed from wishlist", "info")
      displayWishlistItemsInModal() // Re-render to show empty message if needed
    })
  })
}

function createWishlistItemCard(product) {
  const card = document.createElement("div")
  card.className = "wishlist-item-card"
  card.dataset.productId = product.id

  card.innerHTML = `
  <div class="product-image" style="background-image: url('${product.images || "/placeholder.svg?height=180&width=180"}');"></div>
  <div class="product-info">
    <h3 class="product-title">${product.name}</h3>
    <p class="product-price">₹${Number.parseFloat(product.price).toLocaleString()}</p>
    <button class="remove-from-wishlist-btn" data-product-id="${product.id}">Remove</button>
  </div>
`
  return card
}

function handleWhatsAppEnquiry(btn) {
  const productCard = btn.closest(".product-card")
  const productId = productCard ? productCard.dataset.product : btn.dataset.productId
  const productTitle = productCard
    ? productCard.querySelector(".product-title")
    : document.querySelector(".product-title-detail")
  const productImage = productCard
    ? productCard.querySelector(".product-image")
    : document.getElementById("main-product-image")
  const activeSizeBtn = document.querySelector(".size-btn.active")

  const productName = productTitle ? productTitle.textContent : "Product"
  const productImageSrc = productImage
    ? productImage.src || getComputedStyle(productImage).backgroundImage.slice(5, -2)
    : ""
  const selectedSize = activeSizeBtn ? activeSizeBtn.textContent.trim() : "M"
  const currentPageUrl = window.location.href

  // Create WhatsApp message with enhanced details
  let message = `Hi! I'm interested in this product:\n\n`
  message += `Product: ${productName}\n`
  message += `Size: ${selectedSize}\n`
  message += `Page URL: ${currentPageUrl}\n`

  if (productImageSrc && !productImageSrc.includes("placeholder")) {
    // Convert relative URLs to absolute URLs
    const absoluteImageUrl = productImageSrc.startsWith("http")
      ? productImageSrc
      : window.location.origin + productImageSrc
    message += `Image: ${absoluteImageUrl}\n`
  }

  message += `\nPlease provide more details and pricing information.`

  // Encode message for URL
  const encodedMessage = encodeURIComponent(message)

  // WhatsApp URL - using the number from the existing code
  const whatsappUrl = `https://wa.me/919709226079?text=${encodedMessage}`

  // Open WhatsApp
  window.open(whatsappUrl, "_blank")
}

window.handleWhatsAppEnquiry = handleWhatsAppEnquiry
