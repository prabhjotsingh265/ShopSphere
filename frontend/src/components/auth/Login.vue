<template>
    <div class="row my-5">
        <!-- here the spinner -->
        <Spinner :store="authStore" />
        <div class="col-md-5 mx-auto">
            <div class="text-center mb-4">
                <Logo :size="52" />
            </div>
            <!-- render validation errors -->
            <RenderValidationErrors
                :formValidationErrors="authStore.validationErrors"
            />
            <div class="ss-auth-card">
                <div class="text-center mb-4">
                    <h5 class="m-0">
                        Welcome back
                    </h5>
                    <p class="text-muted small mb-0">Sign in to your ShopSphere account</p>
                </div>
                <div>
                    <form @submit.prevent="loginUser">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email*</label>
                            <input
                                type="email"
                                class="form-control"
                                v-model="data.user.email"
                                name="email"
                                id="email"
                                aria-describedby="helpId"
                                placeholder="Email*"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password*</label>
                            <input
                                type="password"
                                class="form-control"
                                v-model="data.user.password"
                                name="password"
                                id="password"
                                aria-describedby="helpId"
                                placeholder="Password*"
                            />
                        </div>
                        <div class="mb-3">
                            <button
                                type="submit"
                                class="btn btn-dark w-100"
                            >
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
                <p class="text-center text-muted small mt-3 mb-0">
                    Don't have an account? <router-link to="/register" style="color: var(--brass-dark)">Register</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import axios from "axios"
    import { onMounted, reactive } from "vue"
    import { useRouter } from "vue-router"
    import { useToast } from "vue-toastification"
    import { BASE_URL } from "../../helpers/config"
    import { useAuthStore } from "../../stores/useAuthStore"
    import Spinner from "../layouts/Spinner.vue"
    import RenderValidationErrors from "../layouts/RenderValidationErrors.vue"
    import Logo from "../layouts/Logo.vue"

    //define the store
    const authStore = useAuthStore()

    //define the router
    const router = useRouter()

    //define the toast
    const toast = useToast()

    //define the data object
    const data = reactive({
        user: {
            email: '',
            password: ''
        }
    })

    //login user
    const loginUser = async () => {
        authStore.clearValidationErrors()
        authStore.isLoading = true
        try {
            const response = await axios.post(`${BASE_URL}/user/login`,
                data.user
            )
            authStore.isLoading = false
            if(response.data.error) {
                toast.error(response.data.error,{
                    timeout: 2000
                })
            }else {
                authStore.setIsLoggedIn()
                authStore.setUser(response.data.user)
                authStore.setToken(response.data.access_token)
                toast.success(response.data.message,{
                    timeout: 2000
                })
                router.push('/')
            }
        } catch (error) {
            authStore.isLoading = false
            if(error?.response?.status === 422) {
                authStore.setValidationErrors(error.response.data.errors)
            }
            console.log(error)
        }
    }

    //once the component is loaded we clear the validation errors
    onMounted(() => authStore.clearValidationErrors())
</script>

<style scoped>
</style>