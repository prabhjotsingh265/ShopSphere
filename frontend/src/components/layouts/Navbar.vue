<template>
  <nav class="navbar navbar-expand-lg ss-navbar sticky-top">
    <div class="container-fluid">
      <router-link class="navbar-brand ss-brand" to="/">
        <Logo :size="32" />
        <span>ShopSphere</span>
      </router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item" v-for="category in productsStore.categories" :key="category.id">
            <a class="nav-link" href="#" @click.prevent="shopCategory(category)">
              {{ category.name }}
            </a>
          </li>
        </ul>
        <ul class="navbar-nav align-items-lg-center gap-lg-1">
          <li class="nav-item">
            <router-link class="nav-link ss-icon-link" to="/favorites" title="Favorites">
              <i class="bi bi-heart"></i>
              <span class="ss-icon-label d-lg-none">Favorites</span>
              <span class="ss-badge" v-if="favoritesStore.favorites.length">{{ favoritesStore.favorites.length }}</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link ss-icon-link" to="/cart" title="Cart">
              <i class="bi bi-bag"></i>
              <span class="ss-icon-label d-lg-none">Cart</span>
              <span class="ss-badge" v-if="cartStore.cartItems.length">{{ cartStore.cartItems.length }}</span>
            </router-link>
          </li>
          <li class="nav-item dropdown" v-if="authStore.isLoggedIn">
            <a class="nav-link ss-icon-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img class="ss-avatar" :src="authStore.user?.profile_image" alt="" />
              <span class="ss-icon-label d-lg-none">{{ authStore.user?.name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end ss-dropdown">
              <li>
                <router-link class="dropdown-item" to="/profile">
                    <i class="bi bi-person-check-fill me-2"></i>{{ authStore.user?.name }}
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/user/orders">
                    <i class="bi bi-bag-check-fill me-2"></i>Orders
                </router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="#" @click.prevent="userLogout">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item d-flex gap-2 mt-2 mt-lg-0" v-else>
            <router-link class="btn btn-sm btn-outline-secondary" to="/login">Login</router-link>
            <router-link class="btn btn-sm btn-dark" to="/register">Register</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
  import axios from "axios"
  import { onMounted } from "vue"
  import { useRouter } from "vue-router"
  import { useToast } from "vue-toastification"
  import { BASE_URL, headersConfig } from "../../helpers/config"
  import { useAuthStore } from "../../stores/useAuthStore"
  import { useCartStore } from "../../stores/useCartStore"
  import { useFavoritesStore } from "../../stores/useFavoritesStore"
  import { useProductsStore } from "../../stores/useProductsStore"
  import Logo from "./Logo.vue"

  //define the cart store
  const cartStore = useCartStore()
  const authStore = useAuthStore()
  const favoritesStore = useFavoritesStore()
  const productsStore = useProductsStore()

  //define the toast
  const toast = useToast()

  //define the router
  const router = useRouter()

  //logout function
  const userLogout = async () => {
    try {
      const response = await axios.post(`${BASE_URL}/user/logout`,null,
        headersConfig(authStore.access_token))
        authStore.clearAuthData()
        toast.success(response.data.message,{
          timeout: 2000
        })
        router.push('/login')
    } catch (error) {
      console.log(error)
    }
  }

  //filter products by category from the navbar, navigating home first if needed
  const shopCategory = (category) => {
    if(router.currentRoute.value.path !== '/') {
      router.push('/')
    }
    productsStore.filterProducts('category', category.slug)
  }

  //fetch the currently logged in user
  //and check if the token is still valid
  const fetchCurrentUser = async () => {
    try {
      const response = await axios.get(`${BASE_URL}/user`,
        headersConfig(authStore.access_token))
        authStore.setIsLoggedIn()
        authStore.setUser(response.data.user)
        authStore.setToken(response.data.access_token)
    } catch (error) {
      if(error?.response?.status === 401) {
        authStore.clearAuthData()
      }
      console.log(error)
    }
  }

  //once the component is loaded we get the currently logged in user,
  //and make sure categories are available even on pages other than Home
  //(which is otherwise the only place that fetches them)
  onMounted(() => {
    authStore.isLoggedIn && fetchCurrentUser()
    if(!productsStore.categories.length) {
      productsStore.fetchAllProducts()
    }
  })
</script>

<style scoped>
  .ss-navbar {
    background: #FFFFFF;
    border-bottom: 1px solid var(--line);
    padding-top: .7rem;
    padding-bottom: .7rem;
    z-index: 1030;
  }
  .ss-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--ink) !important;
  }
  .ss-brand span {
    font-family: 'Big Shoulders Display', sans-serif;
    font-weight: 900;
    font-size: 1.3rem;
    letter-spacing: .01em;
  }
  .ss-icon-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.25rem;
    color: var(--ink) !important;
    padding: 6px 10px !important;
    border-radius: var(--radius-s, 6px);
    transition: background-color .15s ease;
  }
  .ss-icon-link:hover { background: var(--bg, #EEF0E8); }
  .ss-icon-link::after { display: none !important; }
  .ss-icon-label { font-size: .95rem; font-weight: 600; }
  .ss-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid var(--line);
  }
  .ss-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: var(--brass, #C9922B);
    color: var(--brass-ink, #5C4108);
    font-family: 'IBM Plex Mono', monospace;
    font-size: .62rem;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    line-height: 1;
  }
  .ss-dropdown {
    border-color: var(--line);
    border-radius: var(--radius-m, 10px);
    box-shadow: 0 12px 28px -8px rgba(20,36,28,.2);
    padding: 8px;
    min-width: 200px;
  }
  .ss-dropdown .dropdown-item {
    border-radius: 6px;
    padding: 8px 12px;
    font-size: .9rem;
  }
  .ss-dropdown .dropdown-item:hover { background: var(--bg, #EEF0E8); }
</style>
