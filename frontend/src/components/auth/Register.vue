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
                        Create your account
                    </h5>
                    <p class="text-muted small mb-0">Join ShopSphere in seconds</p>
                </div>
                <div>
                    <form @submit.prevent="registerNewUser">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name*</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="data.user.name"
                                name="name"
                                id="name"
                                aria-describedby="helpId"
                                placeholder="Name*"
                            />
                        </div>
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
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
                <p class="text-center text-muted small mt-3 mb-0">
                    Already have an account? <router-link to="/login" style="color: var(--brass-dark)">Login</router-link>
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
            name: '',
            email: '',
            password: ''
        }
    })

    //register new user
    const registerNewUser = async () => {
        authStore.clearValidationErrors()
        authStore.isLoading = true
        try {
            const response = await axios.post(`${BASE_URL}/user/register`,
                data.user
            )
            authStore.isLoading = false
            toast.success(response.data.message,{
                timeout: 2000
            })
            router.push('/login')
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
