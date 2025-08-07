// Admin Panel JavaScript
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle for mobile
  const sidebarToggle = document.querySelector(".sidebar-toggle")
  const sidebar = document.querySelector(".sidebar")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("show")
    })
  }

  // Auto-hide alerts
  const alerts = document.querySelectorAll(".alert")
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0"
      setTimeout(() => {
        alert.remove()
      }, 300)
    }, 5000)
  })

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll(".btn-delete")
  deleteButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!confirm("Are you sure you want to delete this item?")) {
        e.preventDefault()
      }
    })
  })

  // Image upload preview
  const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]')
  imageInputs.forEach((input) => {
    input.addEventListener("change", (e) => {
      const file = e.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          let preview = input.parentNode.querySelector(".image-preview")
          if (!preview) {
            preview = document.createElement("img")
            preview.className = "image-preview"
            input.parentNode.appendChild(preview)
          }
          preview.src = e.target.result
        }
        reader.readAsDataURL(file)
      }
    })
  })

  // Search functionality
  const searchInputs = document.querySelectorAll(".search-input")
  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase()
      const table = this.closest(".card").querySelector("table tbody")
      const rows = table.querySelectorAll("tr")

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase()
        if (text.includes(searchTerm)) {
          row.style.display = ""
        } else {
          row.style.display = "none"
        }
      })
    })
  })

  // Auto-generate slug from name
  const nameInputs = document.querySelectorAll('input[name="name"]')
  nameInputs.forEach((nameInput) => {
    const slugInput = nameInput.form.querySelector('input[name="slug"]')
    if (slugInput) {
      nameInput.addEventListener("input", function () {
        const slug = this.value
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9-]/g, "-")
          .replace(/-+/g, "-")
          .replace(/^-|-$/g, "")
        slugInput.value = slug
      })
    }
  })

  // AJAX form submission with proper action URL
  const ajaxForms = document.querySelectorAll("form[data-ajax]")
  ajaxForms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault()

      const formData = new FormData(form)
      const submitBtn = form.querySelector('button[type="submit"]')
      const originalText = submitBtn.innerHTML

      // Validate required fields
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.classList.add("is-invalid")
          isValid = false
        } else {
          field.classList.remove("is-invalid")
        }
      })

      if (!isValid) {
        showAlert("Please fill in all required fields", "danger")
        return
      }

      submitBtn.innerHTML = '<span class="loading"></span> Processing...'
      submitBtn.disabled = true

      // Get the correct action URL from form action attribute
      const actionUrl = form.getAttribute("action") || window.location.pathname

      fetch(actionUrl, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
          }
          return response.json()
        })
        .then((data) => {
          if (data.success) {
            showAlert(data.message, "success")
            if (data.redirect) {
              setTimeout(() => {
                window.location.href = data.redirect
              }, 1500)
            } else {
              form.reset()
              const modal = form.closest(".modal")
              if (modal) {
                const bootstrapModal = window.bootstrap?.Modal?.getInstance(modal)
                if (bootstrapModal) {
                  bootstrapModal.hide()
                }
              }
              setTimeout(() => {
                location.reload()
              }, 1500)
            }
          } else {
            showAlert(data.message || "An error occurred", "danger")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          showAlert("An error occurred. Please try again.", "danger")
        })
        .finally(() => {
          submitBtn.innerHTML = originalText
          submitBtn.disabled = false
        })
    })
  })

  // Price formatting
  const priceInputs = document.querySelectorAll('input[name="price"]')
  priceInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      const value = Number.parseFloat(this.value)
      if (!isNaN(value)) {
        this.value = value.toFixed(2)
      }
    })
  })

  // Stock validation
  const stockInputs = document.querySelectorAll('input[name="stock"]')
  stockInputs.forEach((input) => {
    input.addEventListener("input", function () {
      if (this.value < 0) {
        this.value = 0
      }
    })
  })

  // Featured checkbox handling
  const featuredCheckboxes = document.querySelectorAll('input[name="featured"]')
  featuredCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      console.log("Featured status:", this.checked)
    })
  })
})

// Utility functions
function showAlert(message, type = "info") {
  // Remove existing alerts
  const existingAlerts = document.querySelectorAll(".alert")
  existingAlerts.forEach((alert) => alert.remove())

  const alert = document.createElement("div")
  alert.className = `alert alert-${type} alert-dismissible fade show`
  alert.style.position = "fixed"
  alert.style.top = "20px"
  alert.style.right = "20px"
  alert.style.zIndex = "9999"
  alert.style.minWidth = "300px"
  alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

  document.body.appendChild(alert)

  // Auto-hide after 5 seconds
  setTimeout(() => {
    if (alert.parentNode) {
      alert.remove()
    }
  }, 5000)
}

function formatPrice(price) {
  return new Intl.NumberFormat("en-IN", {
    style: "currency",
    currency: "INR",
  }).format(price)
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString("en-IN", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}

// Global error handler
window.addEventListener("error", (e) => {
  console.error("Global error:", e.error)
})

// Global unhandled promise rejection handler
window.addEventListener("unhandledrejection", (e) => {
  console.error("Unhandled promise rejection:", e.reason)
})
