<template>
    <div class="col-md-4">
        <!-- here the spinner -->
        <Spinner :store="authStore" />
        <!-- render the form validation errors -->
        <RenderValidationErrors :formValidationErrors="authStore.validationErrors" />
        <div class="ss-content-card">
            <div class="d-flex flex-column align-items-center p-4">
                <img
                    :src="authStore.user?.profile_image"
                    :alt="authStore.user?.name"
                    width="130"
                    height="130"
                    class="rounded-circle ss-profile-avatar"
                >
                <div class="w-100 mt-3">
                    <label for="image" class="form-label">Update photo</label>
                    <div class="d-flex gap-2">
                        <input
                            type="file"
                            class="form-control form-control-sm"
                            name="image"
                            id="image"
                            @change="handleFileInputChange"
                            :key="data.fileInputKey"
                        />
                        <button
                            @click="updateUserProfileImage"
                            type="submit"
                            class="btn btn-sm btn-dark"
                        >
                            <i class="bi bi-check2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <i class="bi bi-person text-muted me-2"></i> {{ authStore.user?.name }}
                </li>
                <li class="list-group-item">
                    <i class="bi bi-envelope-at-fill text-muted me-2"></i> {{ authStore.user?.email }}
                </li>
                <li class="list-group-item">
                    <router-link to="/user/orders" class="text-decoration-none" style="color: var(--brass-dark)">
                        <i class="bi bi-bag-check-fill me-2"></i> Orders
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
    import axios from "axios"
    import { onMounted, reactive } from "vue"
    import { useToast } from "vue-toastification"
    import { BASE_URL, headersConfig } from "../../helpers/config"
    import { useAuthStore } from "../../stores/useAuthStore"
    import Spinner from "../layouts/Spinner.vue"
    import RenderValidationErrors from "../layouts/RenderValidationErrors.vue"

    //define the store
    const authStore = useAuthStore()

    //define the toast
    const toast = useToast()

    //define the data
    const data = reactive({
        image: null,
        fileInputKey: 0
    })

    //add the function to handle the file input change
    const handleFileInputChange = (event) => {
        data.image = event.target.files[0]
    }

    //update user profile image function
    const updateUserProfileImage = async () => {
        authStore.clearValidationErrors()
        authStore.isLoading = true

        //send the file
        const formData = new FormData()
        formData.append('profile_image',data.image)
        formData.append('_method','PUT')

        try {
            const response = await axios.post(`${BASE_URL}/user/update/profile`,
                formData,headersConfig(authStore.access_token,'multipart/form-data')
            )
            authStore.user = response.data.user
            authStore.isLoading = false
            toast.success(response.data.message,{
                timeout: 2000
            })
            clearInputFile()
        } catch (error) {
            authStore.isLoading = false
            if(error?.response?.status === 422) {
                authStore.setValidationErrors(error.response.data.errors)
            }
            console.log(error)
        }
    }

    //clear input file
    const clearInputFile = () => {
        data.image = null
        data.fileInputKey++
    }

    //once the component is loaded we clear the validation errors
    onMounted(() => authStore.clearValidationErrors())
</script>

<style scoped>
.ss-profile-avatar {
    object-fit: cover;
    border: 3px solid var(--bg, #EEF0E8);
    box-shadow: 0 0 0 2px var(--brass, #C9922B);
}
</style>
